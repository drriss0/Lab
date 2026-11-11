<?php

namespace App\Controllers;

use App\Models\BarangModel;
use App\Models\DistribusiModel;
use App\Models\PeminjamanModel;
use App\Models\PelaporanModel;

class Barang extends BaseController
{
    protected BarangModel     $barangModel;
    protected DistribusiModel $distribusiModel;
    protected PeminjamanModel $peminjamanModel;
    protected PelaporanModel  $pelaporanModel;

    public function __construct()
    {
        $this->barangModel     = new BarangModel();
        $this->distribusiModel = new DistribusiModel();
        $this->peminjamanModel = new PeminjamanModel();
        $this->pelaporanModel  = new PelaporanModel();
    }


    // ============================================================
    // GENERATE KODE BARANG
    // ============================================================
    public function generateKode()
    {
        // Fix: checkAuth() mengembalikan null jika sudah login, RedirectResponse jika belum
        if ($redirect = $this->checkAuth()) return $this->response->setJSON(['error' => 'Unauthorized'], 401);

        $rawPrefix = $this->request->getPost('prefix') ?? $this->request->getGet('prefix');
        $prefix    = strtoupper(trim((string)$rawPrefix));
        if (!$prefix) {
            return $this->response->setJSON(['kode' => '']);
        }

        $db = \Config\Database::connect();

        // Ambil semua nomor yang sudah terpakai untuk prefix ini
        $rows = $db->query(
            "SELECT kode_barang FROM barang WHERE kode_barang REGEXP ?",
            ['^' . preg_quote($prefix, '/') . '-[0-9]+$']
        )->getResultArray();

        $usedNumbers = [];
        foreach ($rows as $row) {
            $parts = explode('-', $row['kode_barang'], 2);
            if (count($parts) === 2 && ctype_digit($parts[1])) {
                $usedNumbers[] = (int)$parts[1];
            }
        }

        // Nomor berikutnya = max yang sudah ada + 1, atau 1 jika belum ada
        $nextNumber = !empty($usedNumbers) ? max($usedNumbers) + 1 : 1;

        $newKode = $prefix . '-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
        return $this->response->setJSON(['kode' => $newKode]);
    }

    // ============================================================
    // SIMPAN — Tambah Barang Baru
    // ============================================================

    public function simpan(): \CodeIgniter\HTTP\RedirectResponse
    {
        if ($redirect = $this->checkAuth()) return $redirect;
        if ($redirect = deny_if_not('admin')) return $redirect;

        $rules = [
            'nama_barang' => 'required|min_length[2]',
            'kode_barang' => [
                'rules'  => 'required|is_unique[barang.kode_barang]',
                'errors' => [
                    'required'  => 'Kode barang wajib diisi.',
                    'is_unique' => 'Gagal: Kode barang yang Anda masukkan sudah terdaftar di sistem. Harap gunakan kode yang berbeda agar tidak terjadi duplikasi aset.'
                ]
            ],
            'stok_awal'   => 'required|is_natural',
            'spesifikasi' => 'permit_empty|max_length[5000]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('errors', $this->validator->getErrors());
        }


        $stokAwal = (int) $this->request->getPost('stok_awal');

        // Handle foto upload
        $fotoNama = null;
        $foto = $this->request->getFile('foto_barang');
        if ($foto && $foto->isValid() && ! $foto->hasMoved()) {
            // Validasi MIME type (server-side, tidak bergantung ekstensi saja)
            $allowedMime = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
            $allowedExt  = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
            if (! in_array($foto->getMimeType(), $allowedMime)
                || ! in_array(strtolower($foto->getExtension()), $allowedExt)) {
                return redirect()->back()->withInput()
                    ->with('error', 'Format foto tidak didukung. Gunakan JPG, PNG, WEBP, atau GIF.');
            }
            // Validasi ukuran file: maks 2 MB
            if ($foto->getSize() > 2 * 1024 * 1024) {
                return redirect()->back()->withInput()
                    ->with('error', 'Ukuran foto melebihi batas maksimal 2 MB.');
            }
            $fotoNama = $foto->getRandomName();
            $foto->move(FCPATH . 'uploads', $fotoNama);
        }

        $this->barangModel->insert([
            'kode_barang'      => strtoupper($this->request->getPost('kode_barang')),
            'nama_barang'      => $this->request->getPost('nama_barang'),
            'kategori'         => $this->request->getPost('kategori'),
            'id_kategori'      => $this->request->getPost('id_kategori') ?: null,
            'merk'             => $this->request->getPost('merk'),
            'spesifikasi'      => trim((string) $this->request->getPost('spesifikasi')) ?: null,
            'stok_awal'        => $stokAwal,
            'stok_gudang'      => $stokAwal,
            'jumlah_digunakan' => 0,
            'jumlah_dipinjam'  => 0,
            'jumlah_rusak'     => 0,
            'tanggal_perolehan' => $this->request->getPost('tanggal_perolehan') ?: null,
            'foto_barang'      => $fotoNama,
        ]);

        return redirect()->to(base_url('dashboard'))
            ->with('success', 'Barang berhasil ditambahkan ke inventaris!');
    }

    // ============================================================
    // DETAIL — Informasi Lengkap Barang
    // ============================================================
    public function detail(string $hash): string|\CodeIgniter\HTTP\RedirectResponse
    {
        if ($redirect = $this->checkAuth()) return $redirect;
        $id = $this->decodeId($hash);
        if (! $id) return redirect()->to(base_url('dashboard'))->with('error', 'Link tidak valid.');

        $barang = $this->barangModel->getDetail($id);
        if (! $barang) {
            return redirect()->to(base_url('dashboard'))
                ->with('error', 'Data barang tidak ditemukan.');
        }

        return view('barang/detail', [
            'title'       => 'Detail — ' . $barang['nama_barang'],
            'active_menu' => 'dashboard',
            'barang'      => $barang,
            'distribusi'  => $this->distribusiModel->getLokasiTerbanyak($id),
            'aktivitas'   => $this->getAktivitasTerakhir($id),
            'kategoriList'=> $this->barangModel->getKategori(),
        ]);
    }

    // ============================================================
    // DISTRIBUSI — Daftar & Form Distribusi Barang
    // ============================================================
    public function distribusi(string $hash): string|\CodeIgniter\HTTP\RedirectResponse
    {
        if ($redirect = $this->checkAuth()) return $redirect;
        $id = $this->decodeId($hash);
        if (! $id) return redirect()->to(base_url('dashboard'))->with('error', 'Link tidak valid.');

        $barang = $this->barangModel->getDetail($id);
        if (! $barang) {
            return redirect()->to(base_url('dashboard'))
                ->with('error', 'Data barang tidak ditemukan.');
        }

        $filters = [
            'q'             => $this->request->getGet('q'),
            'tanggal_mulai' => $this->request->getGet('tanggal_mulai'),
            'tanggal_akhir' => $this->request->getGet('tanggal_akhir'),
            'status'        => $this->request->getGet('status'),
            'ruangan'       => $this->request->getGet('ruangan'),
        ];

        $db = \Config\Database::connect();
        $pjListQuery = $db->table('distribusi')
            ->select('penanggung_jawab')
            ->where('id_barang', $id)
            ->where('tujuan_distribusi', 'peminjaman')
            ->where('penanggung_jawab IS NOT NULL', null, false)
            ->where('penanggung_jawab !=', '')
            ->orderBy('id', 'DESC')
            ->limit(1)
            ->get()->getRowArray();
        $existingPj = $pjListQuery ? $pjListQuery['penanggung_jawab'] : null;

        return view('barang/distribusi', [
            'title'        => 'Distribusi — ' . $barang['nama_barang'],
            'active_menu'  => 'dashboard',
            'barang'       => $barang,
            'distribusi'   => $this->distribusiModel->getByBarang($id, $filters),
            'filters'      => $filters,
            'ruangan'      => $this->distribusiModel->getRuangan(),
            'total_digunakan' => $this->distribusiModel->getTotalDigunakan($id),
            'existing_pj_peminjaman' => $existingPj,
        ]);
    }

    public function simpanDistribusi(string $hash): \CodeIgniter\HTTP\RedirectResponse
    {
        if ($redirect = $this->checkAuth()) return $redirect;
        if ($redirect = deny_if_not('admin')) return $redirect;
        $id = $this->decodeId($hash);
        if (! $id) return redirect()->to(base_url('dashboard'))->with('error', 'Link tidak valid.');

        $barang = $this->barangModel->getDetail($id);
        if (! $barang) return redirect()->to(base_url('dashboard'));

        $jumlah          = (int) $this->request->getPost('jumlah');
        $tujuan          = $this->request->getPost('tujuan_distribusi') ?? 'ruangan';

        if ($jumlah <= 0) {
            return redirect()->back()->with('error', 'Jumlah harus lebih dari 0.');
        }

        if ($jumlah > (int)$barang['stok_gudang']) {
            return redirect()->back()
                ->with('error', 'Stok gudang tidak mencukupi. Stok tersedia: ' . $barang['stok_gudang']);
        }

        // Jika tujuan ruangan, wajib ada id_ruangan
        $idRuangan = null;
        if ($tujuan === 'ruangan') {
            $idRuangan = $this->request->getPost('id_ruangan');
            if (! $idRuangan) {
                return redirect()->back()->with('error', 'Lokasi ruangan wajib dipilih untuk distribusi ke ruangan.');
            }

            // Validasi Kapasitas Ruangan untuk Meja & Kursi
            $jenisBarang = strtolower($barang['id_kategori'] ?? $barang['jenis_barang'] ?? '');
            if (in_array($jenisBarang, ['meja', 'kursi'])) {
                $db = \Config\Database::connect();
                $ruangan = $db->table('ruangan')->where('id', $idRuangan)->get()->getRowArray();
                
                if ($ruangan) {
                    $kapasitas = (int)($ruangan['kapasitas_' . $jenisBarang] ?? 0);
                    
                    // Hitung total meja/kursi yang sedang digunakan di ruangan ini
                    $totalExisting = $db->table('distribusi d')
                        ->join('barang b', 'b.id = d.id_barang')
                        ->where('d.id_ruangan', $idRuangan)
                        ->where('d.status', 'digunakan')
                        ->where('b.id_kategori', $barang['id_kategori'])
                        ->selectSum('d.jumlah')
                        ->get()->getRowArray();
                    
                    $totalTerisi = (int)($totalExisting['jumlah'] ?? 0);
                    
                    if (($totalTerisi + $jumlah) > $kapasitas) {
                        return redirect()->back()->with('error', 'Gagal: Kapasitas ruangan tidak mencukupi untuk jumlah barang yang ingin ditambahkan.');
                    }
                }
            }
        }

        $keterangan = $this->request->getPost('keterangan');
        if (!is_valid_keterangan($keterangan)) {
            return redirect()->back()->withInput()->with('error', 'Keterangan tidak valid. Jika diisi, wajib mengandung minimal 3 karakter alfabet.');
        }

        // Generate nomor unit otomatis (mengisi kekosongan terlebih dahulu)
        $db = \Config\Database::connect();
        
        $builder = $db->table('distribusi')
                      ->select('no_unit')
                      ->where('id_barang', $id)
                      ->whereNotIn('status', ['ditarik', 'digudang', 'pelaporan_rusak'])
                      ->where('no_unit >', 0);
                      
        if ($tujuan === 'ruangan') {
            $builder->where('id_ruangan', $idRuangan);
        } else {
            $builder->where('tujuan_distribusi', 'peminjaman');
        }
        
        $existingUnits = array_column($builder->get()->getResultArray(), 'no_unit');
        
        // Tambahan: Jangan isi kekosongan jika barang tersebut sedang rusak dan masih ada di pelaporan_rusak
        $brokenBuilder = $db->table('pelaporan_rusak p')
                            ->join('distribusi d', 'd.id = p.id_distribusi')
                            ->select('d.no_unit')
                            ->where('p.id_barang', $id)
                            ->where('p.status !=', 'tidak_bisa_diperbaiki')
                            ->where('d.no_unit >', 0);
                            
        if ($tujuan === 'ruangan') {
            $brokenBuilder->where('p.id_ruangan', $idRuangan);
        } else {
            $brokenBuilder->where('d.tujuan_distribusi', 'peminjaman');
        }
        
        $brokenUnits = array_column($brokenBuilder->get()->getResultArray(), 'no_unit');
        $existingUnits = array_unique(array_merge($existingUnits, $brokenUnits));

        $maxUnit = !empty($existingUnits) ? max($existingUnits) : 0;
        
        $missingUnits = [];
        for ($i = 1; $i <= $maxUnit; $i++) {
            if (!in_array($i, $existingUnits)) {
                $missingUnits[] = $i;
            }
        }

        // Insert N record + update stok dalam satu transaction
        $db->transStart();
        for ($i = 0; $i < $jumlah; $i++) {
            if (!empty($missingUnits)) {
                $currentNoUnit = array_shift($missingUnits);
            } else {
                $maxUnit++;
                $currentNoUnit = $maxUnit;
            }

            $this->distribusiModel->insert([
                'id_barang'        => $id,
                'no_unit'          => $currentNoUnit,
                'id_ruangan'       => $idRuangan,
                'jumlah'           => 1,
                'tanggal'          => $this->request->getPost('tanggal'),
                'penanggung_jawab' => $this->request->getPost('penanggung_jawab'),
                'status'           => 'digunakan',
                'tujuan_distribusi' => $tujuan,
                'keterangan'       => $keterangan,
            ]);
        }

        // Update stok barang sesuai tujuan distribusi
        if ($tujuan === 'peminjaman') {
            $this->barangModel->update($id, [
                'stok_gudang'      => (int)$barang['stok_gudang'] - $jumlah,
                'stok_peminjaman'  => (int)($barang['stok_peminjaman'] ?? 0) + $jumlah,
            ]);
        } else {
            $this->barangModel->update($id, [
                'stok_gudang'      => (int)$barang['stok_gudang'] - $jumlah,
                'jumlah_digunakan' => (int)$barang['jumlah_digunakan'] + $jumlah,
            ]);
        }
        $db->transComplete();

        if (! $db->transStatus()) {
            return redirect()->back()->with('error', 'Gagal menyimpan distribusi. Silakan coba lagi.');
        }

        return redirect()->to(base_url('barang/' . encode_id($id) . '/distribusi'))
            ->with('success', $tujuan === 'peminjaman'
                ? 'Barang berhasil didistribusikan ke pool peminjaman!'
                : 'Barang berhasil didistribusikan!');
    }

    public function tarikDistribusi(string $hash): \CodeIgniter\HTTP\RedirectResponse
    {
        if ($redirect = $this->checkAuth()) return $redirect;
        if ($redirect = deny_if_not('admin')) return $redirect;
        $idDistribusi = $this->decodeId($hash);
        if (! $idDistribusi) return redirect()->back()->with('error', 'Link tidak valid.');

        $dist = $this->distribusiModel->find($idDistribusi);
        if (! $dist) return redirect()->to(base_url('dashboard'));

        $idBarang = $dist['id_barang'];
        $jumlah   = (int)$dist['jumlah'];
        $barang   = $this->barangModel->find($idBarang);
        $tujuan   = $dist['tujuan_distribusi'] ?? 'ruangan';

        // Tarik distribusi + update stok dalam satu transaction
        $db = \Config\Database::connect();
        $db->transStart();
        $this->distribusiModel->tarikBarang($idDistribusi);

        // Kembalikan stok sesuai jalur distribusi asal
        if ($tujuan === 'peminjaman') {
            $this->barangModel->update($idBarang, [
                'stok_gudang'     => (int)$barang['stok_gudang'] + $jumlah,
                'stok_peminjaman' => max(0, (int)($barang['stok_peminjaman'] ?? 0) - $jumlah),
            ]);
        } else {
            $this->barangModel->update($idBarang, [
                'stok_gudang'      => (int)$barang['stok_gudang'] + $jumlah,
                'jumlah_digunakan' => max(0, (int)$barang['jumlah_digunakan'] - $jumlah),
            ]);
        }
        $db->transComplete();

        if (! $db->transStatus()) {
            return redirect()->back()->with('error', 'Gagal menarik barang. Silakan coba lagi.');
        }

        return redirect()->back()
            ->with('success', 'Barang berhasil ditarik kembali ke gudang!');
    }

    public function hapusDistribusi(string $hash): \CodeIgniter\HTTP\RedirectResponse
    {
        if ($redirect = $this->checkAuth()) return $redirect;
        if ($redirect = deny_if_not('admin')) return $redirect;
        $idDistribusi = $this->decodeId($hash);
        if (! $idDistribusi) return redirect()->back()->with('error', 'Link tidak valid.');

        $dist = $this->distribusiModel->find($idDistribusi);
        if (! $dist) return redirect()->back()->with('error', 'Data tidak ditemukan.');

        // Hanya izinkan hapus jika statusnya ditarik
        if (!in_array($dist['status'], ['ditarik', 'digudang'])) {
            return redirect()->back()->with('error', 'Hanya data distribusi dengan status Ditarik atau Di Gudang yang bisa dihapus riwayatnya.');
        }

        $this->distribusiModel->delete($idDistribusi);

        return redirect()->back()
            ->with('success', 'Riwayat distribusi berhasil dihapus permanen!');
    }

    public function tarikDistribusiBulk(): \CodeIgniter\HTTP\RedirectResponse
    {
        if ($redirect = $this->checkAuth()) return $redirect;
        if ($redirect = deny_if_not('admin')) return $redirect;

        $ids = $this->request->getPost('id_distribusi');
        if (empty($ids) || !is_array($ids)) {
            return redirect()->back()->with('error', 'Tidak ada data yang dipilih.');
        }
        try {
            $ids = $this->normalizeBulkIds($ids);
        } catch (\InvalidArgumentException $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }

        $berhasil = 0;
        foreach ($ids as $hash) {
            $id = $this->decodeId((string)$hash);
            if (! $id) continue;
            
            $dist = $this->distribusiModel->find($id);
            if (!$dist || !in_array($dist['status'], ['digunakan'])) continue;

            $idBarang = $dist['id_barang'];
            $jumlah   = (int)$dist['jumlah'];
            $barang   = $this->barangModel->find($idBarang);
            $tujuan   = $dist['tujuan_distribusi'] ?? 'ruangan';

            $this->distribusiModel->tarikBarang($id);

            // Kembalikan stok sesuai jalur distribusi asal
            if ($tujuan === 'peminjaman') {
                $this->barangModel->update($idBarang, [
                    'stok_gudang'     => (int)$barang['stok_gudang'] + $jumlah,
                    'stok_peminjaman' => max(0, (int)($barang['stok_peminjaman'] ?? 0) - $jumlah),
                ]);
            } else {
                $this->barangModel->update($idBarang, [
                    'stok_gudang'      => (int)$barang['stok_gudang'] + $jumlah,
                    'jumlah_digunakan' => max(0, (int)$barang['jumlah_digunakan'] - $jumlah),
                ]);
            }
            $berhasil++;
        }

        return redirect()->back()->with('success', "$berhasil barang berhasil ditarik kembali ke gudang!");
    }

    public function hapusDistribusiBulk(): \CodeIgniter\HTTP\RedirectResponse
    {
        if ($redirect = $this->checkAuth()) return $redirect;
        if ($redirect = deny_if_not('admin', 'koordinator_lab')) return $redirect;

        $ids = $this->request->getPost('id_distribusi');
        if (empty($ids) || !is_array($ids)) {
            return redirect()->back()->with('error', 'Tidak ada riwayat yang dipilih.');
        }
        try {
            $ids = $this->normalizeBulkIds($ids);
        } catch (\InvalidArgumentException $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }

        $berhasil = 0;
        foreach ($ids as $hash) {
            $id = $this->decodeId((string)$hash);
            if (! $id) continue;
            
            $dist = $this->distribusiModel->find($id);
            if (!$dist || !in_array($dist['status'], ['ditarik', 'digudang'])) continue;

            $this->distribusiModel->delete($id);
            $berhasil++;
        }

        return redirect()->back()->with('success', "$berhasil riwayat distribusi berhasil dihapus permanen!");
    }



    // ============================================================
    // PEMINJAMAN — Daftar & Form Peminjaman Barang
    // ============================================================
    public function peminjaman(string $hash): string|\CodeIgniter\HTTP\RedirectResponse
    {
        if ($redirect = $this->checkAuth()) return $redirect;
        $id = $this->decodeId($hash);
        if (! $id) return redirect()->to(base_url('dashboard'))->with('error', 'Link tidak valid.');

        $barang = $this->barangModel->getDetail($id);
        if (! $barang) {
            return redirect()->to(base_url('dashboard'))
                ->with('error', 'Data barang tidak ditemukan.');
        }

        return view('barang/peminjaman', [
            'title'          => 'Peminjaman — ' . $barang['nama_barang'],
            'active_menu'    => 'dashboard',
            'barang'         => $barang,
            'peminjaman'     => $this->peminjamanModel->getByBarang($id),
            'total_dipinjam' => $this->peminjamanModel->getTotalDipinjam($id),
        ]);
    }

    public function simpanPeminjaman(string $hash): \CodeIgniter\HTTP\RedirectResponse
    {
        if ($redirect = $this->checkAuth()) return $redirect;
        if ($redirect = deny_if_not('admin')) return $redirect;
        $id = $this->decodeId($hash);
        if (! $id) return redirect()->to(base_url('dashboard'))->with('error', 'Link tidak valid.');

        $barang = $this->barangModel->getDetail($id);
        if (! $barang) return redirect()->to(base_url('dashboard'));

        $jumlah = (int) $this->request->getPost('jumlah');

        if ($jumlah <= 0) {
            return redirect()->back()->with('error', 'Jumlah harus lebih dari 0.');
        }

        if ($jumlah > (int)($barang['stok_peminjaman'] ?? 0)) {
            return redirect()->back()
                ->with('error', 'Stok di unit Peminjaman tidak mencukupi. Tersedia: ' . (int)($barang['stok_peminjaman'] ?? 0));
        }

        $keterangan = $this->request->getPost('keterangan');
        if (!is_valid_keterangan($keterangan)) {
            return redirect()->back()->withInput()->with('error', 'Keterangan tidak valid. Jika diisi, wajib mengandung minimal 3 karakter alfabet.');
        }

        $this->peminjamanModel->insert([
            'id_barang'      => $id,
            'nama_peminjam'  => $this->request->getPost('nama_peminjam'),
            'nim_nik'        => $this->request->getPost('nim_nik'),
            'jumlah'         => $jumlah,
            'tanggal_pinjam' => $this->request->getPost('tanggal_pinjam'),
            'tenggat_kembali' => $this->request->getPost('tenggat_kembali'),
            'status'         => 'dipinjam',
            'keterangan'     => $keterangan,
        ]);

        $this->barangModel->update($id, [
            'stok_peminjaman' => max(0, (int)($barang['stok_peminjaman'] ?? 0) - $jumlah),
            'jumlah_dipinjam' => (int)$barang['jumlah_dipinjam'] + $jumlah,
        ]);

        return redirect()->to(base_url('barang/' . encode_id($id) . '/peminjaman'))
            ->with('success', 'Data peminjaman berhasil dicatat!');
    }

    public function kembalikanPeminjaman(string $hash): \CodeIgniter\HTTP\RedirectResponse
    {
        if ($redirect = $this->checkAuth()) return $redirect;
        if ($redirect = deny_if_not('admin')) return $redirect;
        $idPeminjaman = $this->decodeId($hash);
        if (! $idPeminjaman) return redirect()->back()->with('error', 'Link tidak valid.');

        $pinjam = $this->peminjamanModel->find($idPeminjaman);
        if (! $pinjam) return redirect()->to(base_url('dashboard'));

        $idBarang = $pinjam['id_barang'];
        $jumlah   = (int)$pinjam['jumlah'];
        $barang   = $this->barangModel->find($idBarang);

        $this->peminjamanModel->tandaiKembali($idPeminjaman);

        $this->barangModel->update($idBarang, [
            'stok_peminjaman' => (int)($barang['stok_peminjaman'] ?? 0) + $jumlah,
            'jumlah_dipinjam' => max(0, (int)$barang['jumlah_dipinjam'] - $jumlah),
        ]);

        return redirect()->to(base_url('barang/' . encode_id($idBarang) . '/peminjaman'))
            ->with('success', 'Barang berhasil dicatat sebagai kembali!');
    }

    public function hapusPeminjaman(string $hash): \CodeIgniter\HTTP\RedirectResponse
    {
        if ($redirect = $this->checkAuth()) return $redirect;
        if ($redirect = deny_if_not('admin')) return $redirect;
        $idPeminjaman = $this->decodeId($hash);
        if (! $idPeminjaman) return redirect()->back()->with('error', 'Link tidak valid.');

        $pinjam = $this->peminjamanModel->find($idPeminjaman);
        if (! $pinjam) return redirect()->to(base_url('dashboard'));

        $idBarang = $pinjam['id_barang'];

        // Jika masih aktif, kurangi status jumlah dipinjam tanpa mengembalikan ke gudang (karena dianggap hilang/rusak/dihapusbukukan)
        if (in_array($pinjam['status'], ['dipinjam', 'terlambat'])) {
            $barang = $this->barangModel->find($idBarang);
            $this->barangModel->update($idBarang, [
                'jumlah_dipinjam' => max(0, (int)$barang['jumlah_dipinjam'] - (int)$pinjam['jumlah']),
            ]);
        }

        $this->peminjamanModel->delete($idPeminjaman);

        return redirect()->to(base_url('barang/' . encode_id($idBarang) . '/peminjaman'))
            ->with('success', 'Data peminjaman berhasil dihapus!');
    }

    // ============================================================
    // PELAPORAN RUSAK — Daftar & Form Laporan Kerusakan
    // ============================================================
    public function pelaporan(string $hash): string|\CodeIgniter\HTTP\RedirectResponse
    {
        if ($redirect = $this->checkAuth()) return $redirect;
        $id = $this->decodeId($hash);
        if (! $id) return redirect()->to(base_url('dashboard'))->with('error', 'Link tidak valid.');

        $barang = $this->barangModel->getDetail($id);
        if (! $barang) {
            return redirect()->to(base_url('dashboard'))
                ->with('error', 'Data barang tidak ditemukan.');
        }

        return view('barang/pelaporan', [
            'title'        => 'Pelaporan Rusak — ' . $barang['nama_barang'],
            'active_menu'  => 'dashboard',
            'barang'       => $barang,
            'pelaporan'    => $this->pelaporanModel->getByBarang($id),
            'total_rusak'  => $this->pelaporanModel->getTotalRusak($id),
            'ruangan'      => $this->distribusiModel->getRuangan(),
        ]);
    }

    public function simpanPelaporan(string $hash): \CodeIgniter\HTTP\RedirectResponse
    {
        if ($redirect = $this->checkAuth()) return $redirect;
        if ($redirect = deny_if_not('admin')) return $redirect;
        $id = $this->decodeId($hash);
        if (! $id) return redirect()->to(base_url('dashboard'))->with('error', 'Link tidak valid.');

        $barang = $this->barangModel->getDetail($id);
        if (! $barang) return redirect()->to(base_url('dashboard'));

        $jumlah = (int) $this->request->getPost('jumlah');

        if ($jumlah <= 0) {
            return redirect()->back()->with('error', 'Jumlah harus lebih dari 0.');
        }

        $keterangan = $this->request->getPost('keterangan');
        if (!is_valid_keterangan($keterangan)) {
            return redirect()->back()->withInput()->with('error', 'Keterangan tidak valid. Jika diisi, wajib mengandung minimal 3 karakter alfabet.');
        }

        $this->pelaporanModel->insert([
            'id_barang'        => $id,
            'id_ruangan'       => $this->request->getPost('id_ruangan') ?: null,
            'jumlah'           => $jumlah,
            'tanggal_lapor'    => $this->request->getPost('tanggal_lapor'),
            'tingkat_kerusakan' => $this->request->getPost('tingkat_kerusakan'),
            'status'           => 'menunggu',
            'keterangan'       => $keterangan,
        ]);

        $this->barangModel->update($id, [
            'jumlah_rusak' => (int)$barang['jumlah_rusak'] + $jumlah,
            'stok_gudang'  => max(0, (int)$barang['stok_gudang'] - $jumlah),
        ]);

        return redirect()->to(base_url('barang/' . encode_id($id) . '/pelaporan'))
            ->with('success', 'Laporan kerusakan berhasil dicatat!');
    }

    public function updateStatusPelaporan(string $hash): \CodeIgniter\HTTP\RedirectResponse
    {
        if ($redirect = $this->checkAuth()) return $redirect;
        if ($redirect = deny_if_not('admin')) return $redirect;
        $idLaporan = $this->decodeId($hash);
        if (! $idLaporan) return redirect()->back()->with('error', 'Link tidak valid.');

        $laporan = $this->pelaporanModel->find($idLaporan);
        if (! $laporan) return redirect()->to(base_url('dashboard'));

        // Whitelist status yang diizinkan
        $allowedStatus = ['menunggu', 'sedang_diperbaiki', 'selesai', 'tidak_bisa_diperbaiki'];
        $status = $this->request->getPost('status');
        if (! in_array($status, $allowedStatus)) {
            return redirect()->back()->with('error', 'Status tidak valid.');
        }

        $this->pelaporanModel->updateStatus($idLaporan, $status);

        return redirect()->to(base_url('barang/' . encode_id($laporan['id_barang']) . '/pelaporan'))
            ->with('success', 'Status laporan berhasil diperbarui!');
    }

    public function editPelaporan(string $hash): \CodeIgniter\HTTP\RedirectResponse
    {
        if ($redirect = $this->checkAuth()) return $redirect;
        if ($redirect = deny_if_not('admin')) return $redirect;
        $idLaporan = $this->decodeId($hash);
        if (! $idLaporan) return redirect()->back()->with('error', 'Link tidak valid.');

        $laporan = $this->pelaporanModel->find($idLaporan);
        if (! $laporan) return redirect()->to(base_url('dashboard'));

        $jumlahBaru = (int) $this->request->getPost('jumlah');
        $selisih = $jumlahBaru - (int)$laporan['jumlah'];

        if ($selisih != 0) {
            $barang = $this->barangModel->find($laporan['id_barang']);
            // Validasi: jika selisih positif (penambahan), cek stok gudang mencukupi
            if ($selisih > 0 && $selisih > (int)$barang['stok_gudang']) {
                return redirect()->back()
                    ->with('error', 'Stok gudang tidak mencukupi untuk penambahan jumlah rusak. Tersedia: ' . $barang['stok_gudang']);
            }
            $this->barangModel->update($laporan['id_barang'], [
                'jumlah_rusak' => max(0, (int)$barang['jumlah_rusak'] + $selisih),
                'stok_gudang'  => max(0, (int)$barang['stok_gudang'] - $selisih),
            ]);
        }

        $this->pelaporanModel->update($idLaporan, [
            'id_ruangan'        => $this->request->getPost('id_ruangan') ?: null,
            'tingkat_kerusakan' => $this->request->getPost('tingkat_kerusakan'),
            'status'            => $this->request->getPost('status'),
            'jumlah'            => $jumlahBaru,
        ]);

        return redirect()->to(base_url('barang/' . encode_id($laporan['id_barang']) . '/pelaporan'))
            ->with('success', 'Data laporan kerusakan berhasil diperbarui!');
    }

    // ============================================================
    // RESTOCK (Penambahan Stok)
    // ============================================================
    public function restock(string $hash): \CodeIgniter\HTTP\RedirectResponse
    {
        if ($redirect = $this->checkAuth()) return $redirect;
        if ($redirect = deny_if_not('admin')) return $redirect;
        $id = $this->decodeId($hash);
        if (! $id) return redirect()->to(base_url('dashboard'))->with('error', 'Link tidak valid.');

        $barang = $this->barangModel->find($id);
        if (! $barang) {
            return redirect()->to(base_url('dashboard'))->with('error', 'Data barang tidak ditemukan.');
        }

        $jumlah = (int) $this->request->getPost('jumlah');
        if ($jumlah <= 0) {
            return redirect()->back()->with('error', 'Jumlah restock harus lebih dari 0.');
        }

        $db = \Config\Database::connect();
        
        $keterangan = $this->request->getPost('keterangan');
        if (!is_valid_keterangan($keterangan)) {
            return redirect()->back()->withInput()->with('error', 'Keterangan tidak valid. Jika diisi, wajib mengandung minimal 3 karakter alfabet.');
        }

        // 1. Catat ke tabel barang_masuk
        $db->table('barang_masuk')->insert([
            'id_barang'     => $id,
            'jumlah'        => $jumlah,
            'tanggal_masuk' => $this->request->getPost('tanggal_masuk') ?: date('Y-m-d'),
            'keterangan'    => $keterangan,
        ]);

        // 2. Update stok di tabel barang
        $this->barangModel->update($id, [
            'stok_awal'   => (int)$barang['stok_awal'] + $jumlah,
            'stok_gudang' => (int)$barang['stok_gudang'] + $jumlah,
        ]);

        return redirect()->to(base_url('barang/' . encode_id($id) . '/detail'))
            ->with('success', 'Stok barang berhasil ditambahkan!');
    }

    // ============================================================
    // EDIT & HAPUS Barang
    // ============================================================
    public function edit(string $hash): \CodeIgniter\HTTP\RedirectResponse
    {
        if ($redirect = $this->checkAuth()) return $redirect;
        if ($redirect = deny_if_not('admin')) return $redirect;
        $id = $this->decodeId($hash);
        if (! $id) return redirect()->to(base_url('dashboard'))->with('error', 'Link tidak valid.');

        $barang = $this->barangModel->find($id);
        if (! $barang) {
            return redirect()->to(base_url('dashboard'))
                ->with('error', 'Data barang tidak ditemukan.');
        }

        // Handle foto upload
        $fotoNama = $barang['foto_barang']; // pertahankan foto lama jika tidak diupload baru
        $foto = $this->request->getFile('foto_barang');
        if ($foto && $foto->isValid() && ! $foto->hasMoved()) {
            // Validasi MIME type (server-side)
            $allowedMime = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
            $allowedExt  = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
            if (! in_array($foto->getMimeType(), $allowedMime)
                || ! in_array(strtolower($foto->getExtension()), $allowedExt)) {
                return redirect()->back()->withInput()
                    ->with('error', 'Format foto tidak didukung. Gunakan JPG, PNG, WEBP, atau GIF.');
            }
            // Validasi ukuran file: maks 2 MB
            if ($foto->getSize() > 2 * 1024 * 1024) {
                return redirect()->back()->withInput()
                    ->with('error', 'Ukuran foto melebihi batas maksimal 2 MB.');
            }
            // Hapus foto lama
            if ($fotoNama && file_exists(FCPATH . 'uploads/' . $fotoNama)) {
                @unlink(FCPATH . 'uploads/' . $fotoNama);
            }
            $fotoNama = $foto->getRandomName();
            $foto->move(FCPATH . 'uploads', $fotoNama);
        }

        $newKode = trim($this->request->getPost('kode_barang') ?? '');
        if ($newKode !== '' && $newKode !== $barang['kode_barang']) {
            if ($this->barangModel->where('kode_barang', $newKode)->where('id !=', $id)->first()) {
                return redirect()->back()->withInput()
                    ->with('error', 'Gagal: Kode barang "' . esc($newKode) . '" sudah digunakan oleh barang lain.');
            }
        }

        $updateData = [
            'kode_barang'       => $newKode !== '' ? $newKode : $barang['kode_barang'],
            'nama_barang'       => $this->request->getPost('nama_barang') ?? $barang['nama_barang'],
            'kategori'          => $this->request->getPost('kategori') ?: $barang['kategori'],
            'id_kategori'       => $this->request->getPost('id_kategori') ?: null,
            'merk'              => $this->request->getPost('merk'),
            'tanggal_perolehan' => $this->request->getPost('tanggal_perolehan') ?: null,
            'foto_barang'       => $fotoNama,
        ];

        // Spesifikasi hanya diperbarui jika field-nya memang terkirim dari form.
        // Form lama (belum ada field spesifikasi) tidak mengirim field ini,
        // jadi spesifikasi yang sudah tersimpan tidak ikut terhapus/tertulis NULL.
        if ($this->request->getPost('spesifikasi') !== null) {
            $updateData['spesifikasi'] = trim((string) $this->request->getPost('spesifikasi')) ?: null;
        }

        $newStokAwal = $this->request->getPost('stok_awal');
        if ($newStokAwal !== null && $newStokAwal !== '') {
            $newStokAwal = (int)$newStokAwal;
            
            // Hitung stok yang benar-benar terpakai dari komponennya
            $stokTerpakai = (int)$barang['jumlah_digunakan'] 
                          + (int)($barang['stok_peminjaman'] ?? 0) 
                          + (int)$barang['jumlah_dipinjam'] 
                          + (int)$barang['jumlah_rusak'];
            
            // Sync stok_awal saat ini jika out of sync (karena bug sebelumnya)
            $currentTotal = $stokTerpakai + (int)$barang['stok_gudang'];
            $stokAwalAcuan = (int)$barang['stok_awal'];
            if ($stokAwalAcuan != $currentTotal) {
                $stokAwalAcuan = $currentTotal;
            }

            $minStok = max($stokTerpakai, 1);
            
            // Validate limits: cannot exceed original stok_awal, cannot be less than minStok
            if ($newStokAwal <= $stokAwalAcuan && $newStokAwal >= $minStok) {
                $updateData['stok_awal'] = $newStokAwal;
                $updateData['stok_gudang'] = $newStokAwal - $stokTerpakai;
            } elseif ($newStokAwal < $minStok) {
                return redirect()->back()->withInput()
                    ->with('error', 'Gagal: Stok awal minimal yang diizinkan adalah ' . $minStok . '.');
            } elseif ($newStokAwal > $stokAwalAcuan) {
                return redirect()->back()->withInput()
                    ->with('error', 'Gagal: Stok awal tidak boleh melebihi stok sebelumnya (' . $stokAwalAcuan . ').');
            }
        }

        $this->barangModel->update($id, $updateData);

        return redirect()->to(base_url('barang/' . encode_id($id) . '/detail'))
            ->with('success', 'Data barang berhasil diperbarui!');
    }

    public function hapus(string $hash): \CodeIgniter\HTTP\RedirectResponse
    {
        if ($redirect = $this->checkAuth()) return $redirect;
        if ($redirect = deny_if_not('admin')) return $redirect;
        $id = $this->decodeId($hash);
        if (! $id) return redirect()->to(base_url('dashboard'))->with('error', 'Link tidak valid.');

        $barang = $this->barangModel->find($id);
        if (! $barang) {
            return redirect()->to(base_url('dashboard'))
                ->with('error', 'Data barang tidak ditemukan.');
        }

        // Cek dependensi: distribusi aktif
        $db = \Config\Database::connect();
        $aktifDist = $db->table('distribusi')
            ->where('id_barang', $id)
            ->whereIn('status', ['digunakan', 'pelaporan_rusak'])
            ->countAllResults();
        if ($aktifDist > 0) {
            return redirect()->to(base_url('barang/' . encode_id($id) . '/detail'))
                ->with('error', 'Barang tidak dapat dihapus karena masih memiliki ' . $aktifDist . ' unit yang sedang digunakan/dilaporkan. Tarik semua unit terlebih dahulu.');
        }

        // Cek dependensi: peminjaman aktif
        $aktifPinjam = $db->table('peminjaman')
            ->where('id_barang', $id)
            ->whereIn('status', ['dipinjam', 'terlambat'])
            ->countAllResults();
        if ($aktifPinjam > 0) {
            return redirect()->to(base_url('barang/' . encode_id($id) . '/detail'))
                ->with('error', 'Barang tidak dapat dihapus karena masih ada ' . $aktifPinjam . ' peminjaman aktif.');
        }

        // Hapus file foto_barang jika ada
        if (!empty($barang['foto_barang'])) {
            $path = FCPATH . 'uploads/' . $barang['foto_barang'];
            if (file_exists($path)) {
                @unlink($path);
            }
        }

        $this->barangModel->delete($id);

        return redirect()->to(base_url('dashboard'))
            ->with('success', 'Barang "' . $barang['nama_barang'] . '" berhasil dihapus dari inventaris.');
    }

    // ============================================================
    // Helper: Aktivitas Terakhir (gabungan distribusi, peminjaman, pelaporan)
    // ============================================================
    private function getAktivitasTerakhir(int $idBarang, int $limit = 6): array
    {
        $db = \Config\Database::connect();

        // Distribusi terakhir (Hanya dari Gudang, bukan hasil alokasi ulang)
        $distList = $db->table('distribusi d')
            ->select("d.tanggal AS tgl, MAX(d.created_at) AS time_sort, CONCAT(SUM(d.jumlah), ' Pcs didistribusikan ke ', IF(d.tujuan_distribusi = 'peminjaman', 'Peminjaman', COALESCE(r.nama_ruangan, 'Ruangan Tidak Diketahui'))) AS keterangan, 'distribusi' AS tipe")
            ->join('ruangan r', 'r.id = d.id_ruangan', 'left')
            ->where('d.id_barang', $idBarang)
            ->where("NOT EXISTS (SELECT 1 FROM distribusi d2 WHERE d2.id_barang = d.id_barang AND d2.alokasi_ke_ruangan = d.id_ruangan AND d2.tanggal_alokasi = d.tanggal)")
            ->groupBy('d.tanggal, d.id_ruangan, r.nama_ruangan, d.tujuan_distribusi')
            ->orderBy('time_sort', 'DESC')->limit(3)
            ->get()->getResultArray();

        // Relokasi / Alokasi Ulang (Dari ruangan ke ruangan lain)
        $relokasiList = $db->table('distribusi d')
            ->select("d.tanggal_alokasi AS tgl, MAX(d.updated_at) AS time_sort, CONCAT(SUM(d.jumlah), ' Pcs dialokasikan ulang dari ', COALESCE(r_asal.nama_ruangan, 'Ruangan'), ' ke ', COALESCE(r_tujuan.nama_ruangan, 'Ruangan')) AS keterangan, 'distribusi' AS tipe")
            ->join('ruangan r_asal', 'r_asal.id = d.id_ruangan', 'left')
            ->join('ruangan r_tujuan', 'r_tujuan.id = d.alokasi_ke_ruangan', 'left')
            ->where('d.id_barang', $idBarang)
            ->where('d.alokasi_ke_ruangan IS NOT NULL')
            ->groupBy('d.tanggal_alokasi, d.id_ruangan, r_asal.nama_ruangan, d.alokasi_ke_ruangan, r_tujuan.nama_ruangan')
            ->orderBy('time_sort', 'DESC')->limit(3)
            ->get()->getResultArray();

        // Peminjaman terakhir
        $pinjList = $db->table('peminjaman')
            ->select("tanggal_pinjam AS tgl, created_at AS time_sort, CONCAT(nama_peminjam, ' meminjam ', jumlah, ' Pcs') AS keterangan, 'peminjaman' AS tipe")
            ->where('id_barang', $idBarang)
            ->orderBy('time_sort', 'DESC')->limit(2)
            ->get()->getResultArray();

        // Laporan rusak terakhir
        $lapList = $db->table('pelaporan_rusak p')
            ->select("p.tanggal_lapor AS tgl, p.created_at AS time_sort, CONCAT('Laporan kerusakan ', p.jumlah, ' Pcs dari ', COALESCE(r.nama_ruangan, 'Gudang')) AS keterangan, 'pelaporan' AS tipe")
            ->join('ruangan r', 'r.id = p.id_ruangan', 'left')
            ->where('p.id_barang', $idBarang)
            ->orderBy('time_sort', 'DESC')->limit(2)
            ->get()->getResultArray();

        // Restock / Barang masuk terakhir
        $masukList = $db->table('barang_masuk')
            ->select("tanggal_masuk AS tgl, created_at AS time_sort, CONCAT('Restock stok gudang sebanyak ', jumlah, ' Pcs', IF(keterangan != '', CONCAT(' (Keterangan: ', keterangan, ')'), '')) AS keterangan, 'restock' AS tipe")
            ->where('id_barang', $idBarang)
            ->orderBy('time_sort', 'DESC')->limit(2)
            ->get()->getResultArray();

        // Ditarik / dikembalikan ke gudang
        $tarikList = $db->table('distribusi d')
            ->select("DATE(d.updated_at) AS tgl, MAX(d.updated_at) AS time_sort, CONCAT(SUM(d.jumlah), ' Pcs ditarik kembali ke Gudang dari ', IF(d.tujuan_distribusi = 'peminjaman', 'Peminjaman', COALESCE(r.nama_ruangan, 'Ruangan Tidak Diketahui'))) AS keterangan, 'ditarik' AS tipe")
            ->join('ruangan r', 'r.id = d.id_ruangan', 'left')
            ->where('d.id_barang', $idBarang)
            ->whereIn('d.status', ['ditarik', 'digudang'])
            ->groupBy('DATE(d.updated_at), d.id_ruangan, r.nama_ruangan, d.tujuan_distribusi')
            ->orderBy('time_sort', 'DESC')->limit(2)
            ->get()->getResultArray();

        $aktivitas = array_merge($distList, $relokasiList, $pinjList, $lapList, $masukList, $tarikList);

        // Urutkan berdasarkan waktu paling presisi (time_sort) secara menurun
        usort($aktivitas, fn($a, $b) => strcmp($b['time_sort'], $a['time_sort']));

        return array_slice($aktivitas, 0, $limit);
    }

    /**
     * Endpoint untuk mengecek kapasitas sisa ruangan (AJAX)
     */
    public function cekKapasitasRuangan()
    {
        $idRuangan = $this->request->getGet('id_ruangan');
        $idBarang  = $this->request->getGet('id_barang');

        if (!$idRuangan || !$idBarang) {
            return $this->response->setJSON(['error' => 'Parameter tidak lengkap']);
        }

        $barang = $this->barangModel->getDetail($idBarang);
        if (!$barang) {
            return $this->response->setJSON(['error' => 'Barang tidak ditemukan']);
        }

        $jenisBarang = strtolower($barang['id_kategori'] ?? $barang['jenis_barang'] ?? '');
        if (!in_array($jenisBarang, ['meja', 'kursi'])) {
            return $this->response->setJSON(['kapasitas' => -1]); // Tidak dibatasi
        }

        $db = \Config\Database::connect();
        $ruangan = $db->table('ruangan')->where('id', $idRuangan)->get()->getRowArray();
        
        if (!$ruangan) {
            return $this->response->setJSON(['error' => 'Ruangan tidak ditemukan']);
        }

        $kapasitas = (int)($ruangan['kapasitas_' . $jenisBarang] ?? 0);
        
        $totalExisting = $db->table('distribusi d')
            ->join('barang b', 'b.id = d.id_barang')
            ->where('d.id_ruangan', $idRuangan)
            ->where('d.status', 'digunakan')
            ->where('b.id_kategori', $barang['id_kategori'])
            ->selectSum('d.jumlah')
            ->get()->getRowArray();
        
        $totalTerisi = (int)($totalExisting['jumlah'] ?? 0);
        $sisa = max(0, $kapasitas - $totalTerisi);

        return $this->response->setJSON([
            'jenis'     => ucfirst($jenisBarang),
            'kapasitas' => $kapasitas,
            'terisi'    => $totalTerisi,
            'sisa'      => $sisa
        ]);
    }
}

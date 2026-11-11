<?php

namespace App\Controllers;

use App\Models\BarangModel;
use App\Models\DistribusiModel;
use App\Security\InventoryPolicy;

class DataBarangRuangan extends BaseController
{
    protected BarangModel     $barangModel;
    protected DistribusiModel $distribusiModel;

    public function __construct()
    {
        $this->barangModel     = new BarangModel();
        $this->distribusiModel = new DistribusiModel();
    }


    /**
     * Daftar semua ruangan beserta ringkasan jumlah barang.
     */

    public function index(): string|\CodeIgniter\HTTP\RedirectResponse
    {
        if ($redirect = $this->checkAuth()) return $redirect;

        $role = session()->get('user_role');

        // dekan, koordinator, TU, admin, kepala_lab: lihat semua ruangan
        $db = \Config\Database::connect();

        $keyword      = $this->request->getGet('q');
        $filterStatus = $this->request->getGet('status');

        $builder = $db->table('ruangan')->orderBy('nama_ruangan', 'ASC');

        if ($keyword) {
            $builder->groupStart()
                ->like('nama_ruangan', $keyword)
                ->orLike('penanggung_jawab', $keyword)
                ->groupEnd();
        }

        if ($filterStatus) {
            $builder->where('status_ruangan', $filterStatus);
        }

        $ruangan = $builder->get()->getResultArray();

        // ✅ Fix N+1: ambil agregasi semua ruangan sekaligus dengan 1 query GROUP BY
        $agregasi = $db->table('distribusi')
            ->select('id_ruangan, SUM(jumlah) AS total_barang, COUNT(*) AS total_jenis')
            ->where('status', 'digunakan')
            ->groupBy('id_ruangan')
            ->get()->getResultArray();

        // Buat lookup array keyed by id_ruangan
        $agregtasiByRuangan = array_column($agregasi, null, 'id_ruangan');

        foreach ($ruangan as &$r) {
            $agg = $agregtasiByRuangan[$r['id']] ?? null;
            $r['total_barang'] = (int)($agg['total_barang'] ?? 0);
            $r['total_jenis']  = (int)($agg['total_jenis']  ?? 0);
        }
        unset($r);

        return view('data_barang_ruangan/index', [
            'title'          => 'Data Barang Ruangan — SIn-FTIK USM',
            'active_menu'    => 'data-barang-ruangan',
            'ruangan'        => $ruangan,
            'total_ruangan'  => count($ruangan),
            'keyword'        => $keyword,
            'filter_status'  => $filterStatus,
            'total_barang'   => $db->table('distribusi')
                ->where('status', 'digunakan')
                ->selectSum('jumlah')
                ->get()->getRowArray()['jumlah'] ?? 0,
        ]);
    }

    /**
     * Detail barang pada satu ruangan tertentu.
     */
    public function detail(string $hash): string|\CodeIgniter\HTTP\RedirectResponse
    {
        if ($redirect = $this->checkAuth()) return $redirect;
        $id = $this->decodeId($hash);
        if (! $id) return redirect()->to(base_url('data-barang-ruangan'))->with('error', 'Link tidak valid.');

        $db = \Config\Database::connect();

        $ruangan = $db->table('ruangan')
            ->where('id', $id)
            ->get()->getRowArray();

        if (! $ruangan) {
            return redirect()->to(base_url('data-barang-ruangan'))
                ->with('error', 'Data ruangan tidak ditemukan.');
        }

        // Cegah kepala_lab akses ruangan yang bukan miliknya (IDOR prevention)
        if ($redirect = $this->checkRoomAccess($id)) return $redirect;

        $role = session()->get('user_role');

        // Barang di ruangan: aktif (digunakan) + riwayat alokasi (ditarik dengan alokasi_ke_ruangan)
        $barangList = $this->distribusiModel->getByRuangan($id);

        // Total hanya dari yang status=digunakan
        $totalBarang = 0;
        $activeCount = 0;
        foreach ($barangList as $b) {
            if ($b['status'] === 'digunakan') {
                $totalBarang += $b['jumlah'];
                $activeCount++;
            }
        }

        // Stats per kategori dinamis (hanya dari digunakan)
        $kategoriUtama = model('App\Models\KategoriUtamaModel')->findAll();
        $kategoriStats = [];
        foreach ($kategoriUtama as $ku) {
            $kategoriStats[$ku['nama_kategori']] = 0;
        }

        foreach ($barangList as $b) {
            if ($b['status'] !== 'digunakan') continue;
            $kat = $b['kategori'] ?? '';
            if (isset($kategoriStats[$kat])) {
                $kategoriStats[$kat] += $b['jumlah'];
            }
        }
        // Barang yang ada stoknya (untuk dropdown alokasikan dari gudang)
        $barangGudang = $db->table('barang b')
            ->select('b.id, b.nama_barang, b.kode_barang, b.stok_gudang')
            ->where('b.stok_gudang >', 0)
            ->orderBy('b.nama_barang', 'ASC')
            ->get()->getResultArray();

        // Semua ruangan selain ruangan ini (untuk modal alokasi ulang)
        $semuaRuangan = $db->table('ruangan')
            ->where('id !=', $id)
            ->orderBy('nama_ruangan', 'ASC')
            ->get()->getResultArray();

        return view('data_barang_ruangan/detail', [
            'title'         => 'Barang di ' . $ruangan['nama_ruangan'],
            'active_menu'   => 'data-barang-ruangan',
            'ruangan'       => $ruangan,
            'barang_list'   => $barangList,
            'total_barang'  => $totalBarang,
            'kategoriStats' => $kategoriStats,
            'barang_gudang' => $barangGudang,
            'semua_ruangan' => $semuaRuangan,
            'activeCount'   => $activeCount,
        ]);
    }

    /**
     * Simpan data ruangan baru
     */
    public function simpan(): \CodeIgniter\HTTP\RedirectResponse
    {
        if ($redirect = $this->checkAuth()) return $redirect;
        if ($redirect = deny_if_not('admin', 'koordinator_lab')) return $redirect;

        $db = \Config\Database::connect();

        $namaRuangan = trim($this->request->getPost('nama_ruangan'));

        // Cek agar nama ruangan tidak duplikat
        if ($db->table('ruangan')->where('nama_ruangan', $namaRuangan)->countAllResults() > 0) {
            return redirect()->back()->withInput()->with('error', 'Gagal: Nama ruangan atau kelas "' . $namaRuangan . '" sudah terdaftar! Harap gunakan nama lain.');
        }

        $data = [
            'nama_ruangan'     => $namaRuangan,
            'penanggung_jawab' => $this->request->getPost('penanggung_jawab'),
            'status_ruangan'   => $this->request->getPost('status_ruangan') ?: 'Bisa Digunakan',
            'kapasitas_meja'   => (int) $this->request->getPost('kapasitas_meja'),
            'kapasitas_kursi'  => (int) $this->request->getPost('kapasitas_kursi'),
        ];

        $foto = $this->request->getFile('foto_ruangan');
        if ($foto && $foto->isValid() && ! $foto->hasMoved()) {
            // Validasi MIME type & ukuran server-side
            $allowedMime = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
            $allowedExt  = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
            if (! in_array($foto->getMimeType(), $allowedMime)
                || ! in_array(strtolower($foto->getExtension()), $allowedExt)) {
                return redirect()->back()->withInput()
                    ->with('error', 'Format foto tidak didukung. Gunakan JPG, PNG, WEBP, atau GIF.');
            }
            if ($foto->getSize() > 2 * 1024 * 1024) {
                return redirect()->back()->withInput()
                    ->with('error', 'Ukuran foto melebihi batas maksimal 2 MB.');
            }
            $newName = $foto->getRandomName();
            $foto->move(FCPATH . 'uploads/ruangan', $newName);
            $data['foto_ruangan'] = $newName;
        }

        $db->table('ruangan')->insert($data);

        return redirect()->to(base_url('data-barang-ruangan'))
            ->with('success', 'Data ruangan berhasil ditambahkan.');
    }

    /**
     * Edit data ruangan
     */
    public function edit(string $hash): \CodeIgniter\HTTP\RedirectResponse
    {
        if ($redirect = $this->checkAuth()) return $redirect;
        if ($redirect = deny_if_not('admin', 'koordinator_lab')) return $redirect;
        $id = $this->decodeId($hash);
        if (! $id) return redirect()->to(base_url('data-barang-ruangan'))->with('error', 'Link tidak valid.');

        $db = \Config\Database::connect();

        $namaRuangan = trim($this->request->getPost('nama_ruangan'));

        // Cek agar nama ruangan tidak duplikat (kecuali ruangan ini sendiri)
        if ($db->table('ruangan')->where('nama_ruangan', $namaRuangan)->where('id !=', $id)->countAllResults() > 0) {
            return redirect()->back()->withInput()->with('error', 'Gagal: Nama ruangan atau kelas "' . $namaRuangan . '" sudah terdaftar! Harap gunakan nama lain.');
        }

        $kapasitasMejaBaru = (int) $this->request->getPost('kapasitas_meja');
        $kapasitasKursiBaru = (int) $this->request->getPost('kapasitas_kursi');

        // Hitung meja yang sedang digunakan
        $jumlahMejaDigunakan = $db->table('distribusi d')
            ->join('barang b', 'b.id = d.id_barang')
            ->where('d.id_ruangan', $id)
            ->where('d.status', 'digunakan')
            ->like('b.nama_barang', 'meja', 'both')
            ->selectSum('d.jumlah')
            ->get()->getRowArray()['jumlah'] ?? 0;

        // Hitung kursi yang sedang digunakan
        $jumlahKursiDigunakan = $db->table('distribusi d')
            ->join('barang b', 'b.id = d.id_barang')
            ->where('d.id_ruangan', $id)
            ->where('d.status', 'digunakan')
            ->like('b.nama_barang', 'kursi', 'both')
            ->selectSum('d.jumlah')
            ->get()->getRowArray()['jumlah'] ?? 0;

        if ($kapasitasMejaBaru < $jumlahMejaDigunakan) {
            return redirect()->back()->withInput()->with('error', "Kapasitas meja minimal {$jumlahMejaDigunakan} (sudah terisi).");
        }

        if ($kapasitasKursiBaru < $jumlahKursiDigunakan) {
            return redirect()->back()->withInput()->with('error', "Kapasitas kursi minimal {$jumlahKursiDigunakan} (sudah terisi).");
        }

        $data = [
            'nama_ruangan'     => $namaRuangan,
            'penanggung_jawab' => $this->request->getPost('penanggung_jawab'),
            'status_ruangan'   => $this->request->getPost('status_ruangan') ?: 'Bisa Digunakan',
            'kapasitas_meja'   => $kapasitasMejaBaru,
            'kapasitas_kursi'  => $kapasitasKursiBaru,
        ];

        $foto = $this->request->getFile('foto_ruangan');
        if ($foto && $foto->isValid() && ! $foto->hasMoved()) {
            // Validasi MIME type & ukuran server-side
            $allowedMime = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
            $allowedExt  = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
            if (! in_array($foto->getMimeType(), $allowedMime)
                || ! in_array(strtolower($foto->getExtension()), $allowedExt)) {
                return redirect()->back()->withInput()
                    ->with('error', 'Format foto tidak didukung. Gunakan JPG, PNG, WEBP, atau GIF.');
            }
            if ($foto->getSize() > 2 * 1024 * 1024) {
                return redirect()->back()->withInput()
                    ->with('error', 'Ukuran foto melebihi batas maksimal 2 MB.');
            }
            $newName = $foto->getRandomName();
            $foto->move(FCPATH . 'uploads/ruangan', $newName);
            $data['foto_ruangan'] = $newName;
        }

        $db->table('ruangan')->where('id', $id)->update($data);

        return redirect()->to(base_url('data-barang-ruangan'))
            ->with('success', 'Data ruangan berhasil diperbarui.');
    }

    /**
     * Hapus data ruangan
     */
    public function hapus(string $hash): \CodeIgniter\HTTP\RedirectResponse
    {
        if ($redirect = $this->checkAuth()) return $redirect;
        if ($redirect = deny_if_not('admin', 'koordinator_lab')) return $redirect;
        $id = $this->decodeId($hash);
        if (! $id) return redirect()->to(base_url('data-barang-ruangan'))->with('error', 'Link tidak valid.');

        $db = \Config\Database::connect();

        // Cek apakah masih ada barang aktif di ruangan ini
        $jumlahAktif = $db->table('distribusi')
            ->where('id_ruangan', $id)
            ->where('status', 'digunakan')
            ->countAllResults();

        if ($jumlahAktif > 0) {
            return redirect()->to(base_url('data-barang-ruangan'))
                ->with('error', "Ruangan tidak dapat dihapus karena masih memiliki {$jumlahAktif} unit barang aktif. Pindahkan atau tarik barang terlebih dahulu.");
        }

        // Ambil data ruangan untuk menghapus fotonya
        $ruangan = $db->table('ruangan')->where('id', $id)->get()->getRowArray();
        if ($ruangan && !empty($ruangan['foto_ruangan'])) {
            $path = FCPATH . 'uploads/ruangan/' . $ruangan['foto_ruangan'];
            if (file_exists($path)) {
                @unlink($path);
            }
        }

        $db->table('ruangan')->where('id', $id)->delete();

        return redirect()->to(base_url('data-barang-ruangan'))
            ->with('success', 'Data ruangan berhasil dihapus.');
    }

    /**
     * Alokasikan Barang dari Gudang ke Ruangan.
     * Setiap unit barang disimpan sebagai record terpisah (jumlah = 1 per record)
     * agar setiap unit dapat dikelola secara individual.
     */
    public function alokasi(): \CodeIgniter\HTTP\RedirectResponse
    {
        if ($redirect = $this->checkAuth()) return $redirect;
        if ($redirect = deny_if_not('admin', 'koordinator_lab')) return $redirect;

        $db = \Config\Database::connect();

        $id_ruangan = $this->request->getPost('id_ruangan');
        $id_barang  = $this->request->getPost('id_barang');
        $jumlah     = (int)($this->request->getPost('jumlah') ?: 1);
        $kondisi    = $this->request->getPost('kondisi') ?: 'Baik';

        $ruangan = $db->table('ruangan')->where('id', $id_ruangan)->get()->getRowArray();
        $penanggung = $ruangan['penanggung_jawab'] ?? null;

        if (! $id_barang) {
            return redirect()->back()->with('error', 'Pilih barang terlebih dahulu.');
        }

        // Cek stok
        $barang = $this->barangModel->find($id_barang);
        if (! $barang) {
            return redirect()->back()->with('error', 'Barang tidak ditemukan.');
        }

        if ($jumlah > (int)$barang['stok_gudang']) {
            return redirect()->back()
                ->with('error', 'Stok gudang tidak mencukupi. Tersedia: ' . $barang['stok_gudang'] . ' pcs.');
        }

        // Validasi Kapasitas Ruangan untuk Meja & Kursi
        $jenisBarang = strtolower($barang['id_kategori'] ?? '');
        if (in_array($jenisBarang, ['meja', 'kursi'])) {
            $kapasitas = (int)($ruangan['kapasitas_' . $jenisBarang] ?? 0);
            
            // Hitung total meja/kursi yang sedang digunakan di ruangan ini
            $totalExisting = $db->table('distribusi d')
                ->join('barang b', 'b.id = d.id_barang')
                ->where('d.id_ruangan', $id_ruangan)
                ->where('d.status', 'digunakan')
                ->where('b.id_kategori', $barang['id_kategori'])
                ->selectSum('d.jumlah')
                ->get()->getRowArray();
            
            $totalTerisi = (int)($totalExisting['jumlah'] ?? 0);
            
            if (($totalTerisi + $jumlah) > $kapasitas) {
                return redirect()->back()->with('error', 'Gagal: Kapasitas ruangan tidak mencukupi untuk jumlah barang yang ingin ditambahkan.');
            }
        }

        // Cari no_unit terakhir untuk barang ini DI RUANGAN INI saja
        // (no_unit bersifat per-ruangan per-barang, mulai dari 1)
        // Jika ada barang yg sama dari ruangan lain dipindah ke sini (alokasi ulang),
        // no_unit-nya dipertahankan dan penomoran baru melanjutkan dari yang tertinggi.
        // Generate nomor unit otomatis (mengisi kekosongan terlebih dahulu)
        $existingUnits = array_column(
            $db->table('distribusi')
               ->select('no_unit')
               ->where('id_barang', $id_barang)
               ->where('id_ruangan', $id_ruangan)
               ->whereNotIn('status', ['ditarik', 'digudang', 'pelaporan_rusak'])
               ->where('no_unit >', 0)
               ->get()->getResultArray(),
            'no_unit'
        );
        
        // Tambahan: Jangan isi kekosongan jika barang tersebut sedang rusak dan masih ada di pelaporan_rusak
        $brokenUnits = array_column(
            $db->table('pelaporan_rusak p')
               ->join('distribusi d', 'd.id = p.id_distribusi')
               ->select('d.no_unit')
               ->where('p.id_barang', $id_barang)
               ->where('p.id_ruangan', $id_ruangan)
               ->where('p.status !=', 'tidak_bisa_diperbaiki')
               ->where('d.no_unit >', 0)
               ->get()->getResultArray(),
            'no_unit'
        );
        
        $existingUnits = array_unique(array_merge($existingUnits, $brokenUnits));
        $maxUnit = !empty($existingUnits) ? max($existingUnits) : 0;
        
        $missingUnits = [];
        for ($i = 1; $i <= $maxUnit; $i++) {
            if (!in_array($i, $existingUnits)) {
                $missingUnits[] = $i;
            }
        }

        // Insert + update stok dalam satu transaction
        $db->transStart();
        $tanggal = date('Y-m-d');
        for ($i = 0; $i < $jumlah; $i++) {
            if (!empty($missingUnits)) {
                $currentNoUnit = array_shift($missingUnits);
            } else {
                $maxUnit++;
                $currentNoUnit = $maxUnit;
            }

            $db->table('distribusi')->insert([
                'id_barang'        => $id_barang,
                'no_unit'          => $currentNoUnit,
                'id_ruangan'       => $id_ruangan,
                'jumlah'           => 1,
                'tanggal'          => $tanggal,
                'penanggung_jawab' => $penanggung,
                'kondisi'          => $kondisi,
                'status'           => 'digunakan',
            ]);
        }

        // Update stok barang
        $this->barangModel->update($id_barang, [
            'stok_gudang'      => (int)$barang['stok_gudang'] - $jumlah,
            'jumlah_digunakan' => (int)$barang['jumlah_digunakan'] + $jumlah,
        ]);
        $db->transComplete();

        if (! $db->transStatus()) {
            return redirect()->back()->with('error', 'Gagal mengalokasikan barang. Silakan coba lagi.');
        }

        return redirect()->to(base_url('data-barang-ruangan/' . encode_id($id_ruangan)))
            ->with('success', "Berhasil mengalokasikan {$jumlah} unit barang ke ruangan.");
    }

    /**
     * Alokasikan barang dari satu ruangan ke ruangan lain (relokasi)
     */
    public function alokasiUlang(string $hash): \CodeIgniter\HTTP\RedirectResponse
    {
        if ($redirect = $this->checkAuth()) return $redirect;
        if ($redirect = deny_if_not('admin', 'koordinator_lab')) return $redirect;
        $idDistribusi = $this->decodeId($hash);
        if (! $idDistribusi) return redirect()->back()->with('error', 'Link tidak valid.');

        $db = \Config\Database::connect();

        $dist = $db->table('distribusi')->where('id', $idDistribusi)->get()->getRowArray();
        if (! $dist) {
            return redirect()->back()->with('error', 'Data distribusi tidak ditemukan.');
        }

        $idRuanganBaru  = $this->request->getPost('id_ruangan_tujuan');
        $ruangan = $db->table('ruangan')->where('id', $idRuanganBaru)->get()->getRowArray();
        if (! $ruangan) {
            return redirect()->back()->with('error', 'Ruangan tujuan tidak valid.');
        }

        // Validasi Kapasitas Ruangan untuk Meja & Kursi
        $barang = $db->table('barang')->where('id', $dist['id_barang'])->get()->getRowArray();
        $jenisBarang = strtolower($barang['id_kategori'] ?? '');
        if (in_array($jenisBarang, ['meja', 'kursi'])) {
            $kapasitas = (int)($ruangan['kapasitas_' . $jenisBarang] ?? 0);
            
            // Hitung total meja/kursi yang sedang digunakan di ruangan tujuan
            $totalExisting = $db->table('distribusi d')
                ->join('barang b', 'b.id = d.id_barang')
                ->where('d.id_ruangan', $idRuanganBaru)
                ->where('d.status', 'digunakan')
                ->where('b.id_kategori', $barang['id_kategori'])
                ->selectSum('d.jumlah')
                ->get()->getRowArray();
            
            $totalTerisi = (int)($totalExisting['jumlah'] ?? 0);
            $jumlahDist = (int)$dist['jumlah'];
            
            if (($totalTerisi + $jumlahDist) > $kapasitas) {
                return redirect()->back()->with('error', "Gagal: Kapasitas {$jenisBarang} di ruangan tujuan tidak mencukupi (Sisa kapasitas: " . max(0, $kapasitas - $totalTerisi) . ").");
            }
        }

        $penanggungBaru = $ruangan['penanggung_jawab'] ?? $dist['penanggung_jawab'];
        $tanggal        = date('Y-m-d');

        // Clear alokasi_ke_ruangan di record SEBELUMNYA yang menunjuk ke ruangan sumber ini
        // (saat R2→R3: bersihkan info "R1→R2" agar hanya R2→R3 yang tersimpan)
        $db->table('distribusi')
            ->where('alokasi_ke_ruangan', $dist['id_ruangan'])
            ->where('id_barang', $dist['id_barang'])
            ->update(['alokasi_ke_ruangan' => null, 'tanggal_alokasi' => null]);

        // Update record ini: tandai bahwa barang sudah dipindah ke ruangan baru
        $db->table('distribusi')->where('id', $idDistribusi)->update([
            'alokasi_ke_ruangan' => $idRuanganBaru,
            'tanggal_alokasi'    => $tanggal,
            'status'             => 'ditarik', // Bersihkan dari active list ruangan sumber
        ]);

        // (Unit numbers are no longer shifted down when an item leaves a room, creating gaps)

        // Buat record baru di ruangan tujuan
        // Hitung no_unit baru: mengisi kekosongan no_unit di ruangan tujuan
        $existingUnitsTujuan = array_column(
            $db->table('distribusi')
               ->select('no_unit')
               ->where('id_barang', $dist['id_barang'])
               ->where('id_ruangan', $idRuanganBaru)
               ->whereNotIn('status', ['ditarik', 'digudang', 'pelaporan_rusak'])
               ->where('no_unit >', 0)
               ->get()->getResultArray(),
            'no_unit'
        );
        
        // Tambahan: Jangan isi kekosongan jika barang tersebut sedang rusak dan masih ada di pelaporan_rusak
        $brokenUnitsTujuan = array_column(
            $db->table('pelaporan_rusak p')
               ->join('distribusi d', 'd.id = p.id_distribusi')
               ->select('d.no_unit')
               ->where('p.id_barang', $dist['id_barang'])
               ->where('p.id_ruangan', $idRuanganBaru)
               ->where('p.status !=', 'tidak_bisa_diperbaiki')
               ->where('d.no_unit >', 0)
               ->get()->getResultArray(),
            'no_unit'
        );
        
        $existingUnitsTujuan = array_unique(array_merge($existingUnitsTujuan, $brokenUnitsTujuan));
        $maxUnitTujuan = !empty($existingUnitsTujuan) ? max($existingUnitsTujuan) : 0;
        
        $missingUnitsTujuan = [];
        for ($i = 1; $i <= $maxUnitTujuan; $i++) {
            if (!in_array($i, $existingUnitsTujuan)) {
                $missingUnitsTujuan[] = $i;
            }
        }
        
        if (!empty($missingUnitsTujuan)) {
            $newNoUnit = array_shift($missingUnitsTujuan);
        } else {
            $newNoUnit = $maxUnitTujuan + 1;
        }

        $keterangan = $this->request->getPost('keterangan');
        if (!is_valid_keterangan($keterangan)) {
            return redirect()->back()->withInput()->with('error', 'Keterangan tidak valid. Jika diisi, wajib mengandung minimal 3 karakter alfabet.');
        }

        $this->distribusiModel->insert([
            'id_barang'        => $dist['id_barang'],
            'no_unit'          => $newNoUnit,
            'id_ruangan'       => $idRuanganBaru,
            'jumlah'           => $dist['jumlah'],
            'tanggal'          => $tanggal,
            'penanggung_jawab' => $penanggungBaru,
            'kondisi'          => $dist['kondisi'],
            'status'           => 'digunakan',
            'keterangan'       => $keterangan,
            'id_asal_distribusi' => $idDistribusi,
        ]);

        return redirect()->back()
            ->with('success', 'Barang berhasil dialokasikan ke ruangan lain!');
    }

    /**
     * Laporkan Barang Rusak dari halaman Data Barang Ruangan
     * Barang langsung hilang dari tabel dan masuk Pelaporan Rusak
     */
    public function laporRusak(string $hash): \CodeIgniter\HTTP\RedirectResponse
    {
        if ($redirect = $this->checkAuth()) return $redirect;
        if ($redirect = deny_if_not('admin', 'koordinator_lab', 'kepala_lab')) return $redirect;
        $idDistribusi = $this->decodeId($hash);
        if (! $idDistribusi) return redirect()->back()->with('error', 'Link tidak valid.');

        $db = \Config\Database::connect();

        $dist = $db->table('distribusi')->where('id', $idDistribusi)->get()->getRowArray();
        if (! $dist) {
            return redirect()->back()->with('error', 'Data distribusi tidak ditemukan.');
        }

        // Cegah kepala_lab lapor rusak barang di ruangan yang bukan miliknya (IDOR prevention)
        if ($redirect = $this->checkRoomAccess((int) $dist['id_ruangan'])) return $redirect;

        $jumlah    = (int)$dist['jumlah'];
        $idBarang  = $dist['id_barang'];
        $idRuangan = $dist['id_ruangan'];
        $barang    = $this->barangModel->find($idBarang);

        $keterangan = $this->request->getPost('keterangan');
        if (!is_valid_keterangan($keterangan)) {
            return redirect()->back()->withInput()->with('error', 'Keterangan tidak valid. Jika diisi, wajib mengandung minimal 3 karakter alfabet.');
        }

        // Insert pelaporan + update distribusi + update stok dalam satu transaction
        $pelaporanModel = new \App\Models\PelaporanModel();
        $db->transStart();
        $pelaporanModel->insert([
            'id_barang'         => $idBarang,
            'id_distribusi'     => $idDistribusi,
            'id_ruangan'        => $idRuangan,
            'jumlah'            => $jumlah,
            'tanggal_lapor'     => date('Y-m-d'),
            'tingkat_kerusakan' => $this->request->getPost('tingkat_kerusakan') ?: 'rusak_ringan',
            'status'            => 'sedang_diperbaiki',
            'keterangan'        => $keterangan,
        ]);

        // Set distribusi jadi pelaporan_rusak
        $db->table('distribusi')->where('id', $idDistribusi)->update(['status' => 'pelaporan_rusak']);

        $tujuan = $dist['tujuan_distribusi'] ?? 'ruangan';
        $updateBarang = [
            'jumlah_rusak' => (int)$barang['jumlah_rusak'] + $jumlah,
        ];
        if ($tujuan === 'peminjaman') {
            $updateBarang['stok_peminjaman'] = max(0, (int)($barang['stok_peminjaman'] ?? 0) - $jumlah);
        } else {
            $updateBarang['jumlah_digunakan'] = max(0, (int)$barang['jumlah_digunakan'] - $jumlah);
        }
        $this->barangModel->update($idBarang, $updateBarang);
        $db->transComplete();

        if (! $db->transStatus()) {
            return redirect()->back()->with('error', 'Gagal melaporkan kerusakan. Silakan coba lagi.');
        }

        return redirect()->back()
            ->with('success', 'Barang berhasil dilaporkan rusak dan dipindahkan ke menu Pelaporan Barang Rusak!');
    }

    /**
     * Laporkan banyak barang rusak sekaligus
     */
    public function laporRusakBulk(): \CodeIgniter\HTTP\RedirectResponse
    {
        if ($redirect = $this->checkAuth()) return $redirect;
        if ($redirect = deny_if_not('admin', 'koordinator_lab', 'kepala_lab')) return $redirect;

        $db = \Config\Database::connect();
        $ids = $this->request->getPost('id_distribusi');
        if (empty($ids) || !is_array($ids)) {
            return redirect()->back()->with('error', 'Tidak ada barang yang dipilih untuk dilaporkan rusak.');
        }

        try {
            $ids = InventoryPolicy::normalizeBulkIds($ids);
        } catch (\InvalidArgumentException $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }

        $keterangan = $this->request->getPost('keterangan');
        if (!is_valid_keterangan($keterangan)) {
            return redirect()->back()->withInput()->with('error', 'Keterangan tidak valid. Jika diisi, wajib mengandung minimal 3 karakter alfabet.');
        }

        $tingkatKerusakan = $this->request->getPost('tingkat_kerusakan') ?: 'rusak_ringan';
        $pelaporanModel = new \App\Models\PelaporanModel();

        // Resolve dan authorize seluruh target sebelum mutation pertama agar
        // request campuran tidak menghasilkan partial cross-room update.
        $distributions = [];
        foreach ($ids as $hash) {
            $idDistribusi = $this->decodeId($hash);
            if (! $idDistribusi) {
                continue;
            }

            $dist = $db->table('distribusi')->where('id', $idDistribusi)->get()->getRowArray();
            if (! $dist) {
                continue;
            }

            if ($redirect = $this->checkRoomAccess((int) $dist['id_ruangan'])) {
                return $redirect;
            }

            $distributions[] = $dist;
        }

        if ($distributions === []) {
            return redirect()->back()->with('error', 'Tidak ada data distribusi valid yang dipilih.');
        }

        $berhasil = 0;
        $db->transStart();
        foreach ($distributions as $dist) {
            $idDistribusi = (int) $dist['id'];

            $jumlah    = (int)$dist['jumlah'];
            $idBarang  = $dist['id_barang'];
            $idRuangan = $dist['id_ruangan'];
            $barang    = $this->barangModel->find($idBarang);

            // Insert ke pelaporan_rusak
            $pelaporanModel->insert([
                'id_barang'         => $idBarang,
                'id_distribusi'     => $idDistribusi,
                'id_ruangan'        => $idRuangan,
                'jumlah'            => $jumlah,
                'tanggal_lapor'     => date('Y-m-d'),
                'tingkat_kerusakan' => $tingkatKerusakan,
                'status'            => 'sedang_diperbaiki',
                'keterangan'        => $keterangan,
            ]);

            // Hapus dari distribusi aktif (status = pelaporan_rusak)
            $db->table('distribusi')->where('id', $idDistribusi)->update(['status' => 'pelaporan_rusak']);

            $tujuan = $dist['tujuan_distribusi'] ?? 'ruangan';
            $updateBarang = [
                'jumlah_rusak' => (int)$barang['jumlah_rusak'] + $jumlah,
            ];
            if ($tujuan === 'peminjaman') {
                $updateBarang['stok_peminjaman'] = max(0, (int)($barang['stok_peminjaman'] ?? 0) - $jumlah);
            } else {
                $updateBarang['jumlah_digunakan'] = max(0, (int)$barang['jumlah_digunakan'] - $jumlah);
            }

            // Update stok barang
            $this->barangModel->update($idBarang, $updateBarang);
            $berhasil++;
        }

        $db->transComplete();
        if (! $db->transStatus()) {
            return redirect()->back()->with('error', 'Gagal melaporkan kerusakan. Silakan coba lagi.');
        }

        return redirect()->back()
            ->with('success', "$berhasil unit barang berhasil dilaporkan rusak!");
    }

    /**
     * Tarik banyak barang ke gudang sekaligus
     */
    public function tarikBulk(): \CodeIgniter\HTTP\RedirectResponse
    {
        if ($redirect = $this->checkAuth()) return $redirect;
        if ($redirect = deny_if_not('admin')) return $redirect;

        $ids = $this->request->getPost('id_distribusi');
        if (empty($ids) || !is_array($ids)) {
            return redirect()->back()->with('error', 'Tidak ada barang yang dipilih untuk ditarik.');
        }
        try {
            $ids = $this->normalizeBulkIds($ids);
        } catch (\InvalidArgumentException $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }

        $berhasil = 0;
        foreach ($ids as $hashDistribusi) {
            $idDistribusi = $this->decodeId((string) $hashDistribusi);
            if (! $idDistribusi) continue;
            $dist = $this->distribusiModel->find($idDistribusi);
            if (!$dist) continue;

            $idBarang = $dist['id_barang'];
            $jumlah   = (int)$dist['jumlah'];
            $barang   = $this->barangModel->find($idBarang);
            $tujuan   = $dist['tujuan_distribusi'] ?? 'ruangan';

            $this->distribusiModel->tarikBarang($idDistribusi);

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

        return redirect()->back()->with('success', "$berhasil unit barang berhasil ditarik kembali ke gudang!");
    }

    /**
     * Alokasikan banyak barang dari satu ruangan ke ruangan lain (relokasi) sekaligus
     */
    public function alokasiUlangBulk(): \CodeIgniter\HTTP\RedirectResponse
    {
        if ($redirect = $this->checkAuth()) return $redirect;
        if ($redirect = deny_if_not('admin', 'koordinator_lab')) return $redirect;

        $db = \Config\Database::connect();
        $ids = $this->request->getPost('id_distribusi');
        if (empty($ids) || !is_array($ids)) {
            return redirect()->back()->with('error', 'Tidak ada barang yang dipilih untuk dipindahkan.');
        }
        try {
            $ids = $this->normalizeBulkIds($ids);
        } catch (\InvalidArgumentException $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }

        $idRuanganBaru  = $this->request->getPost('id_ruangan_tujuan');
        $ruangan = $db->table('ruangan')->where('id', $idRuanganBaru)->get()->getRowArray();
        
        if (!$ruangan) {
            return redirect()->back()->with('error', 'Ruangan tujuan tidak valid.');
        }

        $tanggal = date('Y-m-d');
        $keterangan = $this->request->getPost('keterangan');
        
        if (!is_valid_keterangan($keterangan)) {
            return redirect()->back()->withInput()->with('error', 'Keterangan tidak valid. Jika diisi, wajib mengandung minimal 3 karakter alfabet.');
        }

        // Validasi Kapasitas Ruangan untuk Meja & Kursi (Bulk)
        $jumlahPindah = ['meja' => 0, 'kursi' => 0];
        $validDistList = [];
        
        foreach ($ids as $hashDistribusi) {
            $idDistribusi = $this->decodeId((string) $hashDistribusi);
            if (! $idDistribusi) continue;
            
            $dist = $db->table('distribusi')->where('id', $idDistribusi)->get()->getRowArray();
            if (! $dist) continue;
            
            $validDistList[] = [
                'id_distribusi' => $idDistribusi,
                'dist' => $dist
            ];
            
            $barang = $db->table('barang')->where('id', $dist['id_barang'])->get()->getRowArray();
            $jenisBarang = strtolower($barang['id_kategori'] ?? '');
            if (in_array($jenisBarang, ['meja', 'kursi'])) {
                $jumlahPindah[$jenisBarang] += (int)$dist['jumlah'];
            }
        }
        
        foreach (['meja', 'kursi'] as $jenis) {
            if ($jumlahPindah[$jenis] > 0) {
                $kapasitas = (int)($ruangan['kapasitas_' . $jenis] ?? 0);
                
                $totalExisting = $db->table('distribusi d')
                    ->join('barang b', 'b.id = d.id_barang')
                    ->where('d.id_ruangan', $idRuanganBaru)
                    ->where('d.status', 'digunakan')
                    ->where('LOWER(b.id_kategori)', $jenis)
                    ->selectSum('d.jumlah')
                    ->get()->getRowArray();
                
                $totalTerisi = (int)($totalExisting['jumlah'] ?? 0);
                
                if (($totalTerisi + $jumlahPindah[$jenis]) > $kapasitas) {
                    return redirect()->back()->with('error', "Gagal: Kapasitas {$jenis} di ruangan tujuan tidak mencukupi (Sisa kapasitas: " . max(0, $kapasitas - $totalTerisi) . ", Jumlah dipindah: {$jumlahPindah[$jenis]}).");
                }
            }
        }

        $berhasil = 0;
        foreach ($validDistList as $item) {
            $idDistribusi = $item['id_distribusi'];
            $dist = $item['dist'];
            if (! $dist) continue;

            $penanggungBaru = $ruangan['penanggung_jawab'] ?? $dist['penanggung_jawab'];

            // Clear alokasi_ke_ruangan di record SEBELUMNYA yang menunjuk ke ruangan sumber ini
            $db->table('distribusi')
                ->where('alokasi_ke_ruangan', $dist['id_ruangan'])
                ->where('id_barang', $dist['id_barang'])
                ->update(['alokasi_ke_ruangan' => null, 'tanggal_alokasi' => null]);

            // Update record ini: tandai bahwa barang sudah dipindah ke ruangan baru
            $db->table('distribusi')->where('id', $idDistribusi)->update([
                'alokasi_ke_ruangan' => $idRuanganBaru,
                'tanggal_alokasi'    => $tanggal,
                'status'             => 'ditarik',
            ]);

            // Hitung no_unit baru di ruangan tujuan
            $existingUnitsTujuan = array_column(
                $db->table('distribusi')
                   ->select('no_unit')
                   ->where('id_barang', $dist['id_barang'])
                   ->where('id_ruangan', $idRuanganBaru)
                   ->whereNotIn('status', ['ditarik', 'digudang', 'pelaporan_rusak'])
                   ->where('no_unit >', 0)
                   ->get()->getResultArray(),
                'no_unit'
            );
            
            // Tambahan: Jangan isi kekosongan jika barang tersebut sedang rusak dan masih ada di pelaporan_rusak
            $brokenUnitsTujuan = array_column(
                $db->table('pelaporan_rusak p')
                   ->join('distribusi d', 'd.id = p.id_distribusi')
                   ->select('d.no_unit')
                   ->where('p.id_barang', $dist['id_barang'])
                   ->where('p.id_ruangan', $idRuanganBaru)
                   ->where('p.status !=', 'tidak_bisa_diperbaiki')
                   ->where('d.no_unit >', 0)
                   ->get()->getResultArray(),
                'no_unit'
            );
            
            $existingUnitsTujuan = array_unique(array_merge($existingUnitsTujuan, $brokenUnitsTujuan));
            $maxUnitTujuan = !empty($existingUnitsTujuan) ? max($existingUnitsTujuan) : 0;
            
            $missingUnitsTujuan = [];
            for ($i = 1; $i <= $maxUnitTujuan; $i++) {
                if (!in_array($i, $existingUnitsTujuan)) {
                    $missingUnitsTujuan[] = $i;
                }
            }
            
            if (!empty($missingUnitsTujuan)) {
                $newNoUnit = array_shift($missingUnitsTujuan);
            } else {
                $newNoUnit = $maxUnitTujuan + 1;
            }

            $this->distribusiModel->insert([
                'id_barang'        => $dist['id_barang'],
                'no_unit'          => $newNoUnit,
                'id_ruangan'       => $idRuanganBaru,
                'jumlah'           => $dist['jumlah'],
                'tanggal'          => $tanggal,
                'penanggung_jawab' => $penanggungBaru,
                'kondisi'          => $dist['kondisi'],
                'status'           => 'digunakan',
                'keterangan'       => $keterangan,
                'id_asal_distribusi' => $idDistribusi,
            ]);
            $berhasil++;
        }

        return redirect()->back()
            ->with('success', "$berhasil unit barang berhasil dialokasikan ke ruangan lain!");
    }
}

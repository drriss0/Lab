<?php

namespace App\Controllers;

use App\Models\BarangModel;
use App\Models\DistribusiModel;
use App\Models\PeminjamanModel;
use App\Security\Base64Image;
use App\Security\InventoryPolicy;

class Peminjaman extends BaseController
{
    protected BarangModel     $barangModel;
    protected DistribusiModel $distribusiModel;
    protected PeminjamanModel $peminjamanModel;

    public function __construct()
    {
        $this->barangModel     = new BarangModel();
        $this->distribusiModel = new DistribusiModel();
        $this->peminjamanModel = new PeminjamanModel();
    }


    // ============================================================
    // INDEX — Tampilkan cards barang pool peminjaman + tabel peminjaman
    // ============================================================

    public function index(): string|\CodeIgniter\HTTP\RedirectResponse
    {
        if ($redirect = $this->checkAuth()) return $redirect;
        if ($redirect = deny_if_not('admin', 'tu')) return $redirect;

        $keyword      = $this->request->getGet('q');
        $filterStatus = $this->request->getGet('status');

        // Hanya barang yang sudah didistribusikan untuk peminjaman (stok_peminjaman > 0)
        $barangList = $this->distribusiModel->getPoolPeminjaman();

        // Semua peminjaman (join barang)
        $allPeminjaman = $this->peminjamanModel->getAllWithBarang();

        // Apply server-side filter
        $peminjamanList = $allPeminjaman;

        if ($keyword) {
            $q = strtolower($keyword);
            $peminjamanList = array_filter($peminjamanList, function($p) use ($q) {
                return str_contains(strtolower($p['nama_peminjam'] ?? ''), $q)
                    || str_contains(strtolower($p['nama_barang'] ?? ''), $q)
                    || str_contains(strtolower($p['kode_barang'] ?? ''), $q)
                    || str_contains(strtolower($p['nim_nik'] ?? ''), $q);
            });
            $peminjamanList = array_values($peminjamanList);
        }

        if ($filterStatus) {
            $peminjamanList = array_filter($peminjamanList, fn($p) => $p['status'] === $filterStatus);
            $peminjamanList = array_values($peminjamanList);
        }

        // Stats (dari semua data, bukan yang difilter)
        $totalAktif     = 0;
        $totalKembali   = 0;
        $totalTerlambat = 0;
        $totalSedangDipinjam = 0;
        $hariIni = date('Y-m-d');
        $kemarin = date('Y-m-d', strtotime('-1 day'));
        foreach ($allPeminjaman as $p) {
            if (in_array($p['status'], ['dipinjam', 'terlambat'])) {
                $totalSedangDipinjam++;
            }
            if (in_array($p['status'], ['dipinjam'])) {
                $totalAktif++;
            } elseif ($p['status'] === 'kembali' && $p['tanggal_kembali'] === $hariIni) {
                $totalKembali++;
            } elseif ($p['status'] === 'terlambat' && $p['tenggat_kembali'] === $kemarin) {
                $totalTerlambat++;
            }
        }

        return view('peminjaman_barang/index', [
            'title'           => 'Peminjaman Barang — SIn-FTIK USM',
            'active_menu'     => 'peminjaman-barang',
            'barang_list'     => $barangList,
            'peminjaman'      => $peminjamanList,
            'keyword'         => $keyword,
            'filter_status'   => $filterStatus,
            'total_aktif'     => $totalAktif,
            'total_kembali'   => $totalKembali,
            'total_terlambat' => $totalTerlambat,
            'total_sedang_dipinjam' => $totalSedangDipinjam,
        ]);
    }

    // ============================================================
    // DETAIL — Rincian unit barang dalam pool peminjaman
    // ============================================================
    public function detail(string $hash): string|\CodeIgniter\HTTP\RedirectResponse
    {
        if ($redirect = $this->checkAuth()) return $redirect;
        if ($redirect = deny_if_not('admin', 'tu')) return $redirect;

        $idBarang = $this->decodeId($hash);
        if (!$idBarang) {
            return redirect()->to(base_url('peminjaman-barang'))->with('error', 'Link tidak valid.');
        }

        $barang = $this->barangModel->getDetail($idBarang);
        if (!$barang) {
            return redirect()->to(base_url('peminjaman-barang'))
                ->with('error', 'Barang tidak ditemukan.');
        }

        $barangList = $this->distribusiModel->getPoolPeminjamanDetail($idBarang);
        
        $activeCount = 0;
        $pj = '';
        foreach ($barangList as $b) {
            if ($b['status'] !== 'ditarik') {
                $activeCount++;
                if (empty($pj) && !empty($b['penanggung_jawab'])) {
                    $pj = $b['penanggung_jawab'];
                }
            }
        }

        return view('peminjaman_barang/detail', [
            'title'          => 'Detail Barang Peminjaman — SIn-FTIK USM',
            'active_menu'    => 'peminjaman-barang',
            'barang'         => $barang,
            'barang_list'    => $barangList,
            'activeCount'    => $activeCount,
            'penanggung_jawab' => $pj
        ]);
    }

    // ============================================================
    // UPDATE PJ — Ubah penanggung jawab pool peminjaman
    // ============================================================
    public function updatePj(string $hash): \CodeIgniter\HTTP\RedirectResponse
    {
        if ($redirect = $this->checkAuth()) return $redirect;
        if ($redirect = deny_if_not('admin', 'tu')) return $redirect;

        $idBarang = $this->decodeId($hash);
        if (!$idBarang) {
            return redirect()->to(base_url('peminjaman-barang'))->with('error', 'Link tidak valid.');
        }

        $pjBaru = $this->request->getPost('penanggung_jawab');
        if (!is_valid_keterangan($pjBaru)) {
            return redirect()->back()->with('error', 'Nama Penanggung Jawab tidak valid.');
        }

        $db = \Config\Database::connect();
        $db->table('distribusi')
           ->where('id_barang', $idBarang)
           ->where('tujuan_distribusi', 'peminjaman')
           ->whereNotIn('status', ['ditarik', 'digudang', 'pelaporan_rusak'])
           ->update(['penanggung_jawab' => $pjBaru]);

        return redirect()->back()->with('success', 'Penanggung Jawab berhasil diperbarui.');
    }

    // ============================================================
    // SIMPAN — Catat peminjaman baru (dari pool stok_peminjaman)
    // ============================================================
    public function simpan(string $hash): \CodeIgniter\HTTP\RedirectResponse
    {
        if ($redirect = $this->checkAuth()) return $redirect;
        if ($redirect = deny_if_not('admin', 'koordinator_lab')) return $redirect;

        $idBarang = $this->decodeId($hash);
        if (! $idBarang) return redirect()->to(base_url('peminjaman-barang'))->with('error', 'Link tidak valid.');

        $barang = $this->barangModel->find($idBarang);
        if (! $barang) {
            return redirect()->to(base_url('peminjaman-barang'))
                ->with('error', 'Barang tidak ditemukan.');
        }

        $jumlah         = (int) $this->request->getPost('jumlah');
        $stokPeminjaman = (int)($barang['stok_peminjaman'] ?? 0);

        if ($jumlah <= 0) {
            return redirect()->back()->with('error', 'Jumlah harus lebih dari 0.');
        }

        if ($jumlah > $stokPeminjaman) {
            return redirect()->back()
                ->with('error', 'Stok peminjaman tidak mencukupi. Tersedia: ' . $stokPeminjaman . ' pcs.');
        }

        // Upload foto dokumentasi (dari kamera - base64)
        $fotoNama = null;
        $fotoBase64 = $this->request->getPost('foto_base64');
        if ($fotoBase64) {
            try {
                $image = Base64Image::decodeDataUrl((string) $fotoBase64);
            } catch (\InvalidArgumentException $exception) {
                return redirect()->back()->with('error', $exception->getMessage());
            }

            $fotoNama = 'cam_' . bin2hex(random_bytes(16)) . '.' . $image['extension'];
            $written = file_put_contents(
                FCPATH . 'uploads/peminjaman/' . $fotoNama,
                $image['bytes'],
                LOCK_EX
            );
            if ($written !== strlen($image['bytes'])) {
                if (is_file(FCPATH . 'uploads/peminjaman/' . $fotoNama)) {
                    unlink(FCPATH . 'uploads/peminjaman/' . $fotoNama);
                }

                return redirect()->back()
                    ->with('error', 'Foto dokumentasi gagal disimpan.');
            }
        }

        $keterangan = $this->request->getPost('keterangan');
        if (!is_valid_keterangan($keterangan)) {
            // Hapus file yang terlanjur diupload jika validasi gagal
            if ($fotoNama) {
                $path = FCPATH . 'uploads/peminjaman/' . $fotoNama;
                if (is_file($path)) unlink($path);
            }
            return redirect()->back()->with('error', 'Keterangan tidak valid. Jika diisi, wajib mengandung minimal 3 karakter alfabet.');
        }

        $db = \Config\Database::connect();
        $db->transBegin();

        // Kurangi stok secara atomik; klausa stok mencegah oversubscription saat
        // dua request meminjam barang yang sama secara bersamaan.
        $db->table('barang')
            ->set('stok_peminjaman', 'stok_peminjaman - ' . $jumlah, false)
            ->set('jumlah_dipinjam', 'jumlah_dipinjam + ' . $jumlah, false)
            ->where('id', $idBarang)
            ->where('stok_peminjaman >=', $jumlah)
            ->update();

        if ($db->affectedRows() !== 1) {
            $db->transRollback();
            if ($fotoNama && is_file(FCPATH . 'uploads/peminjaman/' . $fotoNama)) {
                unlink(FCPATH . 'uploads/peminjaman/' . $fotoNama);
            }

            return redirect()->back()->with('error', 'Stok berubah atau tidak lagi mencukupi. Silakan coba kembali.');
        }

        $this->peminjamanModel->insert([
            'id_barang'        => $idBarang,
            'nama_peminjam'    => $this->request->getPost('nama_peminjam'),
            'nim_nik'          => $this->request->getPost('nim_nik'),
            'no_telp'          => $this->request->getPost('no_telp'),
            'jumlah'           => $jumlah,
            'tanggal_pinjam'   => $this->request->getPost('tanggal_pinjam'),
            'tenggat_kembali'  => $this->request->getPost('tenggat_kembali'),
            'status'           => 'dipinjam',
            'keterangan'       => $keterangan,
            'foto_dokumentasi' => $fotoNama,
        ]);

        if (! $db->transStatus()) {
            $db->transRollback();
            if ($fotoNama && is_file(FCPATH . 'uploads/peminjaman/' . $fotoNama)) {
                unlink(FCPATH . 'uploads/peminjaman/' . $fotoNama);
            }

            return redirect()->back()->with('error', 'Peminjaman gagal disimpan. Silakan coba kembali.');
        }

        $db->transCommit();

        return redirect()->to(base_url('peminjaman-barang'))
            ->with('success', 'Peminjaman berhasil dicatat!');
    }

    // ============================================================
    // EDIT — Update data peminjaman
    // ============================================================
    public function edit(string $hash): \CodeIgniter\HTTP\RedirectResponse
    {
        if ($redirect = $this->checkAuth()) return $redirect;
        if ($redirect = deny_if_not('admin', 'koordinator_lab')) return $redirect;

        $id = decode_id($hash);
        $pinjam = $this->peminjamanModel->find($id);
        if (! $pinjam) return redirect()->to(base_url('peminjaman-barang'));

        $jumlahBaru     = (int) $this->request->getPost('jumlah');
        $statusBaru     = $this->request->getPost('status');
        $idBarang       = $pinjam['id_barang'];
        $barang         = $this->barangModel->find($idBarang);

        if (! InventoryPolicy::isPositiveQuantity($jumlahBaru)) {
            return redirect()->back()->withInput()
                ->with('error', 'Jumlah peminjaman harus lebih dari 0.');
        }

        if (! InventoryPolicy::isLoanStatus((string) $statusBaru)) {
            return redirect()->back()->withInput()
                ->with('error', 'Status peminjaman tidak valid.');
        }

        if (! $barang) {
            return redirect()->back()->with('error', 'Barang tidak ditemukan.');
        }

        $selisih        = $jumlahBaru - (int)$pinjam['jumlah'];
        $stokPeminjaman = (int)($barang['stok_peminjaman'] ?? 0);

        if ($pinjam['status'] === 'kembali' && $statusBaru !== 'kembali'
            && $jumlahBaru > $stokPeminjaman) {
            return redirect()->back()
                ->with('error', 'Stok peminjaman tidak mencukupi untuk reaktivasi. Tersedia: ' . $stokPeminjaman);
        }

        if ($pinjam['status'] !== 'kembali' && $statusBaru !== 'kembali'
            && $selisih > $stokPeminjaman) {
            return redirect()->back()
                ->with('error', 'Stok peminjaman tidak mencukupi untuk penambahan. Tersedia: ' . $stokPeminjaman);
        }

        $keterangan = $this->request->getPost('keterangan');
        if (!is_valid_keterangan($keterangan)) {
            return redirect()->back()->withInput()
                ->with('error', 'Keterangan tidak valid. Jika diisi, wajib mengandung minimal 3 karakter alfabet.');
        }

        // Handle foto upload
        $fotoLama = $pinjam['foto_dokumentasi'];
        $fotoNama = $fotoLama;
        $fotoBaruDiunggah = false;
        $foto = $this->request->getFile('foto_dokumentasi');
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
            $fotoNama = $foto->getRandomName();
            $foto->move(FCPATH . 'uploads/peminjaman', $fotoNama);
            $fotoBaruDiunggah = true;
        }

        $db = \Config\Database::connect();
        $db->transStart();

        // Kelola perubahan stok berdasarkan perubahan status
        if ($pinjam['status'] !== 'kembali' && $statusBaru === 'kembali') {
            // Dipinjam → Kembali: kembalikan ke pool stok_peminjaman
            $this->barangModel->update($idBarang, [
                'stok_peminjaman' => $stokPeminjaman + (int)$pinjam['jumlah'],
                'jumlah_dipinjam' => max(0, (int)$barang['jumlah_dipinjam'] - (int)$pinjam['jumlah']),
            ]);
            $jumlahBaru = (int)$pinjam['jumlah'];
        } elseif ($pinjam['status'] === 'kembali' && $statusBaru !== 'kembali') {
            // Kembali → Dipinjam lagi: kurangi dari pool
            $this->barangModel->update($idBarang, [
                'stok_peminjaman' => $stokPeminjaman - $jumlahBaru,
                'jumlah_dipinjam' => (int)$barang['jumlah_dipinjam'] + $jumlahBaru,
            ]);
        } elseif ($pinjam['status'] !== 'kembali' && $selisih !== 0) {
            // Perubahan jumlah saat masih aktif
            $this->barangModel->update($idBarang, [
                'stok_peminjaman' => $stokPeminjaman - $selisih,
                'jumlah_dipinjam' => max(0, (int)$barang['jumlah_dipinjam'] + $selisih),
            ]);
        }

        $this->peminjamanModel->update($id, [
            'nama_peminjam'    => $this->request->getPost('nama_peminjam'),
            'nim_nik'          => $this->request->getPost('nim_nik'),
            'no_telp'          => $this->request->getPost('no_telp'),
            'jumlah'           => $jumlahBaru,
            'tanggal_pinjam'   => $this->request->getPost('tanggal_pinjam'),
            'tenggat_kembali'  => $this->request->getPost('tenggat_kembali'),
            'tanggal_kembali'  => $statusBaru === 'kembali' ? date('Y-m-d') : null,
            'status'           => $statusBaru,
            'keterangan'       => $keterangan,
            'foto_dokumentasi' => $fotoNama,
        ]);

        $db->transComplete();
        if (! $db->transStatus()) {
            if ($fotoBaruDiunggah && file_exists(FCPATH . 'uploads/peminjaman/' . $fotoNama)) {
                @unlink(FCPATH . 'uploads/peminjaman/' . $fotoNama);
            }

            return redirect()->back()->with('error', 'Gagal memperbarui peminjaman. Silakan coba lagi.');
        }

        // Hapus foto lama hanya setelah transaksi database berhasil agar kegagalan
        // update tidak meninggalkan record yang menunjuk ke file yang sudah hilang.
        if ($fotoBaruDiunggah && $fotoLama
            && file_exists(FCPATH . 'uploads/peminjaman/' . $fotoLama)) {
            @unlink(FCPATH . 'uploads/peminjaman/' . $fotoLama);
        }

        return redirect()->to(base_url('peminjaman-barang'))
            ->with('success', 'Data peminjaman berhasil diperbarui!');
    }

    // ============================================================
    // KEMBALI — Tandai barang dikembalikan ke pool
    // ============================================================
    public function kembali(string $hash): \CodeIgniter\HTTP\RedirectResponse
    {
        if ($redirect = $this->checkAuth()) return $redirect;
        if ($redirect = deny_if_not('admin', 'koordinator_lab')) return $redirect;

        $id = decode_id($hash);
        $pinjam = $this->peminjamanModel->find($id);
        if (! $pinjam) return redirect()->to(base_url('peminjaman-barang'));

        if (! InventoryPolicy::isReturnableLoanStatus((string) $pinjam['status'])) {
            return redirect()->to(base_url('peminjaman-barang'))
                ->with('error', 'Peminjaman ini sudah dikembalikan atau tidak dapat dikembalikan lagi.');
        }

        $idBarang = $pinjam['id_barang'];
        $barang   = $this->barangModel->find($idBarang);
        $jumlah   = (int)$pinjam['jumlah'];

        if (! $barang || ! InventoryPolicy::isPositiveQuantity($jumlah)) {
            return redirect()->to(base_url('peminjaman-barang'))
                ->with('error', 'Data peminjaman tidak valid.');
        }

        $db = \Config\Database::connect();
        $db->transBegin();
        $db->table('peminjaman')
            ->where('id', $id)
            ->whereIn('status', InventoryPolicy::ACTIVE_LOAN_STATUSES)
            ->update([
            'status'          => 'kembali',
            'tanggal_kembali' => date('Y-m-d'),
        ]);

        if ($db->affectedRows() !== 1) {
            $db->transRollback();

            return redirect()->to(base_url('peminjaman-barang'))
                ->with('error', 'Peminjaman sudah diproses oleh permintaan lain.');
        }

        // Kembalikan ke pool stok_peminjaman (bukan stok_gudang)
        $db->table('barang')
            ->set('stok_peminjaman', 'stok_peminjaman + ' . $jumlah, false)
            ->set('jumlah_dipinjam', 'GREATEST(0, jumlah_dipinjam - ' . $jumlah . ')', false)
            ->where('id', $idBarang)
            ->update();

        if (! $db->transStatus()) {
            $db->transRollback();

            return redirect()->to(base_url('peminjaman-barang'))
                ->with('error', 'Gagal memproses pengembalian. Silakan coba lagi.');
        }

        $db->transCommit();

        return redirect()->to(base_url('peminjaman-barang'))
            ->with('success', 'Barang berhasil ditandai kembali ke pool peminjaman!');
    }

    // ============================================================
    // HAPUS — Hapus record peminjaman
    // ============================================================
    public function hapus(string $hash): \CodeIgniter\HTTP\RedirectResponse
    {
        if ($redirect = $this->checkAuth()) return $redirect;
        if ($redirect = deny_if_not('admin', 'koordinator_lab')) return $redirect;

        $id = decode_id($hash);
        $pinjam = $this->peminjamanModel->find($id);
        if (! $pinjam) return redirect()->to(base_url('peminjaman-barang'));

        $idBarang = $pinjam['id_barang'];

        // Kembalikan stok_peminjaman jika masih aktif
        if (in_array($pinjam['status'], ['dipinjam', 'terlambat'])) {
            $barang = $this->barangModel->find($idBarang);
            $this->barangModel->update($idBarang, [
                'stok_peminjaman' => (int)($barang['stok_peminjaman'] ?? 0) + (int)$pinjam['jumlah'],
                'jumlah_dipinjam' => max(0, (int)$barang['jumlah_dipinjam'] - (int)$pinjam['jumlah']),
            ]);
        }

        // Hapus foto dokumentasi
        if (!empty($pinjam['foto_dokumentasi'])) {
            $path = FCPATH . 'uploads/peminjaman/' . $pinjam['foto_dokumentasi'];
            if (is_file($path)) unlink($path);
        }

        $this->peminjamanModel->delete($id);

        return redirect()->to(base_url('peminjaman-barang'))
            ->with('success', 'Data peminjaman berhasil dihapus!');
    }

    // ============================================================
    // BULK KEMBALI — Tandai kembali massal
    // ============================================================
    public function bulkKembali(): \CodeIgniter\HTTP\RedirectResponse
    {
        if ($redirect = $this->checkAuth()) return $redirect;
        if ($redirect = deny_if_not('admin', 'koordinator_lab')) return $redirect;

        $ids = $this->request->getPost('id_peminjaman');
        if (empty($ids) || !is_array($ids)) {
            return redirect()->back()->with('error', 'Tidak ada data peminjaman yang dipilih.');
        }
        try {
            $ids = $this->normalizeBulkIds($ids);
        } catch (\InvalidArgumentException $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }

        $db = \Config\Database::connect();
        $db->transBegin();
        $berhasil = 0;
        foreach ($ids as $hash) {
            $id = decode_id($hash);
            if (!$id) continue;

            $pinjam = $this->peminjamanModel->find($id);
            if (! $pinjam || ! InventoryPolicy::isReturnableLoanStatus((string) $pinjam['status'])) continue;

            $idBarang = (int) $pinjam['id_barang'];
            $jumlah   = (int) $pinjam['jumlah'];
            if (! InventoryPolicy::isPositiveQuantity($jumlah)) continue;

            // State transition bersyarat membuat request replay/concurrent hanya
            // dapat mengkredit stok satu kali.
            $db->table('peminjaman')
                ->where('id', $id)
                ->whereIn('status', InventoryPolicy::ACTIVE_LOAN_STATUSES)
                ->update([
                    'status'          => 'kembali',
                    'tanggal_kembali' => date('Y-m-d'),
                ]);

            if ($db->affectedRows() !== 1) continue;

            $db->table('barang')
                ->set('stok_peminjaman', 'stok_peminjaman + ' . $jumlah, false)
                ->set('jumlah_dipinjam', 'GREATEST(0, jumlah_dipinjam - ' . $jumlah . ')', false)
                ->where('id', $idBarang)
                ->update();

            $berhasil++;
        }

        if (! $db->transStatus()) {
            $db->transRollback();

            return redirect()->back()->with('error', 'Pengembalian massal gagal diproses.');
        }

        $db->transCommit();

        return redirect()->back()->with('success', "$berhasil data peminjaman berhasil ditandai kembali.");
    }

    // ============================================================
    // BULK HAPUS — Hapus massal riwayat
    // ============================================================
    public function bulkHapus(): \CodeIgniter\HTTP\RedirectResponse
    {
        if ($redirect = $this->checkAuth()) return $redirect;
        if ($redirect = deny_if_not('admin', 'koordinator_lab')) return $redirect;

        $ids = $this->request->getPost('id_peminjaman');
        if (empty($ids) || !is_array($ids)) {
            return redirect()->back()->with('error', 'Tidak ada data peminjaman yang dipilih.');
        }
        try {
            $ids = $this->normalizeBulkIds($ids);
        } catch (\InvalidArgumentException $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }

        $berhasil = 0;
        foreach ($ids as $hash) {
            $id = decode_id($hash);
            if (!$id) continue;
            
            $pinjam = $this->peminjamanModel->find($id);
            if (!$pinjam) continue;
            
            $idBarang = $pinjam['id_barang'];
            
            if (in_array($pinjam['status'], ['dipinjam', 'terlambat'])) {
                $barang = $this->barangModel->find($idBarang);
                $this->barangModel->update($idBarang, [
                    'stok_peminjaman' => (int)($barang['stok_peminjaman'] ?? 0) + (int)$pinjam['jumlah'],
                    'jumlah_dipinjam' => max(0, (int)$barang['jumlah_dipinjam'] - (int)$pinjam['jumlah']),
                ]);
            }
            
            if (!empty($pinjam['foto_dokumentasi'])) {
                $path = FCPATH . 'uploads/peminjaman/' . $pinjam['foto_dokumentasi'];
                if (is_file($path)) unlink($path);
            }
            
            $this->peminjamanModel->delete($id);
            $berhasil++;
        }

        return redirect()->back()->with('success', "$berhasil data peminjaman berhasil dihapus secara permanen.");
    }
}

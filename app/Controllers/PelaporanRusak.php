<?php

namespace App\Controllers;

use App\Models\BarangModel;
use App\Models\PelaporanModel;

class PelaporanRusak extends BaseController
{
    protected BarangModel    $barangModel;
    protected PelaporanModel $pelaporanModel;

    public function __construct()
    {
        $this->barangModel    = new BarangModel();
        $this->pelaporanModel = new PelaporanModel();
    }

    /**
     * Halaman utama: daftar semua laporan barang rusak dari semua barang.
     */
    public function index(): string|\CodeIgniter\HTTP\RedirectResponse
    {
        if ($redirect = $this->checkAuth()) return $redirect;
        if ($redirect = deny_if_not('admin', 'tu')) return $redirect;

        $db      = \Config\Database::connect();
        $keyword = $this->request->getGet('q');
        $status  = $this->request->getGet('status');
        $tingkat = $this->request->getGet('tingkat');

        // Query semua pelaporan rusak lengkap dengan nama barang & ruangan
        $builder = $db->table('pelaporan_rusak p')
            ->select('p.*, b.nama_barang, b.kode_barang, b.merk,
                      k.nama AS nama_kategori,
                      r.nama_ruangan,
                      d.no_unit, d.tujuan_distribusi')
            ->join('barang b',          'b.id = p.id_barang',       'left')
            ->join('jenis_barang k', 'k.id = b.id_kategori',     'left')
            ->join('ruangan r',         'r.id = p.id_ruangan',      'left')
            ->join('distribusi d',      'd.id = p.id_distribusi',   'left');

        if ($keyword) {
            $builder->groupStart()
                ->like('b.nama_barang', $keyword)
                ->orLike('b.kode_barang', $keyword)
                ->orLike('r.nama_ruangan', $keyword)
                ->groupEnd();
        }

        if ($status) {
            $builder->where('p.status', $status);
        }

        if ($tingkat) {
            $builder->where('p.tingkat_kerusakan', $tingkat);
        }

        $builder->orderBy('p.tanggal_lapor', 'DESC');
        $laporanList = $builder->get()->getResultArray();

        // Stats ringkasan (jangan hitung yang sudah dikembalikan)
        $statRow = $db->table('pelaporan_rusak')->select('
            COUNT(*) AS total_laporan,
            SUM(jumlah) AS total_unit,
            SUM(CASE WHEN status="sedang_diperbaiki" THEN jumlah ELSE 0 END) AS total_proses,
            SUM(CASE WHEN status="selesai" THEN jumlah ELSE 0 END) AS total_selesai,
            SUM(CASE WHEN tingkat_kerusakan="rusak_berat" THEN jumlah ELSE 0 END) AS total_berat,
            SUM(CASE WHEN tingkat_kerusakan="rusak_ringan" THEN jumlah ELSE 0 END) AS total_ringan
        ')->where('status !=', 'dikembalikan')->get()->getRowArray();

        return view('pelaporan_rusak/index', [
            'title'          => 'Pelaporan Barang Rusak — SIn-FTIK USM',
            'active_menu'    => 'pelaporan-rusak',
            'laporan_list'   => $laporanList,
            'keyword'        => $keyword,
            'filter_status'  => $status,
            'filter_tingkat' => $tingkat,
            'stats'          => [
                'total_laporan' => (int) ($statRow['total_laporan'] ?? 0),
                'total_unit'    => (int) ($statRow['total_unit']    ?? 0),
                'total_proses'  => (int) ($statRow['total_proses']  ?? 0),
                'total_selesai' => (int) ($statRow['total_selesai'] ?? 0),
                'total_berat'   => (int) ($statRow['total_berat']   ?? 0),
                'total_ringan'  => (int) ($statRow['total_ringan']  ?? 0),
            ],
        ]);
    }

    /**
     * Update status penanganan laporan.
     */
    public function updateStatus(string $hash): \CodeIgniter\HTTP\RedirectResponse
    {
        if ($redirect = $this->checkAuth()) return $redirect;
        if ($redirect = deny_if_not('admin', 'koordinator_lab')) return $redirect;
        $id = $this->decodeId($hash);
        if (! $id) return redirect()->to(base_url('pelaporan-rusak'))->with('error', 'Link tidak valid.');

        $laporan = $this->pelaporanModel->find($id);
        if (! $laporan) {
            return redirect()->to(base_url('pelaporan-rusak'))
                ->with('error', 'Data laporan tidak ditemukan.');
        }

        $statusBaru = $this->request->getPost('status');
        $allowed    = ['sedang_diperbaiki', 'selesai', 'tidak_bisa_diperbaiki'];
        if (! in_array($statusBaru, $allowed)) {
            return redirect()->back()->with('error', 'Status tidak valid.');
        }

        $this->pelaporanModel->update($id, ['status' => $statusBaru]);

        $label = match ($statusBaru) {
            'sedang_diperbaiki'     => 'Sedang Diperbaiki',
            'selesai'               => 'Selesai',
            'tidak_bisa_diperbaiki' => 'Tidak Bisa Diperbaiki',
            default                 => $statusBaru,
        };

        return redirect()->to(base_url('pelaporan-rusak'))
            ->with('success', "Status laporan berhasil diubah menjadi \"$label\".");
    }

    /**
     * Hapus laporan rusak.
     */
    public function hapus(string $hash): \CodeIgniter\HTTP\RedirectResponse
    {
        if ($redirect = $this->checkAuth()) return $redirect;
        if ($redirect = deny_if_not('admin', 'koordinator_lab')) return $redirect;
        $id = $this->decodeId($hash);
        if (! $id) return redirect()->to(base_url('pelaporan-rusak'))->with('error', 'Link tidak valid.');

        $laporan = $this->pelaporanModel->find($id);
        if (! $laporan) {
            return redirect()->to(base_url('pelaporan-rusak'))
                ->with('error', 'Data laporan tidak ditemukan.');
        }

        $this->revertLaporanStok($laporan);
        $this->revertLaporanDistribusi($laporan);
        $this->pelaporanModel->delete($id);

        return redirect()->to(base_url('pelaporan-rusak'))
            ->with('success', 'Laporan berhasil dihapus.');
    }

    /**
     * Kembalikan barang ke ruangan setelah selesai diperbaiki.
     */
    public function kembaliKeRuangan(string $hash): \CodeIgniter\HTTP\RedirectResponse
    {
        if ($redirect = $this->checkAuth()) return $redirect;
        if ($redirect = deny_if_not('admin', 'koordinator_lab')) return $redirect;
        $id = $this->decodeId($hash);
        if (! $id) return redirect()->to(base_url('pelaporan-rusak'))->with('error', 'Link tidak valid.');

        $laporan = $this->pelaporanModel->find($id);
        if (! $laporan) {
            return redirect()->to(base_url('pelaporan-rusak'))
                ->with('error', 'Data laporan tidak ditemukan.');
        }

        if ($laporan['status'] !== 'selesai') {
            return redirect()->to(base_url('pelaporan-rusak'))
                ->with('error', 'Hanya barang yang sudah selesai diperbaiki yang dapat dikembalikan ke ruangan.');
        }

        $this->prosesKembaliKeRuangan($laporan);
        $this->pelaporanModel->update($id, ['status' => 'dikembalikan']);

        return redirect()->to(base_url('pelaporan-rusak'))
            ->with('success', 'Barang berhasil dikembalikan ke ruangan dan siap digunakan.');
    }

    /**
     * Aksi masal untuk laporan terpilih.
     */
    public function bulkAction(): \CodeIgniter\HTTP\RedirectResponse
    {
        if ($redirect = $this->checkAuth()) return $redirect;
        if ($redirect = deny_if_not('admin', 'koordinator_lab')) return $redirect;

        $ids    = $this->request->getPost('laporan_ids');
        $action = $this->request->getPost('bulk_action');

        if (empty($ids) || ! is_array($ids) || empty($action)) {
            return redirect()->back()->with('error', 'Pilih minimal satu laporan dan tentukan aksinya.');
        }
        try {
            $ids = $this->normalizeBulkIds($ids);
        } catch (\InvalidArgumentException $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }

        $berhasil = 0;
        $gagal    = 0;

        foreach ($ids as $hash) {
            $id = $this->decodeId((string)$hash);
            if (! $id) continue;
            
            $laporan = $this->pelaporanModel->find($id);
            if (! $laporan) continue;

            if ($action === 'selesai' || $action === 'tidak_bisa_diperbaiki') {
                if ($laporan['status'] === 'sedang_diperbaiki') {
                    $this->pelaporanModel->update($id, ['status' => $action]);
                    

                    
                    $berhasil++;
                } else {
                    $gagal++;
                }
            } elseif ($action === 'kembali') {
                if ($laporan['status'] === 'selesai') {
                    $this->prosesKembaliKeRuangan($laporan);
                    $this->pelaporanModel->update($id, ['status' => 'dikembalikan']);
                    $berhasil++;
                } else {
                    $gagal++;
                }
            } elseif ($action === 'hapus') {
                $this->revertLaporanStok($laporan);
                $this->revertLaporanDistribusi($laporan);
                $this->pelaporanModel->delete($id);
                $berhasil++;
            }
        }

        $actionLabels = [
            'selesai'               => 'menyelesaikan perbaikan',
            'tidak_bisa_diperbaiki' => 'menandai tidak bisa diperbaiki pada',
            'kembali'               => 'mengembalikan barang ke ruangan dari',
            'hapus'                 => 'menghapus',
        ];

        $actionText = $actionLabels[$action] ?? 'memproses';
        $msg        = "Berhasil $actionText $berhasil data laporan.";

        if ($gagal > 0) {
            $msg .= " ($gagal data diabaikan karena status tidak sesuai).";
        }

        return redirect()->back()->with($berhasil > 0 ? 'success' : 'error', $msg);
    }

    // ============================================================
    // Private Helpers
    // ============================================================

    /**
     * Proses pengembalian barang ke ruangan (shared antara kembaliKeRuangan & bulkAction).
     */
    private function prosesKembaliKeRuangan(array $laporan): void
    {
        $db = \Config\Database::connect();

        // Ambil data distribusi lama untuk fallback penanggung jawab
        $distLama = null;
        if (! empty($laporan['id_distribusi'])) {
            $distLama = $db->table('distribusi')->where('id', $laporan['id_distribusi'])->get()->getRowArray();
        }

        $ruangan    = $db->table('ruangan')->where('id', $laporan['id_ruangan'])->get()->getRowArray();
        $penanggung = $ruangan['penanggung_jawab'] ?? ($distLama['penanggung_jawab'] ?? null);

        // Update distribusi menjadi digunakan kembali
        if (! empty($laporan['id_distribusi'])) {
            $db->table('distribusi')
               ->where('id', $laporan['id_distribusi'])
               ->update([
                   'id_ruangan'       => $laporan['id_ruangan'],
                   'penanggung_jawab' => $penanggung,
                   'kondisi'          => 'diperbaiki',
                   'status'           => 'digunakan',
               ]);
        }

        // Update stok barang
        $barang = $this->barangModel->find($laporan['id_barang']);
        if (! $barang) return;

        $isPeminjaman = false;
        if (! empty($laporan['id_distribusi'])) {
            // Baca ulang data distribusi setelah update di atas
            $distInfo = $db->table('distribusi')->where('id', $laporan['id_distribusi'])->get()->getRowArray();
            if ($distInfo && ($distInfo['tujuan_distribusi'] ?? '') === 'peminjaman') {
                $isPeminjaman = true;
            }
        }

        $updateData = [
            'jumlah_rusak' => max(0, (int) $barang['jumlah_rusak'] - (int) $laporan['jumlah']),
        ];

        if ($isPeminjaman) {
            $updateData['stok_peminjaman'] = (int) ($barang['stok_peminjaman'] ?? 0) + (int) $laporan['jumlah'];
        } else {
            $updateData['jumlah_digunakan'] = (int) $barang['jumlah_digunakan'] + (int) $laporan['jumlah'];
        }

        $db->table('barang')->where('id', $laporan['id_barang'])->update($updateData);
    }

    /**
     * Kembalikan stok barang saat laporan dihapus.
     */
    private function revertLaporanStok(array $laporan): void
    {
        $barang = $this->barangModel->find($laporan['id_barang']);
        if (! $barang) return;

        $updateBarang = [];

        if ($laporan['status'] !== 'dikembalikan') {
            $updateBarang['jumlah_rusak'] = max(0, (int) $barang['jumlah_rusak'] - (int) $laporan['jumlah']);
        }

        if (in_array($laporan['status'], ['menunggu', 'sedang_diperbaiki', 'selesai'])) {
            $updateBarang['stok_gudang'] = (int) $barang['stok_gudang'] + (int) $laporan['jumlah'];
        } elseif ($laporan['status'] === 'tidak_bisa_diperbaiki') {
            $updateBarang['stok_awal'] = max(0, (int) $barang['stok_awal'] - (int) $laporan['jumlah']);
        }

        if (! empty($updateBarang)) {
            $this->barangModel->update($laporan['id_barang'], $updateBarang);
        }
    }

    /**
     * Revert data distribusi saat laporan dihapus.
     */
    private function revertLaporanDistribusi(array $laporan): void
    {
        if (empty($laporan['id_distribusi'])) return;

        $db   = \Config\Database::connect();
        $dist = $db->table('distribusi')->where('id', $laporan['id_distribusi'])->get()->getRowArray();
        if (! $dist) return;

        if ($laporan['status'] === 'tidak_bisa_diperbaiki') {
            // Rusak parah — hapus distribusi secara permanen
            $db->table('distribusi')->where('id', $dist['id'])->delete();
        } elseif ($laporan['status'] !== 'dikembalikan') {
            // Belum dikembalikan — kembalikan status ke gudang
            $db->table('distribusi')->where('id', $dist['id'])->update([
                'no_unit' => 0,
                'status'  => 'digudang',
            ]);
        }
        // Jika sudah 'dikembalikan' — hanya riwayat laporan yang dihapus, distribusi dibiarkan
    }
}

<?php

namespace App\Controllers;

class Pengajuan extends BaseController
{
    private function db(): \CodeIgniter\Database\BaseConnection
    {
        return \Config\Database::connect();
    }

    public function index(): string|\CodeIgniter\HTTP\RedirectResponse
    {
        if ($redirect = $this->checkAuth()) return $redirect;
        if ($redirect = deny_if_not('admin', 'dekan', 'koordinator_lab', 'tu')) return $redirect;

        $db = $this->db();

        $keyword  = $this->request->getGet('q');
        $status   = $this->request->getGet('status');
        $kategori = $this->request->getGet('kategori');

        $builder = $db->table('pengajuan_inventaris')
            ->orderBy("FIELD(urgensi, 'tinggi', 'sedang', 'rendah')")
            ->orderBy('created_at', 'DESC');

        if ($keyword) {
            $builder->groupStart()
                ->like('nama_barang', $keyword)
                ->orLike('nama_pengaju', $keyword)
                ->groupEnd();
        }
        if ($status) {
            $builder->where('status', $status);
        }
        if ($kategori) {
            $builder->where('kategori', $kategori);
        }

        $list = $builder->get()->getResultArray();

        // Hitung stats global (tanpa filter)
        $allList  = $db->table('pengajuan_inventaris')->get()->getResultArray();
        $total    = count($allList);
        $menunggu = count(array_filter($allList, fn($r) => in_array($r['status'], ['menunggu', 'direvisi'])));
        $diproses = count(array_filter($allList, fn($r) => $r['status'] === 'diproses'));
        $selesai  = count(array_filter($allList, fn($r) => $r['status'] === 'selesai'));
        $ditolak  = count(array_filter($allList, fn($r) => $r['status'] === 'ditolak'));

        // Daftar kategori unik (kolom sudah ada di tabel sekarang)
        $rows = $db->query("SELECT DISTINCT kategori FROM pengajuan_inventaris WHERE kategori IS NOT NULL AND kategori != '' ORDER BY kategori ASC")->getResultArray();
        $kategoriList = array_column($rows, 'kategori');

        // Daftar jurusan dari database
        $jurusanList = $db->table('jurusan')->orderBy('nama_jurusan', 'ASC')->get()->getResultArray();

        return view('pengajuan/index', [
            'title'           => 'Pengajuan Inventaris — SIn-FTIK USM',
            'active_menu'     => 'pengajuan',
            'list'            => $list,
            'keyword'         => $keyword,
            'filter_status'   => $status,
            'filter_kategori' => $kategori,
            'kategori_list'   => $kategoriList,
            'jurusan_list'    => $jurusanList,
            'stats'           => compact('total', 'menunggu', 'diproses', 'selesai', 'ditolak'),
        ]);
    }

    public function simpan(): \CodeIgniter\HTTP\RedirectResponse
    {
        if ($redirect = $this->checkAuth()) return $redirect;
        if ($redirect = deny_if_not('admin')) return $redirect;

        $keterangan = $this->request->getPost('keterangan');
        if (!is_valid_keterangan($keterangan)) {
            return redirect()->back()->withInput()->with('error', 'Keterangan/Alasan pengajuan tidak valid. Jika diisi, wajib mengandung minimal 3 karakter alfabet.');
        }

        $namaPengaju = $this->request->getPost('nama_pengaju');
        if (empty(trim((string)$namaPengaju))) {
            return redirect()->back()->withInput()->with('error', 'Nama pengaju wajib diisi.');
        }

        $nimNik = $this->request->getPost('nim_nik');
        if (empty(trim((string)$nimNik))) {
            return redirect()->back()->withInput()->with('error', 'NIP / NIDN wajib diisi.');
        }

        $this->db()->table('pengajuan_inventaris')->insert([
            'nama_pengaju'       => trim($namaPengaju),
            'nim_nik'            => trim((string)$nimNik),
            'nama_barang'        => $this->request->getPost('nama_barang'),
            'kategori_pengajuan' => $this->request->getPost('kategori_pengajuan'),
            'kebutuhan_jurusan'  => $this->request->getPost('kebutuhan_jurusan'),
            'kategori'           => $this->request->getPost('kategori'),
            'jumlah'             => (int) $this->request->getPost('jumlah'),
            'urgensi'            => $this->request->getPost('urgensi') ?: 'sedang',
            'keterangan'         => $keterangan,
            'tanggal'            => date('Y-m-d'),
            'status'             => 'menunggu',
        ]);

        return redirect()->to(base_url('pengajuan'))
            ->with('success', 'Pengajuan inventaris berhasil dikirim!');
    }

    /**
     * Edit — hanya jika status masih 'menunggu'
     */
    public function edit(string $hash): \CodeIgniter\HTTP\RedirectResponse
    {
        if ($redirect = $this->checkAuth()) return $redirect;
        if ($redirect = deny_if_not('admin')) return $redirect;
        $id = $this->decodeId($hash);
        if (! $id) return redirect()->to(base_url('pengajuan'))->with('error', 'Link tidak valid.');

        $pengajuan = $this->db()->table('pengajuan_inventaris')->where('id', $id)->get()->getRowArray();

        if (! $pengajuan) {
            return redirect()->to(base_url('pengajuan'))
                ->with('error', 'Data pengajuan tidak ditemukan.');
        }

        if (!in_array($pengajuan['status'], ['menunggu', 'direvisi'])) {
            return redirect()->to(base_url('pengajuan'))
                ->with('error', 'Pengajuan yang sudah diproses tidak dapat diedit.');
        }

        $keterangan = $this->request->getPost('keterangan');
        if (!is_valid_keterangan($keterangan)) {
            return redirect()->back()->withInput()->with('error', 'Keterangan/Alasan pengajuan tidak valid. Jika diisi, wajib mengandung minimal 3 karakter alfabet.');
        }

        $namaPengaju = $this->request->getPost('nama_pengaju');
        if (empty(trim((string)$namaPengaju))) {
            return redirect()->back()->withInput()->with('error', 'Nama pengaju wajib diisi.');
        }

        $nimNik = $this->request->getPost('nim_nik');
        if (empty(trim((string)$nimNik))) {
            return redirect()->back()->withInput()->with('error', 'NIP / NIDN wajib diisi.');
        }

        $this->db()->table('pengajuan_inventaris')->where('id', $id)->update([
            'nama_pengaju'       => trim($namaPengaju),
            'nim_nik'            => trim((string)$nimNik),
            'nama_barang'        => $this->request->getPost('nama_barang'),
            'kategori_pengajuan' => $this->request->getPost('kategori_pengajuan'),
            'kebutuhan_jurusan'  => $this->request->getPost('kebutuhan_jurusan'),
            'kategori'           => $this->request->getPost('kategori'),
            'jumlah'             => (int)$this->request->getPost('jumlah'),
            'urgensi'        => $this->request->getPost('urgensi'),
            'keterangan'     => $keterangan,
            'status'         => 'menunggu',       // reset ke menunggu setelah diedit
            'updated_at'     => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to(base_url('pengajuan'))
            ->with('success', 'Pengajuan berhasil diperbarui dan dikembalikan ke antrian.');
    }

    public function updateStatus(string $hash): \CodeIgniter\HTTP\RedirectResponse
    {
        if ($redirect = $this->checkAuth()) return $redirect;
        if ($redirect = deny_if_not('admin', 'koordinator_lab')) return $redirect;
        $id = $this->decodeId($hash);
        if (! $id) return redirect()->to(base_url('pengajuan'))->with('error', 'Link tidak valid.');

        $db = $this->db();
        $pengajuan = $db->table('pengajuan_inventaris')->where('id', $id)->get()->getRowArray();

        if (! $pengajuan) {
            return redirect()->to(base_url('pengajuan'))->with('error', 'Data tidak ditemukan.');
        }

        if (in_array($pengajuan['status'], ['selesai', 'ditolak'])) {
            return redirect()->to(base_url('pengajuan'))
                ->with('error', 'Pengajuan ini sudah final (Selesai/Ditolak) dan tidak dapat diubah statusnya lagi.');
        }

        // Whitelist status yang diizinkan
        $allowedStatus = ['menunggu', 'diproses', 'selesai', 'direvisi', 'ditolak'];
        $statusBaru = $this->request->getPost('status');
        if (! in_array($statusBaru, $allowedStatus)) {
            return redirect()->back()->with('error', 'Status tidak valid.');
        }

        $db->table('pengajuan_inventaris')
            ->where('id', $id)
            ->update([
                'status'     => $statusBaru,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

        $msg = 'Status pengajuan berhasil diperbarui!';
        if ($statusBaru === 'selesai') {
            $msg = 'Pengajuan berhasil disetujui.';
        } elseif ($statusBaru === 'ditolak') {
            $msg = 'Pengajuan telah ditolak.';
        } elseif ($statusBaru === 'diproses') {
            $msg = 'Pengajuan sedang diproses.';
        }

        return redirect()->to(base_url('pengajuan'))
            ->with('success', $msg);
    }

    public function revisi(string $hash): \CodeIgniter\HTTP\RedirectResponse
    {
        if ($redirect = $this->checkAuth()) return $redirect;
        if ($redirect = deny_if_not('admin', 'koordinator_lab')) return $redirect;
        $id = $this->decodeId($hash);
        if (! $id) return redirect()->to(base_url('pengajuan'))->with('error', 'Link tidak valid.');

        $db = $this->db();
        $pengajuan = $db->table('pengajuan_inventaris')->where('id', $id)->get()->getRowArray();

        if (! $pengajuan) {
            return redirect()->to(base_url('pengajuan'))->with('error', 'Data tidak ditemukan.');
        }

        $db->table('pengajuan_inventaris')
            ->where('id', $id)
            ->update([
                'status'         => 'direvisi',
                'catatan_revisi' => $this->request->getPost('catatan_revisi'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ]);

        return redirect()->to(base_url('pengajuan'))
            ->with('success', 'Pengajuan dikembalikan untuk direvisi.');
    }

    /**
     * Tolak — final, tidak bisa diubah lagi
     */
    public function tolak(string $hash): \CodeIgniter\HTTP\RedirectResponse
    {
        if ($redirect = $this->checkAuth()) return $redirect;
        if ($redirect = deny_if_not('admin', 'koordinator_lab')) return $redirect;
        $id = $this->decodeId($hash);
        if (! $id) return redirect()->to(base_url('pengajuan'))->with('error', 'Link tidak valid.');

        $db = $this->db();
        $pengajuan = $db->table('pengajuan_inventaris')->where('id', $id)->get()->getRowArray();

        if (! $pengajuan) {
            return redirect()->to(base_url('pengajuan'))->with('error', 'Data tidak ditemukan.');
        }

        if (in_array($pengajuan['status'], ['selesai', 'ditolak'])) {
            return redirect()->to(base_url('pengajuan'))
                ->with('error', 'Pengajuan ini sudah final dan tidak dapat ditolak lagi.');
        }

        $db->table('pengajuan_inventaris')
            ->where('id', $id)
            ->update([
                'status'         => 'ditolak',
                'catatan_revisi' => $this->request->getPost('catatan_revisi'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ]);

        return redirect()->to(base_url('pengajuan'))
            ->with('success', 'Pengajuan telah ditolak.');
    }

    /**
     * Hapus — status 'diproses' tidak bisa dihapus
     */
    public function hapus(string $hash): \CodeIgniter\HTTP\RedirectResponse
    {
        if ($redirect = $this->checkAuth()) return $redirect;
        if ($redirect = deny_if_not('admin')) return $redirect;
        $id = $this->decodeId($hash);
        if (! $id) return redirect()->to(base_url('pengajuan'))->with('error', 'Link tidak valid.');

        $pengajuan = $this->db()->table('pengajuan_inventaris')->where('id', $id)->get()->getRowArray();

        if (! $pengajuan) {
            return redirect()->to(base_url('pengajuan'))
                ->with('error', 'Data pengajuan tidak ditemukan.');
        }

        if ($pengajuan['status'] === 'diproses') {
            return redirect()->to(base_url('pengajuan'))
                ->with('error', 'Pengajuan yang sedang diproses tidak dapat dihapus.');
        }

        $this->db()->table('pengajuan_inventaris')->where('id', $id)->delete();

        return redirect()->to(base_url('pengajuan'))
            ->with('success', 'Pengajuan berhasil dihapus.');
    }
}

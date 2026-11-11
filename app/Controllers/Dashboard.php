<?php

namespace App\Controllers;

use App\Models\BarangModel;

class Dashboard extends BaseController
{
    protected BarangModel $barangModel;

    public function __construct()
    {
        $this->barangModel = new BarangModel();
    }

    /**
     * Halaman utama dashboard.
     */
    public function index(): string|\CodeIgniter\HTTP\RedirectResponse
    {
        if ($redirect = $this->checkAuth()) {
            return $redirect;
        }

        $role = session()->get('user_role');

        // Dashboard memuat statistik inventaris global dan hanya ditujukan
        // untuk admin/TU. Role lain dialihkan sebelum query dijalankan.
        if (! in_array($role, ['admin', 'tu'], true)) {
            if ($role === 'staff') {
                return redirect()->to(base_url('helpdesk'))
                    ->with('error', 'Akses dashboard hanya tersedia untuk Admin dan TU.');
            }

            if ($role === 'kepala_lab') {
                $idRuangan = session()->get('user_id_ruangan');
                if ($idRuangan) {
                    return redirect()->to(base_url('data-barang-ruangan/' . encode_id($idRuangan)));
                }
            }

            return redirect()->to(base_url('data-barang-ruangan'));
        }

        $keyword    = $this->request->getGet('q');
        $kategori   = $this->request->getGet('kategori');
        $statusStok = $this->request->getGet('status_stok');

        $data = [
            'title'          => 'Dashboard — SIn-FTIK USM',
            'active_menu'    => 'dashboard',
            'stats'          => $this->barangModel->getStats(),
            'barang_list'    => $this->barangModel->getAll($keyword, $kategori, $statusStok),
            'kategori'       => $this->barangModel->getKategori(),
            'keyword'        => $keyword,
            'kategori_aktif' => $kategori,
            'status_stok'    => $statusStok,
        ];

        return view('dashboard/index', $data);
    }
}

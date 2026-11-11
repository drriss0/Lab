<?php

namespace App\Controllers;

use App\Models\ModulModel;

class HelpDesk extends BaseController
{
    protected ModulModel $modulModel;

    public function __construct()
    {
        $this->modulModel = new ModulModel();
    }

    /**
     * Halaman Help Desk — FAQ, Panduan, dan Modul.
     */
    public function index(): string|\CodeIgniter\HTTP\RedirectResponse
    {
        if ($redirect = $this->checkAuth()) return $redirect;

        $db = \Config\Database::connect();

        // Ambil semua FAQ aktif
        $faqList = $db->table('faq')
            ->where('is_active', 1)
            ->orderBy('urutan', 'ASC')
            ->get()->getResultArray();

        // Ambil semua modul aktif
        $modulList = $this->modulModel->where('is_active', 1)->orderBy('created_at', 'DESC')->findAll();

        return view('helpdesk/index', [
            'title'           => 'Help Desk — SIn-FTIK USM',
            'active_menu'     => 'helpdesk',
            'faq_list'        => $faqList,
            'kategori_list'   => [],
            'filter_kategori' => null,
            'modul_list'      => $modulList,
        ]);
    }
}

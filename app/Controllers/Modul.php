<?php

namespace App\Controllers;

use App\Models\ModulModel;

class Modul extends BaseController
{
    protected ModulModel $modulModel;

    public function __construct()
    {
        $this->modulModel = new ModulModel();
    }

    public function index(): string|\CodeIgniter\HTTP\RedirectResponse
    {
        if ($redirect = $this->checkAuth()) return $redirect;
        if ($redirect = deny_if_not('admin')) return $redirect;

        $data = [
            'title'       => 'Kelola Modul — SIn-FTIK USM',
            'active_menu' => 'modul',
            'modul_list'  => $this->modulModel->orderBy('created_at', 'DESC')->findAll(),
        ];

        return view('modul/index', $data);
    }

    public function store(): \CodeIgniter\HTTP\RedirectResponse
    {
        if ($redirect = $this->checkAuth()) return $redirect;
        if ($redirect = deny_if_not('admin')) return $redirect;

        $fileModul = $this->request->getFile('file_modul');
        $judul     = $this->request->getPost('judul');

        if (! $fileModul->isValid() || $fileModul->hasMoved()) {
            return redirect()->back()->with('error', 'Upload file modul gagal atau tidak valid.');
        }

        // Whitelist tipe file yang diizinkan
        $allowedMimes = [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/zip',
            'application/x-zip-compressed',
        ];
        $allowedExts = ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx', 'zip'];

        $mimeType  = $fileModul->getMimeType();
        $extension = strtolower($fileModul->getClientExtension());

        if (! in_array($mimeType, $allowedMimes) || ! in_array($extension, $allowedExts)) {
            return redirect()->back()->with('error', 'Tipe file tidak diizinkan. Hanya file PDF, Word, PowerPoint, Excel, atau ZIP yang dapat diupload.');
        }

        // Batas ukuran file: 20MB
        if ($fileModul->getSize() > 20 * 1024 * 1024) {
            return redirect()->back()->with('error', 'Ukuran file terlalu besar. Maksimum 20MB.');
        }

        $newName = $fileModul->getRandomName();
        $fileModul->move(ROOTPATH . 'public/uploads/modul', $newName);

        $this->modulModel->save([
            'judul'      => $judul,
            'file_modul' => $newName,
            'is_active'  => 1,
        ]);

        return redirect()->to(base_url('modul'))->with('success', 'Modul berhasil ditambahkan.');
    }

    public function delete(string $hash): \CodeIgniter\HTTP\RedirectResponse
    {
        if ($redirect = $this->checkAuth()) return $redirect;
        if ($redirect = deny_if_not('admin')) return $redirect;

        $id = $this->decodeId($hash);
        if (! $id) return redirect()->to(base_url('modul'))->with('error', 'Link tidak valid.');

        $modul = $this->modulModel->find($id);
        if (! $modul) {
            return redirect()->to(base_url('modul'))->with('error', 'Modul tidak ditemukan.');
        }

        $filepath = ROOTPATH . 'public/uploads/modul/' . $modul['file_modul'];
        if (file_exists($filepath)) {
            unlink($filepath);
        }

        $this->modulModel->delete($id);

        return redirect()->to(base_url('modul'))->with('success', 'Modul berhasil dihapus.');
    }
}

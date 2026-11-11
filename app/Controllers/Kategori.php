<?php

namespace App\Controllers;

use App\Models\KategoriModel;

class Kategori extends BaseController
{
    protected KategoriModel $kategoriModel;

    public function __construct()
    {
        $this->kategoriModel = new KategoriModel();
    }


    public function index(): string|\CodeIgniter\HTTP\RedirectResponse
    {
        if ($redirect = $this->checkAuth()) return $redirect;

        $kategoriList = $this->kategoriModel->getKategoriWithCount();

        return view('kategori/index', [
            'title'       => 'Jenis Barang — SIn-FTIK USM',
            'active_menu' => 'kategori',
            'kategori'    => $kategoriList,
        ]);
    }

    public function simpan(): \CodeIgniter\HTTP\RedirectResponse
    {
        if ($redirect = $this->checkAuth()) return $redirect;
        if ($redirect = deny_if_not('admin')) return $redirect;

        $nama = $this->request->getPost('nama');
        if (empty(trim($nama))) {
            return redirect()->back()->with('error', 'Nama jenis barang wajib diisi.');
        }

        $kode = strtoupper(trim($this->request->getPost('kode_kategori')));
        if (empty($kode)) {
            return redirect()->back()->with('error', 'Kode Awal (Prefix) wajib diisi.');
        }
        if (!preg_match('/^[A-Z]+$/', $kode)) {
            return redirect()->back()->with('error', 'Kode Awal (Prefix) hanya boleh berisi huruf (contoh: M, CAM, PC). Angka akan diurutkan otomatis oleh sistem.');
        }

        $exists = $this->kategoriModel->where('kode_kategori', $kode)->first();
        if ($exists) {
            return redirect()->back()->with('error', 'Kode Awal (Prefix) tersebut sudah digunakan oleh jenis barang lain. Kode awalan tidak boleh sama.');
        }

        $this->kategoriModel->insert([
            'kode_kategori' => $kode,
            'nama'          => $nama,
            'created_at'    => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to(base_url('kategori'))->with('success', 'Jenis barang baru berhasil ditambahkan!');
    }

    public function edit(string $hash): \CodeIgniter\HTTP\RedirectResponse
    {
        if ($redirect = $this->checkAuth()) return $redirect;
        if ($redirect = deny_if_not('admin')) return $redirect;
        $id = $this->decodeId($hash);
        if (! $id) return redirect()->to(base_url('kategori'))->with('error', 'Link tidak valid.');

        $kategori = $this->kategoriModel->find($id);
        if (!$kategori) {
            return redirect()->to(base_url('kategori'))->with('error', 'Jenis barang tidak ditemukan.');
        }

        $nama = $this->request->getPost('nama');
        if (empty(trim($nama))) {
            return redirect()->back()->with('error', 'Nama jenis barang wajib diisi.');
        }

        $kode = strtoupper(trim($this->request->getPost('kode_kategori')));
        if (empty($kode)) {
            return redirect()->back()->with('error', 'Kode Awal (Prefix) wajib diisi.');
        }
        if (!preg_match('/^[A-Z]+$/', $kode)) {
            return redirect()->back()->with('error', 'Kode Awal (Prefix) hanya boleh berisi huruf (contoh: M, CAM, PC). Angka akan diurutkan otomatis oleh sistem.');
        }

        $exists = $this->kategoriModel->where('kode_kategori', $kode)->first();
        if ($exists && $exists['id'] != $id) {
            return redirect()->back()->with('error', 'Kode Awal (Prefix) tersebut sudah digunakan oleh jenis barang lain. Kode awalan tidak boleh sama.');
        }

        $this->kategoriModel->update($id, [
            'kode_kategori' => $kode,
            'nama'          => $nama,
        ]);

        return redirect()->to(base_url('kategori'))->with('success', 'Data jenis barang berhasil diperbarui!');
    }

    public function hapus(string $hash): \CodeIgniter\HTTP\RedirectResponse
    {
        if ($redirect = $this->checkAuth()) return $redirect;
        if ($redirect = deny_if_not('admin')) return $redirect;
        $id = $this->decodeId($hash);
        if (! $id) return redirect()->to(base_url('kategori'))->with('error', 'Link tidak valid.');

        $kategori = $this->kategoriModel->find($id);
        if (!$kategori) {
            return redirect()->to(base_url('kategori'))->with('error', 'Jenis barang tidak ditemukan.');
        }

        // Cek apakah jenis ini masih dipakai di tabel barang (via id FK)
        $db = \Config\Database::connect();
        $dipakai = $db->table('barang')->where('id_kategori', $id)->countAllResults();

        if ($dipakai > 0) {
            return redirect()->to(base_url('kategori'))
                ->with('error', 'Jenis barang gagal dihapus karena masih digunakan oleh ' . $dipakai . ' aset inventaris.');
        }

        $this->kategoriModel->delete($id);
        return redirect()->to(base_url('kategori'))->with('success', 'Jenis barang berhasil dihapus!');
    }
}

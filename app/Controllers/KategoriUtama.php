<?php

namespace App\Controllers;

use App\Models\KategoriUtamaModel;

class KategoriUtama extends BaseController
{
    protected KategoriUtamaModel $kategoriModel;

    public function __construct()
    {
        $this->kategoriModel = new KategoriUtamaModel();
    }


    public function index(): string|\CodeIgniter\HTTP\RedirectResponse
    {
        if ($redirect = $this->checkAuth()) return $redirect;

        $kategoriList = $this->kategoriModel->getKategoriWithCount();

        return view('kategori_utama/index', [
            'title'       => 'Kategori — SIn-FTIK USM',
            'active_menu' => 'kelola',
            'kategori'    => $kategoriList,
        ]);
    }

    public function simpan(): \CodeIgniter\HTTP\RedirectResponse
    {
        if ($redirect = $this->checkAuth()) return $redirect;
        if ($redirect = deny_if_not('admin')) return $redirect;

        $nama = trim($this->request->getPost('nama_kategori'));
        if (empty($nama)) {
            return redirect()->back()->with('error', 'Nama kategori wajib diisi.');
        }

        $exists = $this->kategoriModel->where('nama_kategori', $nama)->first();
        if ($exists) {
            return redirect()->back()->with('error', 'Kategori utama dengan nama tersebut sudah ada.');
        }

        $this->kategoriModel->insert([
            'nama_kategori' => $nama,
            'keterangan'    => trim($this->request->getPost('keterangan')),
            'created_at'    => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to(base_url('kategori-utama'))->with('success', 'Kategori baru berhasil ditambahkan!');
    }

    public function edit(string $hash): \CodeIgniter\HTTP\RedirectResponse
    {
        if ($redirect = $this->checkAuth()) return $redirect;
        if ($redirect = deny_if_not('admin')) return $redirect;
        $id = $this->decodeId($hash);
        if (! $id) return redirect()->to(base_url('kategori-utama'))->with('error', 'Link tidak valid.');

        $kategori = $this->kategoriModel->find($id);
        if (!$kategori) {
            return redirect()->to(base_url('kategori-utama'))->with('error', 'Kategori tidak ditemukan.');
        }

        $nama = trim($this->request->getPost('nama_kategori'));
        if (empty($nama)) {
            return redirect()->back()->with('error', 'Nama kategori wajib diisi.');
        }

        $exists = $this->kategoriModel->where('nama_kategori', $nama)->first();
        if ($exists && $exists['id'] != $id) {
            return redirect()->back()->with('error', 'Kategori utama dengan nama tersebut sudah ada.');
        }

        // Jika nama berubah, sinkronkan juga di tabel barang
        $db      = \Config\Database::connect();
        $oldName = $kategori['nama_kategori'];
        $newName = trim($nama);

        if ($oldName !== $newName) {
            $db->table('barang')->where('kategori', $oldName)->update(['kategori' => $newName]);
        }

        $this->kategoriModel->update($id, [
            'nama_kategori' => $newName,
            'keterangan'    => trim($this->request->getPost('keterangan')),
        ]);

        return redirect()->to(base_url('kategori-utama'))->with('success', 'Data kategori berhasil diperbarui!');
    }

    public function hapus(string $hash): \CodeIgniter\HTTP\RedirectResponse
    {
        if ($redirect = $this->checkAuth()) return $redirect;
        if ($redirect = deny_if_not('admin')) return $redirect;
        $id = $this->decodeId($hash);
        if (! $id) return redirect()->to(base_url('kategori-utama'))->with('error', 'Link tidak valid.');

        $kategori = $this->kategoriModel->find($id);
        if (!$kategori) {
            return redirect()->to(base_url('kategori-utama'))->with('error', 'Kategori tidak ditemukan.');
        }

        // Cek apakah jenis ini masih dipakai di tabel barang
        $db = \Config\Database::connect();
        $namaKategori = $kategori['nama_kategori'];
        $dipakai = $db->table('barang')->where('kategori', $namaKategori)->countAllResults();

        if ($dipakai > 0) {
            return redirect()->to(base_url('kategori-utama'))
                ->with('error', 'Kategori gagal dihapus karena masih digunakan oleh ' . $dipakai . ' aset inventaris.');
        }

        $this->kategoriModel->delete($id);
        return redirect()->to(base_url('kategori-utama'))->with('success', 'Kategori berhasil dihapus!');
    }
}

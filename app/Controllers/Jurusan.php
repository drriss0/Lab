<?php

namespace App\Controllers;

use App\Models\JurusanModel;

class Jurusan extends BaseController
{
    protected JurusanModel $jurusanModel;

    public function __construct()
    {
        $this->jurusanModel = new JurusanModel();
    }

    public function index(): string|\CodeIgniter\HTTP\RedirectResponse
    {
        if ($redirect = $this->checkAuth()) return $redirect;
        if ($redirect = deny_if_not('admin')) return $redirect;

        $list = $this->jurusanModel->getAllJurusan();

        return view('jurusan/index', [
            'title'       => 'Kelola Jurusan — SIn-FTIK USM',
            'active_menu' => 'kelola', // Tetap highlight menu kelola
            'list'        => $list,
        ]);
    }

    public function simpan(): \CodeIgniter\HTTP\RedirectResponse
    {
        if ($redirect = $this->checkAuth()) return $redirect;
        if ($redirect = deny_if_not('admin')) return $redirect;

        $nama = strtoupper(trim($this->request->getPost('nama_jurusan')));
        if (empty($nama)) {
            return redirect()->back()->with('error', 'Nama jurusan wajib diisi.');
        }

        $exists = $this->jurusanModel->where('nama_jurusan', $nama)->first();
        if ($exists) {
            return redirect()->back()->with('error', 'Jurusan dengan nama tersebut sudah ada.');
        }

        $this->jurusanModel->insert([
            'nama_jurusan' => $nama,
            'created_at'   => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to(base_url('jurusan'))->with('success', 'Jurusan baru berhasil ditambahkan!');
    }

    public function edit(string $hash): \CodeIgniter\HTTP\RedirectResponse
    {
        if ($redirect = $this->checkAuth()) return $redirect;
        if ($redirect = deny_if_not('admin')) return $redirect;
        $id = $this->decodeId($hash);
        if (! $id) return redirect()->to(base_url('jurusan'))->with('error', 'Link tidak valid.');

        $jurusan = $this->jurusanModel->find($id);
        if (!$jurusan) {
            return redirect()->to(base_url('jurusan'))->with('error', 'Jurusan tidak ditemukan.');
        }

        $nama = strtoupper(trim($this->request->getPost('nama_jurusan')));
        if (empty($nama)) {
            return redirect()->back()->with('error', 'Nama jurusan wajib diisi.');
        }

        $exists = $this->jurusanModel->where('nama_jurusan', $nama)->first();
        if ($exists && $exists['id'] != $id) {
            return redirect()->back()->with('error', 'Jurusan dengan nama tersebut sudah ada.');
        }

        $this->jurusanModel->update($id, [
            'nama_jurusan' => $nama,
        ]);

        return redirect()->to(base_url('jurusan'))->with('success', 'Data jurusan berhasil diperbarui!');
    }

    public function hapus(string $hash): \CodeIgniter\HTTP\RedirectResponse
    {
        if ($redirect = $this->checkAuth()) return $redirect;
        if ($redirect = deny_if_not('admin')) return $redirect;
        $id = $this->decodeId($hash);
        if (! $id) return redirect()->to(base_url('jurusan'))->with('error', 'Link tidak valid.');

        $jurusan = $this->jurusanModel->find($id);
        if (!$jurusan) {
            return redirect()->to(base_url('jurusan'))->with('error', 'Jurusan tidak ditemukan.');
        }

        // Cek apakah dipakai di tabel pengajuan_inventaris
        $db = \Config\Database::connect();
        $namaJurusan = $jurusan['nama_jurusan'];
        $dipakai = $db->table('pengajuan_inventaris')
                      ->where('kebutuhan_jurusan', $namaJurusan)
                      ->countAllResults();

        if ($dipakai > 0) {
            return redirect()->to(base_url('jurusan'))
                ->with('error', 'Jurusan gagal dihapus karena sudah dipakai pada ' . $dipakai . ' data pengajuan.');
        }

        $this->jurusanModel->delete($id);
        return redirect()->to(base_url('jurusan'))->with('success', 'Jurusan berhasil dihapus!');
    }
}

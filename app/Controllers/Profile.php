<?php

namespace App\Controllers;

use App\Models\UserModel;

class Profile extends BaseController
{
    protected UserModel $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    /**
     * Tampilkan halaman profil saya
     */
    public function index(): string|\CodeIgniter\HTTP\RedirectResponse
    {
        if ($redirect = $this->checkAuth()) return $redirect;

        $userId = session()->get('user_id');
        $user = $this->userModel->find($userId);

        if (!$user) {
            return redirect()->to(base_url('login'))->with('error', 'Sesi tidak valid.');
        }

        return view('profile/index', [
            'title'       => 'Profil Saya - SIn-FTIK USM',
            'active_menu' => 'profile',
            'user'        => $user,
        ]);
    }

    /**
     * Proses simpan profil
     */
    public function simpanProfil(): \CodeIgniter\HTTP\RedirectResponse
    {
        if ($redirect = $this->checkAuth()) return $redirect;

        $userId = session()->get('user_id');
        $user = $this->userModel->find($userId);

        if (!$user) {
            return redirect()->to(base_url('login'))->with('error', 'Sesi tidak valid.');
        }

        $nama = $this->request->getPost('nama');
        $username = $this->request->getPost('username');
        $email = $this->request->getPost('email');

        // Validasi input
        if (empty($nama) || empty($username) || empty($email)) {
            return redirect()->back()->with('error', 'Semua field wajib diisi.');
        }

        // Validasi format email
        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return redirect()->back()->with('error', 'Format email tidak valid.');
        }

        // Cek apakah username sudah dipakai orang lain
        $existingUsername = $this->userModel->where('username', $username)->where('id !=', $userId)->first();
        if ($existingUsername) {
            return redirect()->back()->with('error', 'Username sudah digunakan oleh pengguna lain.');
        }

        // Cek apakah email sudah dipakai orang lain
        $existingEmail = $this->userModel->where('email', $email)->where('id !=', $userId)->first();
        if ($existingEmail) {
            return redirect()->back()->with('error', 'Email sudah digunakan oleh pengguna lain.');
        }

        // Jika semua lolos, update profil
        $this->userModel->update($userId, [
            'nama'     => $nama,
            'username' => $username,
            'email'    => $email
        ]);

        // Update session data
        session()->set([
            'user_nama'     => $nama,
            'user_username' => $username,
        ]);

        return redirect()->to(base_url('profile'))->with('success', 'Profil berhasil diperbarui!');
    }

    /**
     * Tampilkan halaman ubah password
     */
    public function ubahPassword(): string|\CodeIgniter\HTTP\RedirectResponse
    {
        if ($redirect = $this->checkAuth()) return $redirect;

        return view('profile/ubah_password', [
            'title'       => 'Ubah Password - SIn-FTIK USM',
            'active_menu' => 'ubah-password',
        ]);
    }

    /**
     * Proses simpan password baru
     */
    public function simpanPassword(): \CodeIgniter\HTTP\RedirectResponse
    {
        if ($redirect = $this->checkAuth()) return $redirect;

        $userId = session()->get('user_id');
        $user = $this->userModel->find($userId);

        if (!$user) {
            return redirect()->to(base_url('login'))->with('error', 'Sesi tidak valid.');
        }

        $passwordLama = $this->request->getPost('password_lama');
        $passwordBaru = $this->request->getPost('password_baru');
        $konfirmasiPassword = $this->request->getPost('konfirmasi_password');

        // Validasi input
        if (empty($passwordLama) || empty($passwordBaru) || empty($konfirmasiPassword)) {
            return redirect()->back()->with('error', 'Semua field wajib diisi.');
        }

        // Cek password lama
        $valid = false;
        if (password_get_info($user['password'])['algo'] !== 0) {
            $valid = $this->userModel->verifyPassword($passwordLama, $user['password']);
        }
        
        if (!$valid) {
            return redirect()->back()->with('error', 'Password lama tidak sesuai.');
        }

        // Cek konfirmasi
        if ($passwordBaru !== $konfirmasiPassword) {
            return redirect()->back()->with('error', 'Konfirmasi password tidak cocok dengan password baru.');
        }

        // Validasi kekuatan password
        if (strlen($passwordBaru) < 8) {
            return redirect()->back()->with('error', 'Password baru minimal 8 karakter.');
        }
        if (!preg_match('/[A-Z]/', $passwordBaru)) {
            return redirect()->back()->with('error', 'Password baru harus mengandung minimal 1 huruf kapital (A-Z).');
        }
        if (!preg_match('/[a-z]/', $passwordBaru)) {
            return redirect()->back()->with('error', 'Password baru harus mengandung minimal 1 huruf kecil (a-z).');
        }
        if (!preg_match('/[0-9]/', $passwordBaru)) {
            return redirect()->back()->with('error', 'Password baru harus mengandung minimal 1 angka (0-9).');
        }
        if (!preg_match('/[!@#$%^&*()_+\-=\[\]{};:\'"\\|,.<>\/?`~]/', $passwordBaru)) {
            return redirect()->back()->with('error', 'Password baru harus mengandung minimal 1 karakter spesial (!@#$%^&* dll).');
        }

        // Jika semua lolos, update password
        $this->userModel->update($userId, [
            'password' => password_hash($passwordBaru, PASSWORD_BCRYPT)
        ]);

        // Password adalah bagian fingerprint session; logout eksplisit membuat
        // revocation langsung dan menghindari satu redirect dengan session basi.
        session()->destroy();

        return redirect()->to(base_url('login'))
            ->with('success', 'Password berhasil diubah. Silakan login kembali.');
    }
}

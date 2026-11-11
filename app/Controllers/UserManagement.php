<?php

namespace App\Controllers;

use App\Models\UserModel;

/**
 * UserManagement — Kelola Akun User (Admin Only)
 */
class UserManagement extends BaseController
{
    protected UserModel $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }


    public function index(): string|\CodeIgniter\HTTP\RedirectResponse
    {
        if ($redirect = $this->checkAuth()) return $redirect;
        if ($redirect = deny_if_not('admin')) return $redirect;

        $db = \Config\Database::connect();
        $users = $db->table('users u')
            ->select('u.*, r.nama_ruangan')
            ->join('ruangan r', 'r.id = u.id_ruangan', 'left')
            ->orderBy('u.role', 'ASC')
            ->orderBy('u.nama', 'ASC')
            ->get()->getResultArray();

        $ruangan = $db->table('ruangan')->orderBy('nama_ruangan', 'ASC')->get()->getResultArray();

        return view('user_management/index', [
            'title'       => 'Kelola User - SIn-FTIK USM',
            'active_menu' => 'users',
            'users'       => $users,
            'ruangan'     => $ruangan,
        ]);
    }

    public function simpan(): \CodeIgniter\HTTP\RedirectResponse
    {
        if ($redirect = $this->checkAuth()) return $redirect;
        if ($redirect = deny_if_not('admin')) return $redirect;

        $username = $this->request->getPost('username');
        $email    = $this->request->getPost('email');
        $password = $this->request->getPost('password');
        $role     = $this->request->getPost('role');

        // Validasi role: hanya nilai enum yang diizinkan
        $validRoles = ['admin', 'staff', 'kepala_lab', 'dekan', 'tu', 'koordinator_lab'];
        if (! in_array($role, $validRoles)) {
            return redirect()->to(base_url('users'))->with('error', 'Role yang dipilih tidak valid.');
        }

        // Validasi kekuatan password
        if (empty($password) || strlen($password) < 8) {
            return redirect()->to(base_url('users'))->with('error', 'Password minimal 8 karakter.');
        }
        if (!preg_match('/[A-Z]/', $password)) {
            return redirect()->to(base_url('users'))->with('error', 'Password harus mengandung minimal 1 huruf kapital (A-Z).');
        }
        if (!preg_match('/[a-z]/', $password)) {
            return redirect()->to(base_url('users'))->with('error', 'Password harus mengandung minimal 1 huruf kecil (a-z).');
        }
        if (!preg_match('/[0-9]/', $password)) {
            return redirect()->to(base_url('users'))->with('error', 'Password harus mengandung minimal 1 angka (0-9).');
        }
        if (!preg_match('/[!@#$%^&*()_+\-=\[\]{};:\'"\\|,.<>\/?`~]/', $password)) {
            return redirect()->to(base_url('users'))->with('error', 'Password harus mengandung minimal 1 karakter spesial (!@#$%^&* dll).');
        }

        // Cek duplikat username/email
        $existing = $this->userModel->where('username', $username)->orWhere('email', $email)->first();
        if ($existing) {
            return redirect()->to(base_url('users'))->with('error', 'Username atau email sudah digunakan.');
        }

        $this->userModel->insert([
            'nama'       => $this->request->getPost('nama'),
            'username'   => $username,
            'email'      => $email,
            'password'   => password_hash($password, PASSWORD_BCRYPT),
            'role'       => $role,
            'id_ruangan' => $this->request->getPost('id_ruangan') ?: null,
            'is_active'  => 1,
        ]);

        return redirect()->to(base_url('users'))->with('success', 'User berhasil ditambahkan!');
    }

    public function edit(string $hash): \CodeIgniter\HTTP\RedirectResponse
    {
        if ($redirect = $this->checkAuth()) return $redirect;
        if ($redirect = deny_if_not('admin')) return $redirect;

        $id = $this->decodeId($hash);
        if (! $id) return redirect()->to(base_url('users'))->with('error', 'Link tidak valid.');

        $user = $this->userModel->find($id);
        if (! $user) {
            return redirect()->to(base_url('users'))->with('error', 'User tidak ditemukan.');
        }

        $username = trim($this->request->getPost('username'));
        $email    = trim($this->request->getPost('email'));
        $role     = $this->request->getPost('role');

        // Validasi role: hanya nilai enum yang diizinkan
        $validRoles = ['admin', 'staff', 'kepala_lab', 'dekan', 'tu', 'koordinator_lab'];
        if (! in_array($role, $validRoles)) {
            return redirect()->to(base_url('users'))->with('error', 'Role yang dipilih tidak valid.');
        }

        // Cek duplikat username/email (kecuali milik user ini sendiri)
        $existing = $this->userModel->groupStart()
                                    ->where('username', $username)
                                    ->orWhere('email', $email)
                                    ->groupEnd()
                                    ->where('id !=', $id)
                                    ->first();
        if ($existing) {
            return redirect()->to(base_url('users'))->with('error', 'Username atau email sudah digunakan oleh user lain.');
        }

        $data = [
            'nama'       => trim($this->request->getPost('nama')),
            'username'   => $username,
            'email'      => $email,
            'role'       => $role,
            'id_ruangan' => $this->request->getPost('id_ruangan') ?: null,
        ];

        $newPass = $this->request->getPost('password');
        if (! empty($newPass)) {
            if (strlen($newPass) < 8) {
                return redirect()->to(base_url('users'))->with('error', 'Password minimal 8 karakter.');
            }
            if (!preg_match('/[A-Z]/', $newPass)) {
                return redirect()->to(base_url('users'))->with('error', 'Password harus mengandung minimal 1 huruf kapital (A-Z).');
            }
            if (!preg_match('/[a-z]/', $newPass)) {
                return redirect()->to(base_url('users'))->with('error', 'Password harus mengandung minimal 1 huruf kecil (a-z).');
            }
            if (!preg_match('/[0-9]/', $newPass)) {
                return redirect()->to(base_url('users'))->with('error', 'Password harus mengandung minimal 1 angka (0-9).');
            }
            if (!preg_match('/[!@#$%^&*()_+\-=\[\]{};:\'"\\|,.<>\/?`~]/', $newPass)) {
                return redirect()->to(base_url('users'))->with('error', 'Password harus mengandung minimal 1 karakter spesial (!@#$%^&* dll).');
            }
            $data['password'] = password_hash($newPass, PASSWORD_BCRYPT);
        }

        $this->userModel->update($id, $data);

        return redirect()->to(base_url('users'))->with('success', 'Data user berhasil diperbarui!');
    }

    public function toggleStatus(string $hash): \CodeIgniter\HTTP\RedirectResponse
    {
        if ($redirect = $this->checkAuth()) return $redirect;
        if ($redirect = deny_if_not('admin')) return $redirect;
        $id = $this->decodeId($hash);
        if (! $id) return redirect()->to(base_url('users'))->with('error', 'Link tidak valid.');

        $user = $this->userModel->find($id);
        if (! $user) {
            return redirect()->to(base_url('users'))->with('error', 'User tidak ditemukan.');
        }

        if ($user['role'] === 'admin') {
            return redirect()->to(base_url('users'))->with('error', 'Akun admin tidak dapat dinonaktifkan.');
        }

        // Cegah admin menonaktifkan dirinya sendiri
        if ((int)$user['id'] === (int)session()->get('user_id')) {
            return redirect()->to(base_url('users'))->with('error', 'Anda tidak dapat mengubah status akun Anda sendiri.');
        }

        $this->userModel->update($id, ['is_active' => $user['is_active'] ? 0 : 1]);
        $label = $user['is_active'] ? 'dinonaktifkan' : 'diaktifkan';

        return redirect()->to(base_url('users'))->with('success', "User berhasil $label.");
    }

    public function hapus(string $hash): \CodeIgniter\HTTP\RedirectResponse
    {
        if ($redirect = $this->checkAuth()) return $redirect;
        if ($redirect = deny_if_not('admin')) return $redirect;
        $id = $this->decodeId($hash);
        if (! $id) return redirect()->to(base_url('users'))->with('error', 'Link tidak valid.');

        $user = $this->userModel->find($id);
        if (! $user) {
            return redirect()->to(base_url('users'))->with('error', 'User tidak ditemukan.');
        }

        if ($user['role'] === 'admin') {
            return redirect()->to(base_url('users'))->with('error', 'Akun admin tidak dapat dihapus.');
        }

        // Cegah admin menghapus dirinya sendiri
        if ((int)$user['id'] === (int)session()->get('user_id')) {
            return redirect()->to(base_url('users'))->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $this->userModel->delete($id);

        return redirect()->to(base_url('users'))->with('success', 'User berhasil dihapus secara permanen.');
    }
}

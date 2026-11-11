<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Security\LoginThrottle;
use App\Security\SessionIdentity;

class Auth extends BaseController
{
    protected UserModel $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function index(): string|\CodeIgniter\HTTP\RedirectResponse
    {
        if (session()->get('logged_in')) {
            return redirect()->to(base_url('dashboard'));
        }

        return view('auth/login', [
            'title' => 'Login — SIn-FTIK USM',
        ]);
    }

    public function login(): \CodeIgniter\HTTP\RedirectResponse
    {
        $session = session();
        $rules = [
            'credential' => 'required|min_length[3]',
            'password'   => 'required|min_length[8]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()
                ->with('error', 'Email/Username dan Password wajib diisi.');
        }

        $credential = (string) $this->request->getPost('credential');
        $password = (string) $this->request->getPost('password');
        $cache = \Config\Services::cache();
        $throttleKeys = LoginThrottle::keys($this->request->getIPAddress(), $credential);
        $globalAttempts = (int) ($cache->get($throttleKeys['global']) ?? 0);
        $targetAttempts = (int) ($cache->get($throttleKeys['target']) ?? 0);

        if ($globalAttempts >= LoginThrottle::GLOBAL_IP_LIMIT
            || $targetAttempts >= LoginThrottle::TARGET_LIMIT) {
            return redirect()->back()
                ->with('error_title', 'Akses Terkunci Sementara')
                ->with('error_desc', 'Terlalu banyak percobaan login gagal. Silakan coba lagi nanti.')
                ->with('cooldown_minutes', 15);
        }

        $user = $this->userModel->findByCredential($credential);
        $valid = $user !== null
            && password_get_info($user['password'])['algo'] !== 0
            && $this->userModel->verifyPassword($password, $user['password']);

        if (! $valid) {
            return $this->handleFailedLogin($credential);
        }

        // A login for account A may reset only A's counter. The global IP
        // counter remains intact so a valid account cannot erase password spray.
        $cache->delete($throttleKeys['target']);

        // Rotate the pre-authentication identifier before storing authority.
        $session->regenerate(true);
        $session->set([
            'logged_in'        => true,
            'user_id'          => $user['id'],
            'user_nama'        => $user['nama'],
            'user_role'        => $user['role'],
            'user_email'       => $user['email'],
            'user_username'    => $user['username'] ?? $user['email'],
            'user_id_ruangan'  => $user['id_ruangan'] ?? null,
            'auth_fingerprint' => SessionIdentity::fingerprint($user),
        ]);

        $landing = match ($user['role']) {
            'dekan', 'koordinator_lab', 'kepala_lab' => base_url('data-barang-ruangan'),
            'staff' => base_url('helpdesk'),
            default => base_url('dashboard'),
        };

        return redirect()->to($landing)
            ->with('success', 'Selamat datang, ' . $user['nama'] . '!');
    }

    public function logout(): \CodeIgniter\HTTP\RedirectResponse
    {
        session()->destroy();

        return redirect()->to(base_url('login'))
            ->with('success', 'Anda telah berhasil keluar.');
    }

    private function handleFailedLogin(string $credential): \CodeIgniter\HTTP\RedirectResponse
    {
        $cache = \Config\Services::cache();
        $keys = LoginThrottle::keys($this->request->getIPAddress(), $credential);
        $globalAttempts = (int) ($cache->get($keys['global']) ?? 0) + 1;
        $targetAttempts = (int) ($cache->get($keys['target']) ?? 0) + 1;

        $cache->save($keys['global'], $globalAttempts, LoginThrottle::TTL_SECONDS);
        $cache->save($keys['target'], $targetAttempts, LoginThrottle::TTL_SECONDS);

        if ($targetAttempts >= LoginThrottle::TARGET_LIMIT
            || $globalAttempts >= LoginThrottle::GLOBAL_IP_LIMIT) {
            return redirect()->back()
                ->with('error_title', 'Akses Terkunci Sementara')
                ->with('error_desc', 'Terlalu banyak percobaan login gagal. Silakan coba lagi nanti.')
                ->with('cooldown_minutes', 15);
        }

        return redirect()->back()
            ->with('error_title', 'Login Gagal')
            ->with('error_desc', 'Email/Username atau password tidak valid.')
            ->with('attempts_left', LoginThrottle::TARGET_LIMIT - $targetAttempts);
    }
}

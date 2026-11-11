<?php

namespace App\Filters;

use App\Models\UserModel;
use App\Security\SessionIdentity;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

/**
 * RoleFilter
 *
 * Memvalidasi apakah role user yang sedang login berhak mengakses route tertentu.
 * Gunakan sebagai argument di Routes.php atau via Config/Filters.php
 *
 * Contoh penggunaan di Routes:
 *   $routes->get('/users', 'UserManagement::index', ['filter' => 'role:admin']);
 */
class RoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

        // Pastikan user sudah login
        if (! $session->get('logged_in')) {
            return redirect()->to(base_url('login'))
                ->with('error', 'Silakan login terlebih dahulu.');
        }

        // Otorisasi tidak boleh bergantung pada snapshot role/status saat login.
        // Perubahan password, role, ruangan, status aktif, atau penghapusan akun
        // mengubah fingerprint dan mencabut seluruh session lama pada request berikutnya.
        $user = (new UserModel())->find((int) $session->get('user_id'));
        if (! SessionIdentity::matches($session->get('auth_fingerprint'), $user)) {
            $session->destroy();

            return redirect()->to(base_url('login'))
                ->with('error', 'Sesi Anda sudah tidak berlaku. Silakan login kembali.');
        }

        // Sinkronkan field non-rahasia agar semua helper memakai state database saat ini.
        $session->set([
            'user_role'       => $user['role'],
            'user_id_ruangan' => $user['id_ruangan'] ?? null,
        ]);

        // Jika tidak ada argument role, berarti semua role diizinkan
        if (empty($arguments)) {
            return;
        }

        $role = $session->get('user_role');

        // Cek apakah role user ada di daftar yang diizinkan
        if (! in_array($role, $arguments, true)) {
            return redirect()->to(base_url('dashboard'))
                ->with('error', 'Akses ditolak. Anda tidak memiliki izin untuk halaman tersebut.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Tidak diperlukan
    }
}

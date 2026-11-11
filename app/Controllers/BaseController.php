<?php

namespace App\Controllers;

use App\Security\InventoryPolicy;
use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 *
 * Extend this class in any new controllers:
 * ```
 *     class Home extends BaseController
 * ```
 *
 * For security, be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Load here all helpers you want to be available in your controllers that extend BaseController.
        // Caution: Do not put the this below the parent::initController() call below.
        $this->helpers = ['role', 'hashid'];

        // Caution: Do not edit this line.
        parent::initController($request, $response, $logger);
    }

    /**
     * Middleware: pastikan user sudah login.
     * Diwarisi oleh semua controller yang meng-extend BaseController.
     */
    protected function checkAuth(): ?\CodeIgniter\HTTP\RedirectResponse
    {
        if (! session()->get('logged_in')) {
            return redirect()->to(base_url('login'))
                ->with('error', 'Silakan login terlebih dahulu.');
        }
        return null;
    }

    /**
     * Decode hash dari URL ke integer ID.
     * Mengembalikan 0 jika hash tidak valid (triggers 404 di controller).
     */
    protected function decodeId(string $hash): int
    {
        return decode_id($hash);
    }

    /**
     * Normalisasi input operasi massal dan batasi cardinality untuk mencegah
     * request berukuran tidak terkendali membebani database.
     *
     * @return list<string>
     */
    protected function normalizeBulkIds(mixed $ids): array
    {
        if (! is_array($ids)) {
            throw new \InvalidArgumentException('Daftar item bulk tidak valid.');
        }

        return InventoryPolicy::normalizeBulkIds($ids);
    }

    /**
     * Middleware: pastikan kepala_lab hanya bisa akses ruangan yang ditugaskan.
     * Role lain (admin, koordinator_lab, tu, dekan) diizinkan akses ke semua ruangan.
     *
     * @param  int  $idRuangan  ID ruangan yang ingin diakses
     * @param  string $redirectUrl URL fallback saat akses ditolak
     */
    protected function checkRoomAccess(int $idRuangan, string $redirectUrl = ''): ?\CodeIgniter\HTTP\RedirectResponse
    {
        $role           = session()->get('user_role');
        $idRuanganUser  = (int) session()->get('user_id_ruangan');

        if ($role === 'kepala_lab' && $idRuanganUser !== $idRuangan) {
            $url = $redirectUrl ?: base_url('data-barang-ruangan');
            return redirect()->to($url)
                ->with('error', 'Akses ditolak. Anda hanya dapat mengelola ruangan yang ditugaskan kepada Anda.');
        }

        return null;
    }
}

<?php

/**
 * role_helper.php
 * 
 * Helper function untuk pemeriksaan role user yang sedang login.
 * Gunakan di Controller maupun View.
 */

if (! function_exists('user_role')) {
    /** Ambil role user saat ini dari session. */
    function user_role(): string
    {
        return session()->get('user_role') ?? 'guest';
    }
}

if (! function_exists('is_admin')) {
    function is_admin(): bool { return user_role() === 'admin'; }
}

if (! function_exists('is_dekan')) {
    function is_dekan(): bool { return user_role() === 'dekan'; }
}

if (! function_exists('is_koordinator')) {
    function is_koordinator(): bool { return user_role() === 'koordinator_lab'; }
}

if (! function_exists('is_kepala_lab')) {
    function is_kepala_lab(): bool { return user_role() === 'kepala_lab'; }
}

if (! function_exists('is_tu')) {
    function is_tu(): bool { return user_role() === 'tu'; }
}

if (! function_exists('can')) {
    /**
     * Cek apakah role saat ini termasuk dalam daftar role yang diizinkan.
     * Contoh: can('admin', 'koordinator_lab')
     */
    function can(string ...$roles): bool
    {
        return in_array(user_role(), $roles);
    }
}

if (! function_exists('deny_if_not')) {
    /**
     * Redirect ke dashboard jika role tidak diizinkan (untuk dipakai di Controller).
     * Kembalikan RedirectResponse atau null.
     */
    function deny_if_not(string ...$roles): ?\CodeIgniter\HTTP\RedirectResponse
    {
        if (! can(...$roles)) {
            return redirect()->to(base_url('dashboard'))
                ->with('error', 'Akses ditolak. Anda tidak memiliki izin untuk tindakan tersebut.');
        }
        return null;
    }
}

if (! function_exists('is_valid_keterangan')) {
    /**
     * Memvalidasi field keterangan/alasan.
     * Jika diisi, wajib mengandung minimal 3 karakter alfabet.
     * @param string|null $text
     * @return bool
     */
    function is_valid_keterangan(?string $text): bool
    {
        $text = trim($text ?? '');
        if ($text === '') {
            return true; // Boleh kosong (opsional)
        }
        // Minimal ada 3 huruf alfabet (a-z, A-Z)
        return preg_match_all('/[a-zA-Z]/', $text) >= 3;
    }
}

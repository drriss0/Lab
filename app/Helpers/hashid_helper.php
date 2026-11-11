<?php

/**
 * HashId Helper — enkripsi/dekripsi integer ID untuk URL
 *
 * Penggunaan:
 *   encode_id(5)         → "XRKp7Q"  (untuk dipakai di URL)
 *   decode_id("XRKp7Q") → 5          (untuk dipakai sebagai DB key)
 *
 * Menggunakan library hashids/hashids dengan salt dari encryption.key .env
 * sehingga hash berbeda di setiap instalasi.
 */

function _hashids_instance(): \Hashids\Hashids
{
    static $instance = null;
    if ($instance === null) {
        // Gunakan encryption key sebagai salt agar hash unik per aplikasi
        $salt     = env('encryption.key', 'sin-ftik-usm-default-salt');
        $minLen   = 8;   // panjang minimum hash
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $instance = new \Hashids\Hashids($salt, $minLen, $alphabet);
    }
    return $instance;
}

/**
 * Encode integer ID menjadi string hash untuk URL.
 *
 * @param int $id ID database
 * @return string Hash string (e.g. "XRKp7Qmn")
 */
function encode_id(int $id): string
{
    return _hashids_instance()->encode($id);
}

/**
 * Decode hash dari URL kembali ke integer ID.
 * Mengembalikan 0 jika hash tidak valid.
 *
 * @param string $hash Hash dari URL
 * @return int ID database, atau 0 jika tidak valid
 */
function decode_id(string $hash): int
{
    $decoded = _hashids_instance()->decode($hash);
    return ! empty($decoded) ? (int) $decoded[0] : 0;
}

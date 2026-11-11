<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table         = 'users';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['nama', 'username', 'email', 'password', 'role', 'id_ruangan', 'is_active'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Cari user berdasarkan username atau email untuk proses login.
     */
    public function findByCredential(string $credential): ?array
    {
        return $this->where('is_active', 1)
                    ->groupStart()
                        ->where('username', $credential)
                        ->orWhere('email', $credential)
                    ->groupEnd()
                    ->first();
    }

    /**
     * Verifikasi password.
     */
    public function verifyPassword(string $plain, string $hashed): bool
    {
        return password_verify($plain, $hashed);
    }
}

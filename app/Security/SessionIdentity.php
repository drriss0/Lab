<?php

namespace App\Security;

final class SessionIdentity
{
    public static function fingerprint(array $user): string
    {
        $identity = [
            (string) ($user['id'] ?? ''),
            (string) ($user['password'] ?? ''),
            (string) ($user['role'] ?? ''),
            (string) ($user['id_ruangan'] ?? ''),
            (string) (int) ($user['is_active'] ?? 0),
        ];

        return hash('sha256', implode("\x1F", $identity));
    }

    public static function matches(?string $sessionFingerprint, ?array $user): bool
    {
        if ($sessionFingerprint === null || $sessionFingerprint === '' || $user === null) {
            return false;
        }

        if ((int) ($user['is_active'] ?? 0) !== 1) {
            return false;
        }

        return hash_equals(self::fingerprint($user), $sessionFingerprint);
    }
}

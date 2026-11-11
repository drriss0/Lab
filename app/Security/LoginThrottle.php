<?php

namespace App\Security;

final class LoginThrottle
{
    public const TARGET_LIMIT = 3;
    public const GLOBAL_IP_LIMIT = 30;
    public const TTL_SECONDS = 900;

    /** @return array{global: string, target: string} */
    public static function keys(string $ipAddress, string $credential): array
    {
        $ipHash = hash('sha256', trim($ipAddress));
        $targetHash = hash('sha256', mb_strtolower(trim($credential), 'UTF-8'));

        return [
            'global' => 'login_global_' . $ipHash,
            'target' => 'login_target_' . $ipHash . '_' . $targetHash,
        ];
    }
}

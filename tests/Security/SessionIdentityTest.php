<?php

use App\Security\SessionIdentity;
use PHPUnit\Framework\TestCase;

require_once dirname(__DIR__, 2) . '/app/Security/SessionIdentity.php';

final class SessionIdentityTest extends TestCase
{
    private array $user = [
        'id' => 7,
        'password' => '$2y$10$example',
        'role' => 'admin',
        'id_ruangan' => null,
        'is_active' => 1,
    ];

    public function testCurrentIdentityMatches(): void
    {
        $fingerprint = SessionIdentity::fingerprint($this->user);

        self::assertTrue(SessionIdentity::matches($fingerprint, $this->user));
    }

    /** @dataProvider revokedIdentityProvider */
    public function testAuthorityChangesRevokeExistingFingerprint(array $changed): void
    {
        $fingerprint = SessionIdentity::fingerprint($this->user);

        self::assertFalse(SessionIdentity::matches($fingerprint, array_replace($this->user, $changed)));
    }

    public static function revokedIdentityProvider(): array
    {
        return [
            'password replacement' => [['password' => '$2y$10$changed']],
            'role downgrade' => [['role' => 'staff']],
            'room reassignment' => [['id_ruangan' => 4]],
            'account disable' => [['is_active' => 0]],
        ];
    }
}

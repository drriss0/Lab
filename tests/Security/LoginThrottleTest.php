<?php

use App\Security\LoginThrottle;
use PHPUnit\Framework\TestCase;

require_once dirname(__DIR__, 2) . '/app/Security/LoginThrottle.php';

final class LoginThrottleTest extends TestCase
{
    public function testDifferentAccountsCannotShareTargetCounter(): void
    {
        $first = LoginThrottle::keys('127.0.0.1', 'admin@example.test');
        $second = LoginThrottle::keys('127.0.0.1', 'staff@example.test');

        self::assertSame($first['global'], $second['global']);
        self::assertNotSame($first['target'], $second['target']);
    }

    public function testCredentialNormalizationIsStable(): void
    {
        self::assertSame(
            LoginThrottle::keys('127.0.0.1', ' User@Example.Test ')['target'],
            LoginThrottle::keys('127.0.0.1', 'user@example.test')['target']
        );
    }
}

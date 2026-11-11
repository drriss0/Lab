<?php

use App\Security\InventoryPolicy;
use PHPUnit\Framework\TestCase;

require_once dirname(__DIR__, 2) . '/app/Security/InventoryPolicy.php';

final class InventoryPolicyTest extends TestCase
{
    public function testKepalaLabCannotAccessAnotherRoom(): void
    {
        self::assertFalse(InventoryPolicy::canAccessRoom('kepala_lab', 10, 11));
        self::assertTrue(InventoryPolicy::canAccessRoom('kepala_lab', 10, 10));
        self::assertTrue(InventoryPolicy::canAccessRoom('admin', null, 11));
    }

    public function testLoanQuantityAndReturnStateAreValidated(): void
    {
        self::assertFalse(InventoryPolicy::isPositiveQuantity(0));
        self::assertFalse(InventoryPolicy::isPositiveQuantity(-1));
        self::assertTrue(InventoryPolicy::isPositiveQuantity(1));
        self::assertTrue(InventoryPolicy::isReturnableLoanStatus('dipinjam'));
        self::assertFalse(InventoryPolicy::isReturnableLoanStatus('kembali'));
    }

    public function testBulkIdsAreDeduplicatedAndBounded(): void
    {
        self::assertSame(['a', 'b'], InventoryPolicy::normalizeBulkIds(['a', 'a', 'b']));

        $this->expectException(InvalidArgumentException::class);
        InventoryPolicy::normalizeBulkIds(range(1, InventoryPolicy::MAX_BULK_ITEMS + 1));
    }
}

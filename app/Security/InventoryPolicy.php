<?php

namespace App\Security;

use InvalidArgumentException;

final class InventoryPolicy
{
    public const MAX_BULK_ITEMS = 100;
    public const LOAN_STATUSES = ['dipinjam', 'kembali', 'terlambat'];
    public const ACTIVE_LOAN_STATUSES = ['dipinjam', 'terlambat'];

    public static function isPositiveQuantity(int $quantity): bool
    {
        return $quantity > 0;
    }

    public static function isLoanStatus(string $status): bool
    {
        return in_array($status, self::LOAN_STATUSES, true);
    }

    public static function isReturnableLoanStatus(string $status): bool
    {
        return in_array($status, self::ACTIVE_LOAN_STATUSES, true);
    }

    public static function canAccessRoom(string $role, ?int $assignedRoomId, int $targetRoomId): bool
    {
        return $role !== 'kepala_lab'
            || ($assignedRoomId !== null && $assignedRoomId === $targetRoomId);
    }

    /** @return list<string> */
    public static function normalizeBulkIds(array $ids): array
    {
        $normalized = array_values(array_unique(array_filter(
            array_map(static fn ($id): string => trim((string) $id), $ids),
            static fn (string $id): bool => $id !== ''
        )));

        if ($normalized === []) {
            throw new InvalidArgumentException('Daftar item bulk tidak boleh kosong.');
        }

        if (count($normalized) > self::MAX_BULK_ITEMS) {
            throw new InvalidArgumentException('Maksimum ' . self::MAX_BULK_ITEMS . ' item per operasi bulk.');
        }

        return $normalized;
    }
}

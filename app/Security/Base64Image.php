<?php

namespace App\Security;

use InvalidArgumentException;

final class Base64Image
{
    public const MAX_BYTES = 2 * 1024 * 1024;
    public const MAX_PIXELS = 16_000_000;

    private const EXTENSIONS = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
    ];

    /** @return array{bytes: string, extension: string, mime: string} */
    public static function decodeDataUrl(string $dataUrl): array
    {
        if (! preg_match('/\Adata:(image\/(?:jpeg|png|webp));base64,([A-Za-z0-9+\/=]+)\z/D', $dataUrl, $matches)) {
            throw new InvalidArgumentException('Format foto kamera tidak valid.');
        }

        $maximumEncodedLength = (int) (ceil(self::MAX_BYTES / 3) * 4);
        if (strlen($matches[2]) > $maximumEncodedLength) {
            throw new InvalidArgumentException('Ukuran foto melebihi batas maksimal 2 MB.');
        }

        $decoded = base64_decode($matches[2], true);
        if ($decoded === false || strlen($decoded) > self::MAX_BYTES) {
            throw new InvalidArgumentException('Data foto kamera tidak valid atau terlalu besar.');
        }

        $imageInfo = @getimagesizefromstring($decoded);
        if ($imageInfo === false || ! isset($imageInfo['mime'], self::EXTENSIONS[$imageInfo['mime']])) {
            throw new InvalidArgumentException('Konten foto kamera bukan gambar yang didukung.');
        }

        if ($imageInfo['mime'] !== $matches[1]) {
            throw new InvalidArgumentException('Tipe konten foto tidak sesuai dengan data URL.');
        }

        $pixels = (int) $imageInfo[0] * (int) $imageInfo[1];
        if ($pixels <= 0 || $pixels > self::MAX_PIXELS) {
            throw new InvalidArgumentException('Dimensi foto terlalu besar.');
        }

        return [
            'bytes'     => $decoded,
            'extension' => self::EXTENSIONS[$imageInfo['mime']],
            'mime'      => $imageInfo['mime'],
        ];
    }
}

<?php

use App\Security\Base64Image;
use PHPUnit\Framework\TestCase;

require_once dirname(__DIR__, 2) . '/app/Security/Base64Image.php';

final class Base64ImageTest extends TestCase
{
    private const ONE_PIXEL_PNG = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII=';

    public function testSmallMatchingImageIsAccepted(): void
    {
        $image = Base64Image::decodeDataUrl('data:image/png;base64,' . self::ONE_PIXEL_PNG);

        self::assertSame('png', $image['extension']);
        self::assertSame('image/png', $image['mime']);
    }

    public function testDeclaredMimeMustMatchDecodedImage(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Base64Image::decodeDataUrl('data:image/jpeg;base64,' . self::ONE_PIXEL_PNG);
    }

    public function testEncodedPayloadIsRejectedBeforeDecodeWhenOversized(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Base64Image::decodeDataUrl(
            'data:image/png;base64,' . str_repeat('A', (int) ceil(Base64Image::MAX_BYTES / 3) * 4 + 4)
        );
    }
}

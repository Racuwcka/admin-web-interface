<?php

namespace BaconQrCode;

use BaconQrCode\Renderer\Image\ImagickImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;

class QrCode
{
    private static ?ImageRenderer $renderer = null;

    private static function initializeRenderer(int $size, string $format): void
    {
        if (is_null(self::$renderer)) {
            self::$renderer = new ImageRenderer(
                new RendererStyle(size: $size, margin: 0),
                new ImagickImageBackEnd($format)
            );
        }
    }

    private static function generateQrCode(mixed $data, int $size = 400, string $format = 'jpeg'): string
    {
        self::initializeRenderer(size: $size, format: $format);

        $writer = new Writer(self::$renderer);

        return $writer->writeString($data);
    }

    public static function renderFile(mixed $data, int $size, string $format): string
    {
        $qrCode = self::generateQrCode(data: $data, size: $size, format: $format);

        $tempFilePath = tempnam(sys_get_temp_dir(), 'qr_') . '.jpeg';
        file_put_contents($tempFilePath, $qrCode);

        return $tempFilePath;
    }

    public static function renderString(mixed $data): string
    {
        $qrCode = self::generateQrCode($data);

        return base64_encode($qrCode);
    }
}
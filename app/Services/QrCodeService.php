<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrCodeService
{
    /**
     * Generate an SVG QR code for verification URL.
     */
    public function generateSvg(string $certificateNumber): string
    {
        $verificationUrl = url('/verify/'.$certificateNumber);

        return (string) QrCode::format('svg')
            ->size(160)
            ->margin(1)
            ->errorCorrection('H')
            ->generate($verificationUrl);
    }

    /**
     * Generate Base64 encoded SVG for inline embedding.
     */
    public function generateBase64Svg(string $certificateNumber): string
    {
        $svg = $this->generateSvg($certificateNumber);

        return 'data:image/svg+xml;base64,'.base64_encode($svg);
    }

    /**
     * Save QR code to public storage and return relative path.
     */
    public function saveQrCode(string $certificateNumber): string
    {
        $svg = $this->generateSvg($certificateNumber);
        $relativePath = 'qrcodes/'.$certificateNumber.'.svg';

        Storage::disk('public')->put($relativePath, $svg);

        return $relativePath;
    }
}

<?php

namespace App\Services;

use App\Models\Certificate;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class CertificatePdfService
{
    protected QrCodeService $qrService;

    public function __construct(QrCodeService $qrService)
    {
        $this->qrService = $qrService;
    }

    /**
     * Ensure storage and font directories exist.
     */
    protected function ensureDirectoriesExist(): void
    {
        $fontPath = storage_path('fonts');
        if (! is_dir($fontPath)) {
            @mkdir($fontPath, 0755, true);
        }

        try {
            if (! Storage::disk('public')->exists('certificates')) {
                Storage::disk('public')->makeDirectory('certificates');
            }
        } catch (\Throwable $e) {
            // Ignore if directory creation is restricted
        }
    }

    /**
     * Get optimized base64 logo.
     */
    protected function getLogoBase64(): ?string
    {
        $pdfLogo = public_path('images/logo_pdf.png');
        if (file_exists($pdfLogo)) {
            return base64_encode(file_get_contents($pdfLogo));
        }

        $origLogo = public_path('images/logo.png');
        if (file_exists($origLogo)) {
            return base64_encode(file_get_contents($origLogo));
        }

        return null;
    }

    /**
     * Generate and save PDF document for a certificate.
     */
    public function generateAndSavePdf(Certificate $certificate): string
    {
        $this->ensureDirectoriesExist();

        $qrSvg = $this->qrService->generateSvg($certificate->certificate_number);
        $qrBase64 = base64_encode($qrSvg);
        $logoBase64 = $this->getLogoBase64();

        $pdf = Pdf::loadView('certificates.pdf', [
            'certificate' => $certificate,
            'cryptoKey' => $certificate->cryptoKey,
            'qrBase64' => $qrBase64,
            'logoBase64' => $logoBase64,
            'verificationUrl' => url('/verify/'.$certificate->certificate_number),
        ])->setPaper('a4', 'landscape')
            ->setOption('isRemoteEnabled', false)
            ->setOption('enable_font_subsetting', true)
            ->setOption('isHtml5ParserEnabled', true);

        $relativePath = 'certificates/'.$certificate->certificate_number.'.pdf';
        Storage::disk('public')->put($relativePath, $pdf->output());

        $certificate->update(['pdf_path' => $relativePath]);

        return $relativePath;
    }

    /**
     * Download or stream PDF for a certificate.
     */
    public function streamPdf(Certificate $certificate)
    {
        $this->ensureDirectoriesExist();

        $qrSvg = $this->qrService->generateSvg($certificate->certificate_number);
        $qrBase64 = base64_encode($qrSvg);
        $logoBase64 = $this->getLogoBase64();

        $pdf = Pdf::loadView('certificates.pdf', [
            'certificate' => $certificate,
            'cryptoKey' => $certificate->cryptoKey,
            'qrBase64' => $qrBase64,
            'logoBase64' => $logoBase64,
            'verificationUrl' => url('/verify/'.$certificate->certificate_number),
        ])->setPaper('a4', 'landscape')
            ->setOption('isRemoteEnabled', false)
            ->setOption('enable_font_subsetting', true)
            ->setOption('isHtml5ParserEnabled', true);

        return $pdf->stream($certificate->certificate_number.'.pdf');
    }
}

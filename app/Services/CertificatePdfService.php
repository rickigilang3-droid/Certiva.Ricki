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
     * Generate and save PDF document for a certificate.
     */
    public function generateAndSavePdf(Certificate $certificate): string
    {
        $qrSvg = $this->qrService->generateSvg($certificate->certificate_number);
        $qrBase64 = base64_encode($qrSvg);

        $logoPath = public_path('images/logo.png');
        $logoBase64 = file_exists($logoPath) ? base64_encode(file_get_contents($logoPath)) : null;

        $pdf = Pdf::loadView('certificates.pdf', [
            'certificate' => $certificate,
            'cryptoKey' => $certificate->cryptoKey,
            'qrBase64' => $qrBase64,
            'logoBase64' => $logoBase64,
            'verificationUrl' => url('/verify/'.$certificate->certificate_number),
        ])->setPaper('a4', 'landscape')
            ->setOption('isRemoteEnabled', true)
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
        $qrSvg = $this->qrService->generateSvg($certificate->certificate_number);
        $qrBase64 = base64_encode($qrSvg);

        $logoPath = public_path('images/logo.png');
        $logoBase64 = file_exists($logoPath) ? base64_encode(file_get_contents($logoPath)) : null;

        $pdf = Pdf::loadView('certificates.pdf', [
            'certificate' => $certificate,
            'cryptoKey' => $certificate->cryptoKey,
            'qrBase64' => $qrBase64,
            'logoBase64' => $logoBase64,
            'verificationUrl' => url('/verify/'.$certificate->certificate_number),
        ])->setPaper('a4', 'landscape')
            ->setOption('isRemoteEnabled', true)
            ->setOption('isHtml5ParserEnabled', true);

        return $pdf->stream($certificate->certificate_number.'.pdf');
    }
}

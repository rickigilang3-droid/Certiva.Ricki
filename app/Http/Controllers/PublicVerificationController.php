<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\VerificationLog;
use App\Services\CertificatePdfService;
use App\Services\CryptoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PublicVerificationController
{
    protected CryptoService $cryptoService;

    public function __construct(CryptoService $cryptoService)
    {
        $this->cryptoService = $cryptoService;
    }

    /**
     * Public verification portal homepage.
     */
    public function index(Request $request)
    {
        $query = $request->input('q');
        $certificate = null;
        $verificationResult = null;

        if ($query) {
            $certificateNumber = trim($query);

            return redirect()->route('verify.show', ['certificate_number' => $certificateNumber]);
        }

        // Fetch recent active key info for cryptographic status badge
        $activeKey = $this->cryptoService->getActiveKey();

        return view('public.verify', [
            'certificate' => null,
            'verificationResult' => null,
            'activeKey' => $activeKey,
            'query' => null,
        ]);
    }

    /**
     * Verify a specific certificate number.
     */
    public function show(Request $request, string $certificate_number)
    {
        $certificate_number = trim($certificate_number);
        $certificate = Certificate::with('cryptoKey')
            ->where('certificate_number', $certificate_number)
            ->first();

        $activeKey = $this->cryptoService->getActiveKey();

        if (! $certificate) {
            // Log failed query with 5-minute debouncing
            $existingLog = VerificationLog::where('certificate_number_queried', $certificate_number)
                ->where('ip_address', $request->ip())
                ->where('status', 'not_found')
                ->where('verified_at', '>=', now()->subMinutes(5))
                ->latest('verified_at')
                ->first();

            if ($existingLog) {
                $existingLog->update([
                    'user_agent' => $request->userAgent(),
                    'verified_at' => now(),
                ]);
            } else {
                VerificationLog::create([
                    'certificate_id' => null,
                    'certificate_number_queried' => $certificate_number,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'status' => 'not_found',
                    'verified_at' => now(),
                ]);
            }

            return view('public.verify', [
                'certificate' => null,
                'verificationResult' => [
                    'status' => 'not_found',
                    'message' => 'Nomor sertifikat tidak ditemukan di pangkalan data resmi universitas.',
                ],
                'activeKey' => $activeKey,
                'query' => $certificate_number,
            ]);
        }

        // Perform live cryptographic verification
        $integrity = $this->cryptoService->verifyCertificateIntegrity($certificate);
        $isSignatureValid = $integrity['isSignatureValid'];
        $isHashValid = $integrity['isHashMatch'];
        $recomputedHash = $integrity['recomputedHash'];

        $isTampered = $integrity['isTampered'];
        $isRevoked = ($certificate->status === 'revoked');

        $status = 'authentic';
        $statusMessage = 'Sertifikat Terverifikasi Asli & Sah secara Kriptografis';

        if ($isTampered) {
            $status = 'tampered';
            $statusMessage = 'PERINGATAN: Integritas kriptografis gagal! Data sertifikat telah diubah atau dipalsukan.';
        } elseif ($isRevoked) {
            $status = 'revoked';
            $statusMessage = 'PERHATIAN: Sertifikat ini telah DICABUT (REVOKED) oleh Universitas Bina Sarana Informatika.';
        }

        // Record verification audit log with debouncing (deduplicate rapid queries within 5 minutes from same IP)
        $existingLog = VerificationLog::where('certificate_number_queried', $certificate_number)
            ->where('ip_address', $request->ip())
            ->where('verified_at', '>=', now()->subMinutes(5))
            ->latest('verified_at')
            ->first();

        if ($existingLog) {
            $existingLog->update([
                'certificate_id' => $certificate->id,
                'status' => $status,
                'user_agent' => $request->userAgent(),
                'verified_at' => now(),
            ]);
        } else {
            VerificationLog::create([
                'certificate_id' => $certificate->id,
                'certificate_number_queried' => $certificate_number,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'status' => $status,
                'verified_at' => now(),
            ]);
        }

        return view('public.verify', [
            'certificate' => $certificate,
            'verificationResult' => [
                'status' => $status,
                'message' => $statusMessage,
                'isSignatureValid' => $isSignatureValid,
                'isHashValid' => $isHashValid,
                'recomputedHash' => $recomputedHash,
                'storedHash' => $certificate->hash_sha256,
                'isRevoked' => $isRevoked,
                'revocationReason' => $certificate->revocation_reason,
                'revokedAt' => $certificate->revoked_at,
            ],
            'activeKey' => $certificate->cryptoKey,
            'query' => $certificate_number,
        ]);
    }

    /**
     * Download the authentic stored PDF file directly from storage.
     */
    public function downloadPdf(string $certificate_number)
    {
        $certificate = Certificate::where('certificate_number', trim($certificate_number))->firstOrFail();
        $pdfSvc = app(CertificatePdfService::class);

        if (! $certificate->pdf_path || ! Storage::disk('public')->exists($certificate->pdf_path)) {
            $pdfSvc->generateAndSavePdf($certificate);
        }

        if ($certificate->pdf_path && Storage::disk('public')->exists($certificate->pdf_path)) {
            return Storage::disk('public')->download($certificate->pdf_path, "{$certificate->certificate_number}.pdf");
        }

        return $pdfSvc->streamPdf($certificate);
    }

    /**
     * View raw cryptographic proof JSON.
     */
    public function rawProof(string $certificate_number)
    {
        $certificate = Certificate::with('cryptoKey')->where('certificate_number', trim($certificate_number))->firstOrFail();

        return response()->json([
            'certificate_number' => $certificate->certificate_number,
            'recipient' => [
                'name' => $certificate->recipient_name,
                'identifier' => $certificate->recipient_identifier,
            ],
            'award' => [
                'title' => $certificate->title,
                'institution' => $certificate->institution_name,
                'department' => $certificate->department,
                'issued_date' => $certificate->issued_date->format('Y-m-d'),
            ],
            'status' => $certificate->status,
            'cryptography' => [
                'algorithm' => $certificate->cryptoKey->algorithm,
                'hash_algorithm' => $certificate->cryptoKey->hash_algorithm,
                'signature_scheme' => $certificate->cryptoKey->signature_scheme,
                'key_status' => $certificate->cryptoKey->status,
                'key_last_rotated' => $certificate->cryptoKey->last_rotated_at ? $certificate->cryptoKey->last_rotated_at->translatedFormat('d F Y') : '22 September 2026',
                'key_fingerprint' => $certificate->cryptoKey->fingerprint,
                'sha256_digest' => $certificate->hash_sha256,
                'signature_rsapss_base64' => $certificate->signature_rsapss,
                'canonical_payload' => $certificate->canonical_payload,
                'public_key_pem' => $certificate->cryptoKey->public_key,
            ],
        ], 200, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }

    /**
     * Verify a certificate by uploading its PDF document.
     */
    public function verifyPdfUpload(Request $request)
    {
        $request->validate([
            'pdf_file' => ['required', 'file', 'mimes:pdf', 'max:10240'],
        ], [
            'pdf_file.required' => 'Silakan pilih berkas PDF sertifikat yang ingin divalidasi.',
            'pdf_file.mimes' => 'Berkas harus berupa dokumen berekstensi .pdf.',
            'pdf_file.max' => 'Ukuran berkas PDF maksimal 10MB.',
        ]);

        $file = $request->file('pdf_file');
        $content = file_get_contents($file->getRealPath());

        $certNumber = $this->extractCertificateNumberFromPdf($content);

        if (! $certNumber) {
            return redirect()->route('verify.index')
                ->withErrors(['pdf_file' => 'Tidak ditemukan nomor registrasi sertifikat (format CERT-...) pada dokumen PDF yang diunggah. Pastikan dokumen merupakan sertifikat resmi terbitan Certiva.']);
        }

        return redirect()->route('verify.show', ['certificate_number' => $certNumber]);
    }

    /**
     * Extract certificate number from raw PDF stream or metadata.
     */
    protected function extractCertificateNumberFromPdf(string $content): ?string
    {
        // 1. Direct search on raw or uncompressed PDF text
        if (preg_match('/CERT-\d{4}-[A-Za-z0-9_-]+/i', $content, $matches)) {
            return trim($matches[0]);
        }

        // 2. Search within compressed stream objects (FlateDecode)
        if (preg_match_all('/stream[\r\n]+(.*?)[\r\n]+endstream/s', $content, $streams)) {
            foreach ($streams[1] as $stream) {
                $uncompressed = @gzuncompress($stream);
                if ($uncompressed && preg_match('/CERT-\d{4}-[A-Za-z0-9_-]+/i', $uncompressed, $matches)) {
                    return trim($matches[0]);
                }
            }
        }

        // 3. Fallback: match by hash
        $fileSha256 = hash('sha256', $content);
        $cert = Certificate::where('hash_sha256', $fileSha256)->first();
        if ($cert) {
            return $cert->certificate_number;
        }

        return null;
    }
}

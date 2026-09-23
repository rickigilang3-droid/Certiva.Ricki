<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\User;
use App\Services\CertificatePdfService;
use App\Services\CryptoService;
use App\Services\QrCodeService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CertificateController
{
    protected CryptoService $cryptoService;

    protected CertificatePdfService $pdfService;

    protected QrCodeService $qrService;

    public function __construct(
        CryptoService $cryptoService,
        CertificatePdfService $pdfService,
        QrCodeService $qrService
    ) {
        $this->cryptoService = $cryptoService;
        $this->pdfService = $pdfService;
        $this->qrService = $qrService;
    }

    /**
     * Display a listing of certificates.
     */
    public function index(Request $request)
    {
        $query = Certificate::with('cryptoKey')->latest();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('certificate_number', 'like', "%{$search}%")
                    ->orWhere('recipient_name', 'like', "%{$search}%")
                    ->orWhere('recipient_identifier', 'like', "%{$search}%")
                    ->orWhere('title', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $certificates = $query->paginate(10)->withQueryString();

        $stats = [
            'total' => Certificate::count(),
            'active' => Certificate::where('status', 'active')->count(),
            'revoked' => Certificate::where('status', 'revoked')->count(),
        ];

        return view('certificates.index', [
            'certificates' => $certificates,
            'stats' => $stats,
        ]);
    }

    /**
     * Show form to issue a new certificate.
     */
    public function create()
    {
        $activeKey = $this->cryptoService->getActiveKey();
        $seq = Certificate::count() + 101;
        do {
            $suggestedNumber = 'CERT-'.date('Y').'-CAMPUS-'.str_pad((string) $seq, 5, '0', STR_PAD_LEFT);
            $seq++;
        } while (Certificate::where('certificate_number', $suggestedNumber)->exists());
        $students = User::where('role', User::ROLE_MAHASISWA)
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'identifier']);

        return view('certificates.create', [
            'activeKey' => $activeKey,
            'suggestedNumber' => $suggestedNumber,
            'students' => $students,
        ]);
    }

    /**
     * Store and cryptographically sign a newly issued certificate.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'certificate_number' => ['required', 'string', 'unique:certificates,certificate_number'],
            'recipient_name' => ['required', 'string', 'max:255'],
            'recipient_identifier' => ['nullable', 'string', 'max:100'],
            'recipient_email' => ['nullable', 'email', 'max:255'],
            'title' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:100'],
            'template' => ['nullable', 'string', 'in:formal,modern,achievement,seminar'],
            'department' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:500'],
            'institution_name' => ['required', 'string', 'max:255'],
            'signatory_name' => ['required', 'string', 'max:255'],
            'signatory_title' => ['required', 'string', 'max:255'],
            'issued_date' => ['required', 'date'],
            'expiry_date' => ['nullable', 'date', 'after_or_equal:issued_date'],
        ]);

        $activeKey = $this->cryptoService->getActiveKey();

        // 1. Build deterministic canonical payload
        $canonicalPayload = $this->cryptoService->buildCanonicalPayload([
            'certificate_number' => $validated['certificate_number'],
            'recipient_name' => $validated['recipient_name'],
            'recipient_identifier' => $validated['recipient_identifier'] ?? '',
            'title' => $validated['title'],
            'institution_name' => $validated['institution_name'],
            'department' => $validated['department'] ?? '',
            'issued_date' => $validated['issued_date'],
            'expiry_date' => $validated['expiry_date'] ?? null,
            'signatory_name' => $validated['signatory_name'],
            'signatory_title' => $validated['signatory_title'],
        ]);

        // 2. Cryptographically sign using RSA-2048 with RSA-PSS & SHA-256
        $signData = $this->cryptoService->signWithActiveKey($canonicalPayload, $activeKey);

        // 3. Save QR Code
        $qrPath = $this->qrService->saveQrCode($validated['certificate_number']);

        // 4. Create Certificate Record
        $certificate = Certificate::create([
            'certificate_number' => $validated['certificate_number'],
            'recipient_name' => $validated['recipient_name'],
            'recipient_identifier' => $validated['recipient_identifier'] ?? null,
            'recipient_email' => $validated['recipient_email'] ?? null,
            'title' => $validated['title'],
            'category' => $validated['category'],
            'template' => $validated['template'] ?? 'formal',
            'department' => $validated['department'] ?? null,
            'description' => $validated['description'] ?? null,
            'institution_name' => $validated['institution_name'],
            'signatory_name' => $validated['signatory_name'],
            'signatory_title' => $validated['signatory_title'],
            'issued_date' => $validated['issued_date'],
            'expiry_date' => $validated['expiry_date'] ?? null,
            'crypto_key_id' => $activeKey->id,
            'canonical_payload' => $canonicalPayload,
            'hash_sha256' => $signData['hash_sha256'],
            'signature_rsapss' => $signData['signature_rsapss'],
            'status' => 'active',
            'qr_path' => $qrPath,
        ]);

        // 5. Generate and store PDF
        $pdfPath = $this->pdfService->generateAndSavePdf($certificate);

        return redirect()->route('certificates.show', $certificate)
            ->with('status', "Sertifikat {$certificate->certificate_number} berhasil diterbitkan dan ditandatangani secara kriptografis (RSA-PSS).");
    }

    /**
     * Display a specific certificate.
     */
    public function show(Certificate $certificate)
    {
        $certificate->load(['cryptoKey', 'verificationLogs' => function ($q) {
            $q->latest()->limit(15);
        }]);

        // Live check
        $integrity = $this->cryptoService->verifyCertificateIntegrity($certificate);

        return view('certificates.show', [
            'certificate' => $certificate,
            'isValidSignature' => $integrity['isSignatureValid'],
            'isHashValid' => $integrity['isHashMatch'],
            'recomputedHash' => $integrity['recomputedHash'],
        ]);
    }

    /**
     * Revoke a certificate.
     */
    public function revoke(Request $request, Certificate $certificate)
    {
        $validated = $request->validate([
            'revocation_reason' => ['required', 'string', 'max:500'],
        ]);

        $certificate->update([
            'status' => 'revoked',
            'revocation_reason' => $validated['revocation_reason'],
            'revoked_at' => Carbon::now(),
        ]);

        // Regenerate stored PDF to immediately include the revocation watermark
        $this->pdfService->generateAndSavePdf($certificate);

        return redirect()->route('certificates.show', $certificate)
            ->with('status', "Sertifikat {$certificate->certificate_number} telah berhasil dicabut (Revoked).");
    }

    /**
     * Download certificate PDF.
     */
    public function downloadPdf(Request $request, Certificate $certificate)
    {
        if ($request->boolean('preview') || $request->boolean('stream')) {
            return $this->pdfService->streamPdf($certificate);
        }

        if (! $certificate->pdf_path || ! Storage::disk('public')->exists($certificate->pdf_path)) {
            $path = $this->pdfService->generateAndSavePdf($certificate);
            $certificate->pdf_path = $path;
        }

        if ($certificate->pdf_path && Storage::disk('public')->exists($certificate->pdf_path)) {
            return Storage::disk('public')->download($certificate->pdf_path, "{$certificate->certificate_number}.pdf");
        }

        return $this->pdfService->streamPdf($certificate);
    }

    /**
     * Stream and preview certificate PDF directly in browser.
     */
    public function previewPdf(Certificate $certificate)
    {
        return $this->pdfService->streamPdf($certificate);
    }
}

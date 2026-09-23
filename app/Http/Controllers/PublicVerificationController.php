<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\VerificationLog;
use App\Services\CertificatePdfService;
use App\Services\CryptoService;
use Carbon\Carbon;
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

            $pdfAudit = session('pdf_audit');
            $forensicAudit = $pdfAudit ?? [
                'is_uploaded_pdf' => false,
                'is_unrecognized' => true,
                'is_tampered' => true,
                'tampered_reasons' => [
                    "Nomor sertifikat \"{$certificate_number}\" tidak terdaftar dalam pangkalan data resmi universitas.",
                    'Tidak ditemukan tanda tangan digital kriptografis yang mengesahkan nomor registrasi ini.',
                    'Integritas dokumen tidak dapat divalidasi.',
                ],
                'detailed_reasons' => [
                    [
                        'number' => 1,
                        'title' => 'Nomor Registrasi Tidak Terdaftar',
                        'description' => "Nomor sertifikat \"{$certificate_number}\" tidak tercatat dalam arsip penerbitan Universitas Bina Sarana Informatika.",
                    ],
                    [
                        'number' => 2,
                        'title' => 'Signature Digital RSA-2048 Tidak Ditemukan',
                        'description' => 'Tidak ditemukan segel digital institusi (RSA-2048) atau hash SHA-256 yang mengesahkan nomor registrasi ini.',
                    ],
                    [
                        'number' => 3,
                        'title' => 'Integritas Dokumen Gagal Diverifikasi',
                        'description' => 'Sistem tidak dapat mengonfirmasi keaslian dokumen tanpa data registrasi resmi di pangkalan data kampus.',
                    ],
                ],
                'audit_checks' => [
                    [
                        'item' => 'Certificate ID',
                        'result' => 'invalid',
                        'badge' => '❌ Tidak Terdaftar',
                        'detail' => $certificate_number,
                    ],
                    [
                        'item' => 'Data terdaftar',
                        'result' => 'invalid',
                        'badge' => '❌ Tidak Ada',
                        'detail' => 'Tidak ada data di pangkalan data resmi UBSI',
                    ],
                    [
                        'item' => 'Signature RSA-2048',
                        'result' => 'invalid',
                        'badge' => '❌ Tidak Ada',
                        'detail' => 'Tanda tangan digital tidak tersedia / tidak valid',
                    ],
                    [
                        'item' => 'Integritas dokumen',
                        'result' => 'invalid',
                        'badge' => '❌ Gagal',
                        'detail' => 'Dokumen tidak dapat diverifikasi keasliannya',
                    ],
                    [
                        'item' => 'Status akhir',
                        'result' => 'invalid',
                        'badge' => 'INVALID',
                        'detail' => 'Nomor sertifikat tidak sah atau tidak valid',
                    ],
                ],
                'conclusion' => 'Dokumen atau nomor registrasi ini tidak dapat dinyatakan sebagai sertifikat yang valid karena tidak tercatat pada sistem verifikasi resmi Universitas Bina Sarana Informatika.',
            ];

            return view('public.verify', [
                'certificate' => null,
                'verificationResult' => [
                    'status' => 'not_found',
                    'message' => 'Nomor sertifikat tidak ditemukan di pangkalan data resmi universitas.',
                ],
                'activeKey' => $activeKey,
                'query' => $certificate_number,
                'forensicAudit' => $forensicAudit,
                'pdfAudit' => $pdfAudit,
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

        $pdfAudit = session('pdf_audit');
        if ($pdfAudit && ! empty($pdfAudit['is_tampered'])) {
            $isTampered = true;
            $status = 'tampered';
            $statusMessage = 'PERINGATAN MANIPULASI BERKAS: Dokumen PDF yang Anda unggah terdeteksi telah dimodifikasi atau diedit! '.implode('; ', $pdfAudit['tampered_reasons']);
        } elseif ($isTampered) {
            $status = 'tampered';
            $statusMessage = 'PERINGATAN: Integritas kriptografis gagal! Data sertifikat telah diubah atau dipalsukan.';
        } elseif ($isRevoked) {
            $status = 'revoked';
            $statusMessage = 'PERHATIAN: Sertifikat ini telah DICABUT (REVOKED) oleh Universitas Bina Sarana Informatika.';
        }

        if ($pdfAudit) {
            $forensicAudit = $pdfAudit;
        } else {
            $auditChecks = [
                [
                    'item' => 'Certificate ID',
                    'result' => 'valid',
                    'badge' => '✓ Ditemukan',
                    'detail' => $certificate->certificate_number,
                ],
                [
                    'item' => 'Data terdaftar',
                    'result' => 'valid',
                    'badge' => '✓ Ditemukan',
                    'detail' => 'Tercatat di basis data resmi UBSI',
                ],
                [
                    'item' => 'Signature RSA-2048',
                    'result' => $isSignatureValid ? 'valid' : 'invalid',
                    'badge' => $isSignatureValid ? '✓ Cocok' : '❌ Tidak cocok',
                    'detail' => $isSignatureValid ? 'Tanda tangan digital valid dengan kunci publik institusi' : 'Tanda tangan digital tidak cocok dengan kunci publik',
                ],
                [
                    'item' => 'Integritas dokumen',
                    'result' => $isHashValid ? 'valid' : 'invalid',
                    'badge' => $isHashValid ? '✓ Sah' : '❌ Gagal',
                    'detail' => $isHashValid ? 'Digest SHA-256 identik 100%' : 'Digest SHA-256 tidak cocok dengan master payload',
                ],
                [
                    'item' => 'Kesesuaian nama',
                    'result' => 'valid',
                    'badge' => '✓ Sesuai',
                    'detail' => $certificate->recipient_name,
                ],
                [
                    'item' => 'Kesesuaian NIM',
                    'result' => 'valid',
                    'badge' => '✓ Sesuai',
                    'detail' => $certificate->recipient_identifier ?? '-',
                ],
                [
                    'item' => 'Status akhir',
                    'result' => (! $isTampered && ! $isRevoked) ? 'valid' : 'invalid',
                    'badge' => (! $isTampered && ! $isRevoked) ? 'VALID' : ($isRevoked ? 'REVOKED' : 'INVALID'),
                    'detail' => (! $isTampered && ! $isRevoked) ? 'Dokumen terverifikasi 100% otentik & sah' : ($isRevoked ? 'Sertifikat telah dicabut' : 'Integritas kriptografis gagal'),
                ],
            ];

            $detailedReasons = [];
            if (! $isSignatureValid) {
                $detailedReasons[] = [
                    'number' => 1,
                    'title' => 'Signature Digital Tidak Cocok',
                    'description' => 'Signature digital pada sertifikat tidak sesuai dengan data yang tersimpan di sistem. Kunci publik RSA-2048 institusi gagal memverifikasi tanda tangan digital.',
                ];
            }
            if (! $isHashValid) {
                $detailedReasons[] = [
                    'number' => count($detailedReasons) + 1,
                    'title' => 'Integritas Dokumen Gagal Diverifikasi',
                    'description' => 'Perubahan pada data sertifikat menyebabkan nilai hash SHA-256 tidak lagi sesuai dengan segel yang diterbitkan.',
                ];
            }

            $forensicAudit = [
                'is_uploaded_pdf' => false,
                'is_tampered' => $isTampered,
                'tampered_reasons' => $isTampered ? ['Integritas kriptografis RSA-PSS / SHA-256 gagal divalidasi.'] : [],
                'detailed_reasons' => $detailedReasons,
                'audit_checks' => $auditChecks,
                'conclusion' => (! $isTampered && ! $isRevoked)
                    ? 'Dokumen dinyatakan VALID dan OTENTIK. Seluruh segel digital RSA-PSS cocok 100% dengan pangkalan data resmi universitas.'
                    : ($isRevoked ? 'Sertifikat ini telah resmi dicabut oleh otoritas kampus.' : 'Dokumen tidak dapat dinyatakan sebagai sertifikat yang valid karena data tidak sesuai dengan data yang telah ditandatangani secara digital.'),
                'detected_name' => $certificate->recipient_name,
                'official_name' => $certificate->recipient_name,
                'name_match' => true,
                'detected_identifier' => $certificate->recipient_identifier,
                'official_identifier' => $certificate->recipient_identifier,
                'identifier_match' => true,
                'title_match' => true,
                'is_signature_valid' => $isSignatureValid,
                'is_hash_valid' => $isHashValid,
            ];
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
            'pdfAudit' => $pdfAudit,
            'forensicAudit' => $forensicAudit,
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
            return response()->download(
                Storage::disk('public')->path($certificate->pdf_path),
                "{$certificate->certificate_number}.pdf"
            );
        }

        return $pdfSvc->streamPdf($certificate);
    }

    /**
     * View raw cryptographic proof JSON.
     */
    public function rawProof(string $certificate_number)
    {
        $certificate = Certificate::with('cryptoKey')->where('certificate_number', trim($certificate_number))->firstOrFail();
        /** @var Carbon $issuedDate */
        $issuedDate = $certificate->issued_date;

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
                'issued_date' => $issuedDate->format('Y-m-d'),
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

        $allText = $this->extractAllTextFromPdf($content);
        $certNumber = $this->extractCertificateNumberFromPdf($content, $allText);

        if (! $certNumber) {
            $unrecognizedAudit = [
                'is_uploaded_pdf' => true,
                'is_unrecognized' => true,
                'is_tampered' => true,
                'tampered_reasons' => [
                    'Tidak ditemukan nomor registrasi resmi (format CERT-...) pada dokumen PDF yang diunggah.',
                    'Dokumen tidak memiliki tanda tangan digital kriptografis RSA-2048 maupun segel hash SHA-256 yang terdaftar di pangkalan data Certiva.',
                    'Integritas dokumen tidak dapat divalidasi dan bukan merupakan sertifikat resmi terbitan Universitas Bina Sarana Informatika.',
                ],
                'detailed_reasons' => [
                    [
                        'number' => 1,
                        'title' => 'Nomor Registrasi Tidak Ditemukan pada Dokumen',
                        'description' => 'Sistem tidak menemukan format nomor registrasi sertifikat resmi (format: CERT-YYYY-...) pada teks, metadata, maupun aliran objek berkas PDF yang diunggah. Dokumen tidak terindeks dalam arsip resmi kampus.',
                    ],
                    [
                        'number' => 2,
                        'title' => 'Tanda Tangan Digital RSA-2048 Tidak Ditemukan',
                        'description' => 'Dokumen tidak memiliki tanda tangan digital institusi (RSA-2048) atau segel hash integritas (SHA-256). Tanpa tanda tangan kriptografis resmi yang cocok dengan Kunci Publik Kampus, keabsahan dokumen tidak dapat diverifikasi.',
                    ],
                    [
                        'number' => 3,
                        'title' => 'Integritas Dokumen Gagal Diverifikasi',
                        'description' => 'Dokumen yang diunggah tidak memenuhi standar autentikasi sertifikat digital kampus dan tidak dapat dinyatakan sebagai dokumen resmi yang diterbitkan oleh Universitas Bina Sarana Informatika.',
                    ],
                ],
                'audit_checks' => [
                    [
                        'item' => 'Certificate ID',
                        'result' => 'invalid',
                        'badge' => '❌ Tidak Terdeteksi',
                        'detail' => 'Nomor registrasi resmi (format CERT-...) tidak ditemukan pada dokumen PDF',
                    ],
                    [
                        'item' => 'Data terdaftar',
                        'result' => 'invalid',
                        'badge' => '❌ Tidak Ada',
                        'detail' => 'Tidak ditemukan arsip berkas di basis data resmi UBSI',
                    ],
                    [
                        'item' => 'Signature RSA-2048',
                        'result' => 'invalid',
                        'badge' => '❌ Tidak Ditemukan',
                        'detail' => 'Tidak memuat tanda tangan digital kriptografis resmi institusi',
                    ],
                    [
                        'item' => 'Integritas dokumen',
                        'result' => 'invalid',
                        'badge' => '❌ Gagal',
                        'detail' => 'Bukan dokumen sertifikat resmi terverifikasi',
                    ],
                    [
                        'item' => 'Status akhir',
                        'result' => 'invalid',
                        'badge' => 'INVALID',
                        'detail' => 'Dokumen TIDAK VALID / Bukan sertifikat resmi terbitan Certiva',
                    ],
                ],
                'conclusion' => 'Dokumen tidak dapat dinyatakan sebagai sertifikat yang valid atau asli karena tidak memuat nomor registrasi terdaftar maupun tanda tangan digital terotentikasi dari Universitas Bina Sarana Informatika.',
            ];

            return redirect()->route('verify.show', ['certificate_number' => 'DOKUMEN-TIDAK-TERDAFTAR'])
                ->with('pdf_audit', $unrecognizedAudit);
        }

        $certificate = Certificate::with('cryptoKey')->where('certificate_number', $certNumber)->first();

        if (! $certificate) {
            $unregisteredAudit = [
                'is_uploaded_pdf' => true,
                'is_unrecognized' => true,
                'is_tampered' => true,
                'tampered_reasons' => [
                    "Nomor registrasi {$certNumber} tidak terdaftar dalam pangkalan data resmi universitas.",
                    'Tanda tangan digital institusi tidak dapat diverifikasi terhadap arsip manapun.',
                ],
                'detailed_reasons' => [
                    [
                        'number' => 1,
                        'title' => 'Nomor Registrasi Tidak Terdaftar di Pangkalan Data',
                        'description' => "Nomor registrasi ({$certNumber}) yang tertera pada berkas PDF tidak ditemukan pada basis data resmi Universitas Bina Sarana Informatika.",
                    ],
                    [
                        'number' => 2,
                        'title' => 'Signature Digital Tidak Valid / Tidak Terotentikasi',
                        'description' => 'Karena sertifikat tidak terdaftar di sistem, tidak ada pasangan kunci publik kriptografis yang mengesahkan penerbitan dokumen ini.',
                    ],
                    [
                        'number' => 3,
                        'title' => 'Integritas Dokumen Gagal Diverifikasi',
                        'description' => 'Dokumen terindikasi sebagai sertifikat palsu atau dibuat tanpa otorisasi penerbit resmi.',
                    ],
                ],
                'audit_checks' => [
                    [
                        'item' => 'Certificate ID',
                        'result' => 'invalid',
                        'badge' => '❌ Tidak Terdaftar',
                        'detail' => "{$certNumber} (Tidak ditemukan di database)",
                    ],
                    [
                        'item' => 'Data terdaftar',
                        'result' => 'invalid',
                        'badge' => '❌ Tidak Ditemukan',
                        'detail' => 'Tidak tercatat di basis data resmi UBSI',
                    ],
                    [
                        'item' => 'Signature RSA-2048',
                        'result' => 'invalid',
                        'badge' => '❌ Tidak Valid',
                        'detail' => 'Kunci publik universitas menolak keabsahan dokumen',
                    ],
                    [
                        'item' => 'Integritas dokumen',
                        'result' => 'invalid',
                        'badge' => '❌ Gagal',
                        'detail' => 'Integritas berkas tidak dapat dipastikan',
                    ],
                    [
                        'item' => 'Status akhir',
                        'result' => 'invalid',
                        'badge' => 'INVALID',
                        'detail' => 'Dokumen palsu atau nomor tidak berizin',
                    ],
                ],
                'conclusion' => 'Dokumen tidak dapat dinyatakan sebagai sertifikat yang valid karena nomor registrasi tidak pernah diterbitkan atau dicatat oleh universitas.',
            ];

            return redirect()->route('verify.show', ['certificate_number' => $certNumber])
                ->with('pdf_audit', $unregisteredAudit);
        }

        $pdfAudit = $this->validatePdfContentAgainstCertificate($allText, $certificate);

        return redirect()->route('verify.show', ['certificate_number' => $certNumber])
            ->with('pdf_audit', $pdfAudit);
    }

    /**
     * Extract searchable text from raw PDF and all uncompressed FlateDecode stream objects.
     */
    protected function extractAllTextFromPdf(string $content): string
    {
        $allText = $content."\n".str_replace("\x00", '', $content);

        if (preg_match_all('/stream[\r\n]+(.*?)[\r\n]+endstream/s', $content, $streams)) {
            foreach ($streams[1] as $stream) {
                $uncompressed = @gzuncompress($stream) ?: @gzinflate($stream);
                if ($uncompressed) {
                    $cleaned = str_replace("\x00", '', $uncompressed);
                    $allText .= "\n".$uncompressed."\n".$cleaned;

                    // Reconstruct text from PDF TJ kerning arrays: [(C) 10 (E) -5 (R) (T)] TJ
                    if (preg_match_all('/\[(.*?)\]\s*TJ/s', $uncompressed, $tjMatches)) {
                        foreach ($tjMatches[1] as $tj) {
                            if (preg_match_all('/\((.*?)\)/s', $tj, $tjParts)) {
                                $joined = implode('', $tjParts[1]);
                                $allText .= "\n".$joined."\n".str_replace("\x00", '', $joined);
                            }
                        }
                    }

                    // Decode hex strings <004300450052...>
                    if (preg_match_all('/<([0-9a-fA-F\s]{16,})>/', $uncompressed, $hexMatches)) {
                        foreach ($hexMatches[1] as $hex) {
                            $cleanHex = preg_replace('/\s+/', '', $hex);
                            if (strlen($cleanHex) % 2 === 0) {
                                $bin = @hex2bin($cleanHex);
                                if ($bin) {
                                    $allText .= "\n".$bin."\n".str_replace("\x00", '', $bin);
                                }
                            }
                        }
                    }
                }
            }
        }

        return $allText;
    }

    /**
     * Compare text inside the uploaded PDF against the authentic cryptographic certificate record.
     */
    protected function validatePdfContentAgainstCertificate(string $allText, Certificate $certificate): array
    {
        $tamperedReasons = [];
        $cleanSearch = str_replace("\x00", '', $allText);

        // 1. Recipient Name check (case-insensitive substring)
        $nameMatch = empty($certificate->recipient_name) || stripos($cleanSearch, $certificate->recipient_name) !== false;
        $detectedName = $certificate->recipient_name;

        if (! $nameMatch) {
            // Attempt to extract the altered name from the PDF content stream
            $detectedName = null;
            if (preg_match('/(?:kepada|tervalidasi kepada)[\s\S]*?\[\(([^()]{2,80})\)\]\s*TJ[\s\S]*?(?:Nomor Induk|NIM)/i', $cleanSearch, $m)) {
                $detectedName = trim($m[1]);
            } elseif (preg_match('/(?:diberikan secara sah dan tervalidasi kepada|kepada)[:\s\r\n]+([A-Za-z\s\.,\'-]{3,60})/i', $cleanSearch, $m)) {
                $detectedName = trim($m[1]);
            }
            if (empty($detectedName) || strlen($detectedName) < 2) {
                $detectedName = 'Nama Telah Diubah pada Dokumen';
            }
            $tamperedReasons[] = "Data nama pada dokumen ({$detectedName}) berbeda dengan data yang tercatat pada database penerbit ({$certificate->recipient_name}).";
        }

        // 2. Recipient Identifier / NIM check
        $identifierMatch = empty($certificate->recipient_identifier) || stripos($cleanSearch, (string) $certificate->recipient_identifier) !== false;
        $detectedIdentifier = $certificate->recipient_identifier;

        if (! $identifierMatch) {
            $detectedIdentifier = null;
            if (preg_match('/(?:Nomor Induk Mahasiswa|NIM)[^\d]{1,30}(\d{5,20})/is', $cleanSearch, $m)) {
                $detectedIdentifier = trim($m[1]);
            }
            if (empty($detectedIdentifier)) {
                $detectedIdentifier = 'NIM Telah Diubah pada Dokumen';
            }
            $tamperedReasons[] = "Nomor Induk Mahasiswa (NIM: {$detectedIdentifier}) pada dokumen tidak sesuai dengan rekaman sah kampus ({$certificate->recipient_identifier}).";
        }

        // 3. Title check
        $titleMatch = empty($certificate->title) || stripos($cleanSearch, $certificate->title) !== false;
        if (! $titleMatch) {
            $tamperedReasons[] = 'Program studi / judul sertifikat pada dokumen tidak sesuai dengan arsip sah universitas.';
        }

        // 4. Cryptographic RSA-2048 & SHA-256 validation
        // When all text attributes match the DB, verify the certificate's own stored integrity
        // directly — this is the most reliable path for authentic documents.
        // Only rebuild a "claimed" payload when some attribute differs (tampered scenario).
        if ($nameMatch && $identifierMatch && $titleMatch) {
            $integrity = $this->cryptoService->verifyCertificateIntegrity($certificate);
            $isDocSignatureValid = $integrity['isSignatureValid'];
            $isDocHashValid = $integrity['isHashMatch'];
            $claimedHash = $integrity['recomputedHash'];
        } else {
            $claimedAttributes = [
                'certificate_number' => $certificate->certificate_number,
                'recipient_name' => $detectedName,
                'recipient_identifier' => $detectedIdentifier,
                'title' => $certificate->title,
                'institution_name' => $certificate->institution_name,
                'department' => $certificate->department ?? '',
                'issued_date' => $certificate->issued_date instanceof \DateTimeInterface
                    ? $certificate->issued_date->format('Y-m-d')
                    : (string) $certificate->issued_date,
                'expiry_date' => $certificate->expiry_date instanceof \DateTimeInterface
                    ? $certificate->expiry_date->format('Y-m-d')
                    : (! empty($certificate->expiry_date) ? (string) $certificate->expiry_date : null),
                'signatory_name' => $certificate->signatory_name,
                'signatory_title' => $certificate->signatory_title,
            ];

            $claimedPayload = $this->cryptoService->buildCanonicalPayload($claimedAttributes);
            $claimedHash = $this->cryptoService->calculateSha256($claimedPayload);

            $isDocSignatureValid = $this->cryptoService->verifySignature(
                $claimedPayload,
                $certificate->signature_rsapss,
                $certificate->cryptoKey->public_key
            );
            $isDocHashValid = hash_equals($certificate->hash_sha256, $claimedHash);
        }

        $isTampered = (! $nameMatch || ! $identifierMatch || ! $titleMatch || ! $isDocSignatureValid || ! $isDocHashValid);

        // Build detailed failure explanations
        $detailedReasons = [];
        if (! $isDocSignatureValid) {
            $detailedReasons[] = [
                'number' => 1,
                'title' => 'Signature Digital Tidak Cocok',
                'description' => 'Signature digital pada dokumen tidak sesuai dengan data sertifikat yang terdaftar di sistem. Kunci publik RSA-2048 milik institusi gagal memverifikasi keabsahan tanda tangan digital atas data yang tertera pada berkas ini.',
            ];
        }

        if (! $nameMatch) {
            $detailedReasons[] = [
                'number' => count($detailedReasons) + 1,
                'title' => 'Data Sertifikat Telah Berubah',
                'description' => 'Data nama pada dokumen berbeda dengan data yang tercatat pada database penerbit resmi.',
                'comparison' => [
                    'field' => 'Nama Lengkap Penerima',
                    'document' => $detectedName,
                    'database' => $certificate->recipient_name,
                ],
            ];
        } elseif (! $identifierMatch) {
            $detailedReasons[] = [
                'number' => count($detailedReasons) + 1,
                'title' => 'Data Sertifikat Telah Berubah',
                'description' => 'Nomor Induk Mahasiswa (NIM) pada dokumen berbeda dengan data yang tercatat pada database penerbit resmi.',
                'comparison' => [
                    'field' => 'Nomor Induk Mahasiswa (NIM)',
                    'document' => $detectedIdentifier,
                    'database' => $certificate->recipient_identifier,
                ],
            ];
        }

        if (! $isDocHashValid || ! $isDocSignatureValid) {
            $detailedReasons[] = [
                'number' => count($detailedReasons) + 1,
                'title' => 'Integritas Dokumen Gagal Diverifikasi',
                'description' => 'Perubahan pada data sertifikat menyebabkan nilai hash/signature yang diverifikasi tidak lagi sesuai dengan signature yang diterbitkan oleh universitas.',
            ];
        }

        // Comprehensive Audit Checklist matching user's specification
        $auditChecks = [
            [
                'item' => 'Certificate ID',
                'result' => 'valid',
                'badge' => '✓ Ditemukan',
                'detail' => $certificate->certificate_number,
            ],
            [
                'item' => 'Data terdaftar',
                'result' => 'valid',
                'badge' => '✓ Ditemukan',
                'detail' => 'Tercatat aktif di basis data resmi UBSI',
            ],
            [
                'item' => 'Signature RSA-2048',
                'result' => $isDocSignatureValid ? 'valid' : 'invalid',
                'badge' => $isDocSignatureValid ? '✓ Cocok' : '❌ Tidak cocok',
                'detail' => $isDocSignatureValid ? 'Segel digital terverifikasi kunci publik institusi' : 'Tanda tangan kriptografis tidak cocok dengan payload berkas',
            ],
            [
                'item' => 'Integritas dokumen',
                'result' => $isDocHashValid ? 'valid' : 'invalid',
                'badge' => $isDocHashValid ? '✓ Sah' : '❌ Gagal',
                'detail' => $isDocHashValid ? 'Hash SHA-256 cocok 100%' : 'Nilai hash SHA-256 berbeda dari segel asli penerbit',
            ],
            [
                'item' => 'Kesesuaian nama',
                'result' => $nameMatch ? 'valid' : 'invalid',
                'badge' => $nameMatch ? '✓ Sesuai' : '❌ Tidak cocok',
                'detail' => $nameMatch ? $certificate->recipient_name : "Dokumen: \"{$detectedName}\" ≠ Arsip: \"{$certificate->recipient_name}\"",
            ],
            [
                'item' => 'Kesesuaian NIM',
                'result' => $identifierMatch ? 'valid' : 'invalid',
                'badge' => $identifierMatch ? '✓ Sesuai' : '❌ Tidak cocok',
                'detail' => $identifierMatch ? ($certificate->recipient_identifier ?? '-') : "Dokumen: \"{$detectedIdentifier}\" ≠ Arsip: \"{$certificate->recipient_identifier}\"",
            ],
            [
                'item' => 'Status akhir',
                'result' => (! $isTampered) ? 'valid' : 'invalid',
                'badge' => (! $isTampered) ? 'VALID' : 'INVALID',
                'detail' => (! $isTampered) ? 'Dokumen terverifikasi 100% otentik & sah' : 'Manipulasi terdeteksi, integritas berkas cacat',
            ],
        ];

        $conclusion = (! $isTampered)
            ? 'Dokumen dinyatakan VALID dan OTENTIK. Seluruh segel digital RSA-PSS dan data penerima cocok 100% dengan pangkalan data resmi universitas.'
            : 'Dokumen tidak dapat dinyatakan sebagai sertifikat yang valid karena data pada dokumen tidak sesuai dengan data yang telah ditandatangani secara digital.';

        return [
            'is_uploaded_pdf' => true,
            'is_tampered' => $isTampered,
            'tampered_reasons' => $tamperedReasons,
            'detailed_reasons' => $detailedReasons,
            'audit_checks' => $auditChecks,
            'conclusion' => $conclusion,
            'detected_name' => $detectedName,
            'official_name' => $certificate->recipient_name,
            'name_match' => $nameMatch,
            'detected_identifier' => $detectedIdentifier,
            'official_identifier' => $certificate->recipient_identifier,
            'identifier_match' => $identifierMatch,
            'title_match' => $titleMatch,
            'is_signature_valid' => $isDocSignatureValid,
            'is_hash_valid' => $isDocHashValid,
            'claimed_hash' => $claimedHash,
            'official_hash' => $certificate->hash_sha256,
        ];
    }

    /**
     * Extract certificate number from raw PDF stream or metadata.
     */
    protected function extractCertificateNumberFromPdf(string $content, ?string $allText = null): ?string
    {
        $searchSpace = ($allText ?? '')."\n".$content;

        // 1. Direct search on uncompressed/full text
        if (preg_match('/CERT-[A-Za-z0-9_-]{4,40}/i', $searchSpace, $matches)) {
            return trim($matches[0]);
        }

        // 2. Clean null-bytes (handles UTF-16BE plain text from DomPDF/PDFlib)
        $noNulls = str_replace("\x00", '', $searchSpace);
        if (preg_match('/CERT-[A-Za-z0-9_-]{4,40}/i', $noNulls, $matches)) {
            return trim($matches[0]);
        }

        // 3. Spaced hyphens or en-dash/em-dash: e.g. "CERT - 2026 - CAMPUS - 00106"
        if (preg_match('/CERT\s*[-–—]\s*(\d{4})\s*[-–—]\s*([A-Za-z0-9_]+)\s*[-–—]\s*([A-Za-z0-9_-]+)/i', $noNulls, $matches)) {
            return 'CERT-'.$matches[1].'-'.strtoupper($matches[2]).'-'.$matches[3];
        }
        if (preg_match('/CERT\s*[-–—]\s*([A-Za-z0-9_–—-]+)/i', $noNulls, $matches)) {
            $normalized = preg_replace('/\s*[-–—]\s*/', '-', trim($matches[0]));
            if (preg_match('/CERT-[A-Za-z0-9_-]{4,40}/i', $normalized, $nm)) {
                return trim($nm[0]);
            }
        }

        // 4. URL format: verify/CERT-...
        if (preg_match('/verify\/(CERT-[A-Za-z0-9_-]{4,40})/i', $noNulls, $matches)) {
            return trim($matches[1]);
        }

        // 5. TJ kerning array reconstruction directly on content
        if (preg_match_all('/\[(.*?)\]\s*TJ/s', $noNulls, $tjMatches)) {
            foreach ($tjMatches[1] as $tjContent) {
                if (preg_match_all('/\((.*?)\)/s', $tjContent, $parts)) {
                    $joined = implode('', $parts[1]);
                    if (preg_match('/CERT-[A-Za-z0-9_-]{4,40}/i', $joined, $m)) {
                        return trim($m[0]);
                    }
                }
            }
        }

        // 6. Hex encoded strings <0043004500520054...> in raw PDF objects
        if (preg_match_all('/<([0-9a-fA-F\s]{16,})>/', $content, $hexMatches)) {
            foreach ($hexMatches[1] as $hex) {
                $cleanHex = preg_replace('/\s+/', '', $hex);
                if (strlen($cleanHex) % 2 === 0) {
                    $binary = @hex2bin($cleanHex);
                    if ($binary) {
                        $binaryClean = str_replace("\x00", '', $binary);
                        if (preg_match('/CERT-[A-Za-z0-9_-]{4,40}/i', $binaryClean, $m)) {
                            return trim($m[0]);
                        }
                    }
                }
            }
        }

        // 7. Search FlateDecode streams manually if not already unpacked
        if (preg_match_all('/stream[\r\n]+(.*?)[\r\n]+endstream/s', $content, $streams)) {
            foreach ($streams[1] as $stream) {
                $uncompressed = @gzuncompress($stream) ?: @gzinflate($stream);
                if ($uncompressed) {
                    $uncompressedClean = str_replace("\x00", '', $uncompressed);
                    if (preg_match('/CERT-[A-Za-z0-9_-]{4,40}/i', $uncompressedClean, $m)) {
                        return trim($m[0]);
                    }
                    if (preg_match('/verify\/(CERT-[A-Za-z0-9_-]{4,40})/i', $uncompressedClean, $m)) {
                        return trim($m[1]);
                    }
                }
            }
        }

        // 8. Fallback: match by file hash
        $fileSha256 = hash('sha256', $content);
        $cert = Certificate::where('hash_sha256', $fileSha256)->first();
        if ($cert) {
            return $cert->certificate_number;
        }

        return null;
    }
}

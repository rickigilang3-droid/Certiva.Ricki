<?php

namespace App\Services;

use App\Models\Certificate;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class CertificateBulkImportService
{
    public function __construct(
        protected CryptoService $cryptoService,
        protected CertificatePdfService $pdfService,
        protected QrCodeService $qrService
    ) {}

    /**
     * Import certificates from an uploaded CSV file.
     *
     * @return array{imported: int, errors: array<int, string>, certificates: array<int, Certificate>}
     */
    public function importFromCsv(UploadedFile $file): array
    {
        $handle = fopen($file->getRealPath(), 'r');
        if (! $handle) {
            return [
                'imported' => 0,
                'errors' => ['Gagal membuka berkas CSV yang diunggah.'],
                'certificates' => [],
            ];
        }

        // Read BOM if present
        $bom = fread($handle, 3);
        if ($bom !== "\xEF\xBB\xBF") {
            rewind($handle);
        }

        // Read header
        $rawHeader = fgetcsv($handle);
        if (! $rawHeader) {
            fclose($handle);

            return [
                'imported' => 0,
                'errors' => ['Berkas CSV kosong atau tidak memiliki tajuk (header).'],
                'certificates' => [],
            ];
        }

        // Detect delimiter if line was read as single column
        if (count($rawHeader) === 1 && str_contains($rawHeader[0], ';')) {
            rewind($handle);
            $rawHeader = fgetcsv($handle, 0, ';');
        }

        $header = array_map(fn ($col) => strtolower(trim((string) $col)), $rawHeader);

        $activeKey = $this->cryptoService->getActiveKey();
        $importedCertificates = [];
        $errors = [];
        $rowNumber = 1;

        $lastSeq = Certificate::count() + 100;

        while (($row = fgetcsv($handle)) !== false) {
            $rowNumber++;

            // Handle empty rows
            if (empty(array_filter($row))) {
                continue;
            }

            // Adjust row for delimiter if needed
            if (count($row) === 1 && str_contains($row[0], ';')) {
                $row = str_getcsv($row[0], ';');
            }

            if (count($row) < count($header)) {
                $row = array_pad($row, count($header), '');
            }

            $data = array_combine($header, array_slice($row, 0, count($header)));

            // Clean data
            $recipientName = trim($data['recipient_name'] ?? $data['nama'] ?? '');
            $recipientIdentifier = trim($data['recipient_identifier'] ?? $data['nim'] ?? '');
            $recipientEmail = trim($data['recipient_email'] ?? $data['email'] ?? '');
            $title = trim($data['title'] ?? $data['program_studi'] ?? $data['judul'] ?? '');
            $category = trim($data['category'] ?? $data['kategori'] ?? 'Ijazah Kelulusan');
            $department = trim($data['department'] ?? $data['fakultas'] ?? 'Fakultas Teknologi Informasi');
            $description = trim($data['description'] ?? $data['deskripsi'] ?? 'Lulus dengan predikat memuaskan.');
            $institutionName = trim($data['institution_name'] ?? $data['institusi'] ?? 'Universitas Bina Sarana Informatika');
            $signatoryName = trim($data['signatory_name'] ?? $data['penandatangan'] ?? 'Prof. Dr. Ir. H. Budi Rahardjo, M.Sc.');
            $signatoryTitle = trim($data['signatory_title'] ?? $data['jabatan'] ?? 'Rektor Universitas Bina Sarana Informatika');
            $issuedDate = trim($data['issued_date'] ?? $data['tanggal_terbit'] ?? date('Y-m-d'));
            $expiryDate = trim($data['expiry_date'] ?? $data['tanggal_kedaluwarsa'] ?? '');

            // Validation
            if (empty($recipientName)) {
                $errors[] = "Baris {$rowNumber}: 'recipient_name' (Nama Penerima) tidak boleh kosong.";

                continue;
            }

            if (empty($title)) {
                $errors[] = "Baris {$rowNumber}: 'title' (Program Studi / Gelar) tidak boleh kosong.";

                continue;
            }

            // Certificate number
            $certNumber = trim($data['certificate_number'] ?? $data['nomor_sertifikat'] ?? '');
            if (empty($certNumber)) {
                $lastSeq++;
                $certNumber = 'CERT-'.date('Y').'-CAMPUS-'.str_pad((string) $lastSeq, 5, '0', STR_PAD_LEFT);
            }

            if (Certificate::where('certificate_number', $certNumber)->exists()) {
                $errors[] = "Baris {$rowNumber}: Nomor sertifikat '{$certNumber}' sudah terdaftar dalam sistem.";

                continue;
            }

            // Parse dates
            try {
                $formattedIssuedDate = Carbon::parse($issuedDate)->format('Y-m-d');
            } catch (\Exception) {
                $formattedIssuedDate = date('Y-m-d');
            }

            $formattedExpiryDate = null;
            if (! empty($expiryDate)) {
                try {
                    $formattedExpiryDate = Carbon::parse($expiryDate)->format('Y-m-d');
                } catch (\Exception) {
                    $formattedExpiryDate = null;
                }
            }

            try {
                // Deterministic canonical payload
                $canonicalPayload = $this->cryptoService->buildCanonicalPayload([
                    'certificate_number' => $certNumber,
                    'recipient_name' => $recipientName,
                    'recipient_identifier' => $recipientIdentifier,
                    'title' => $title,
                    'institution_name' => $institutionName,
                    'department' => $department,
                    'issued_date' => $formattedIssuedDate,
                    'expiry_date' => $formattedExpiryDate,
                    'signatory_name' => $signatoryName,
                    'signatory_title' => $signatoryTitle,
                ]);

                // Cryptographic sign with RSA-PSS SHA-256
                $signData = $this->cryptoService->signWithActiveKey($canonicalPayload, $activeKey);

                // Save QR Code
                $qrPath = $this->qrService->saveQrCode($certNumber);

                // Create DB Record
                $certificate = Certificate::create([
                    'certificate_number' => $certNumber,
                    'recipient_name' => $recipientName,
                    'recipient_identifier' => $recipientIdentifier ?: null,
                    'recipient_email' => $recipientEmail ?: null,
                    'title' => $title,
                    'category' => $category,
                    'department' => $department,
                    'description' => $description,
                    'institution_name' => $institutionName,
                    'signatory_name' => $signatoryName,
                    'signatory_title' => $signatoryTitle,
                    'issued_date' => $formattedIssuedDate,
                    'expiry_date' => $formattedExpiryDate,
                    'crypto_key_id' => $activeKey->id,
                    'canonical_payload' => $canonicalPayload,
                    'hash_sha256' => $signData['hash_sha256'],
                    'signature_rsapss' => $signData['signature_rsapss'],
                    'status' => 'active',
                    'qr_path' => $qrPath,
                ]);

                // Generate PDF
                $this->pdfService->generateAndSavePdf($certificate);

                $importedCertificates[] = $certificate;
            } catch (\Exception $e) {
                $errors[] = "Baris {$rowNumber}: Terjadi kesalahan saat memproses ({$e->getMessage()}).";
            }
        }

        fclose($handle);

        return [
            'imported' => count($importedCertificates),
            'errors' => $errors,
            'certificates' => $importedCertificates,
        ];
    }
}

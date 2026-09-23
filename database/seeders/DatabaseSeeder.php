<?php

namespace Database\Seeders;

use App\Models\Certificate;
use App\Models\User;
use App\Services\CertificatePdfService;
use App\Services\CryptoService;
use App\Services\QrCodeService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Default Admin User
        $admin = User::firstOrCreate(
            ['email' => 'admin@certiva.local'],
            [
                'name' => 'Administrator Kampus',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        // 2. Initialize Active Cryptographic Key
        $cryptoService = app(CryptoService::class);
        $qrService = app(QrCodeService::class);
        $pdfService = app(CertificatePdfService::class);

        $activeKey = $cryptoService->getActiveKey();

        // 3. Sample Certificates Data
        $sampleCerts = [
            [
                'certificate_number' => 'CERT-2026-CAMPUS-001',
                'recipient_name' => 'Ahmad Fauzi, S.Kom.',
                'recipient_identifier' => '20220801045',
                'recipient_email' => 'ahmad.fauzi@student.certiva.ac.id',
                'title' => 'Sarjana Komputer (S.Kom) - Teknik Informatika',
                'category' => 'Ijazah & Sertifikat Kelulusan',
                'department' => 'Fakultas Ilmu Komputer',
                'description' => 'Lulus dengan Predikat Dengan Pujian (Cum Laude) IPK 3.92',
                'institution_name' => 'Universitas Bina Sarana Informatika',
                'signatory_name' => 'Prof. Dr. Ir. H. Budi Rahardjo, M.Sc.',
                'signatory_title' => 'Rektor Universitas Bina Sarana Informatika',
                'issued_date' => '2026-09-22',
                'status' => 'active',
                'revocation_reason' => null,
                'revoked_at' => null,
            ],
            [
                'certificate_number' => 'CERT-2026-CAMPUS-002',
                'recipient_name' => 'Siti Nurhaliza, S.T.',
                'recipient_identifier' => '20220801089',
                'recipient_email' => 'siti.nurhaliza@student.certiva.ac.id',
                'title' => 'Sarjana Teknik (S.T.) - Rekayasa Sistem Siber',
                'category' => 'Ijazah & Sertifikat Kelulusan',
                'department' => 'Fakultas Teknik & Ilmu Komputer',
                'description' => 'Lulus dengan Predikat Sangat Memuaskan IPK 3.84',
                'institution_name' => 'Universitas Bina Sarana Informatika',
                'signatory_name' => 'Prof. Dr. Ir. H. Budi Rahardjo, M.Sc.',
                'signatory_title' => 'Rektor Universitas Bina Sarana Informatika',
                'issued_date' => '2026-09-22',
                'status' => 'active',
                'revocation_reason' => null,
                'revoked_at' => null,
            ],
            [
                'certificate_number' => 'CERT-2026-CAMPUS-003',
                'recipient_name' => 'Dr. Budi Santoso',
                'recipient_identifier' => 'NIDN-0412098201',
                'recipient_email' => 'budi.santoso@certiva.ac.id',
                'title' => 'Sertifikasi Ahli Kriptografi Terapan & Keamanan Data Nasional',
                'category' => 'Sertifikat Kompetensi Profesi',
                'department' => 'Lembaga Sertifikasi Profesi Teknologi Informasi (LSP-TIK)',
                'description' => 'Kompetensi Standar Kerja Nasional Indonesia (SKKNI) Level 8 Bidang Keamanan Siber',
                'institution_name' => 'Universitas Bina Sarana Informatika',
                'signatory_name' => 'Prof. Dr. Ir. H. Budi Rahardjo, M.Sc.',
                'signatory_title' => 'Ketua Dewan Penguji & Rektor',
                'issued_date' => '2026-09-22',
                'status' => 'active',
                'revocation_reason' => null,
                'revoked_at' => null,
            ],
            [
                'certificate_number' => 'CERT-2026-CAMPUS-004',
                'recipient_name' => 'Dimas Anggara',
                'recipient_identifier' => '20210801012',
                'recipient_email' => 'dimas.anggara@student.certiva.ac.id',
                'title' => 'Sarjana Manajemen (S.M.)',
                'category' => 'Ijazah & Sertifikat Kelulusan',
                'department' => 'Fakultas Ekonomi & Bisnis',
                'description' => 'Penyelesaian Tugas Akhir Skripsi',
                'institution_name' => 'Universitas Bina Sarana Informatika',
                'signatory_name' => 'Prof. Dr. Ir. H. Budi Rahardjo, M.Sc.',
                'signatory_title' => 'Rektor Universitas Bina Sarana Informatika',
                'issued_date' => '2026-09-22',
                'status' => 'revoked',
                'revocation_reason' => 'Pencabutan kelulusan akibat temuan pelanggaran integritas akademik (plagiarisme karya ilmiah) berdasarkan Surat Keputusan Senat Akademik No. 412/SK/SA/2026.',
                'revoked_at' => Carbon::create(2026, 9, 22, 14, 30, 0),
            ],
        ];

        foreach ($sampleCerts as $item) {
            $payload = $cryptoService->buildCanonicalPayload([
                'certificate_number' => $item['certificate_number'],
                'recipient_name' => $item['recipient_name'],
                'recipient_identifier' => $item['recipient_identifier'],
                'title' => $item['title'],
                'institution_name' => $item['institution_name'],
                'department' => $item['department'],
                'issued_date' => $item['issued_date'],
                'expiry_date' => null,
                'signatory_name' => $item['signatory_name'],
                'signatory_title' => $item['signatory_title'],
            ]);

            $sigData = $cryptoService->signWithActiveKey($payload, $activeKey);
            $qrPath = $qrService->saveQrCode($item['certificate_number']);

            $cert = Certificate::updateOrCreate(
                ['certificate_number' => $item['certificate_number']],
                [
                    'recipient_name' => $item['recipient_name'],
                    'recipient_identifier' => $item['recipient_identifier'],
                    'recipient_email' => $item['recipient_email'],
                    'title' => $item['title'],
                    'category' => $item['category'],
                    'department' => $item['department'],
                    'description' => $item['description'],
                    'institution_name' => $item['institution_name'],
                    'signatory_name' => $item['signatory_name'],
                    'signatory_title' => $item['signatory_title'],
                    'issued_date' => $item['issued_date'],
                    'crypto_key_id' => $activeKey->id,
                    'canonical_payload' => $payload,
                    'hash_sha256' => $sigData['hash_sha256'],
                    'signature_rsapss' => $sigData['signature_rsapss'],
                    'status' => $item['status'],
                    'revocation_reason' => $item['revocation_reason'],
                    'revoked_at' => $item['revoked_at'],
                    'qr_path' => $qrPath,
                ]
            );

            $pdfService->generateAndSavePdf($cert);
        }
    }
}

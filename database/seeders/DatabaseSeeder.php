<?php

namespace Database\Seeders;

use App\Models\Certificate;
use App\Models\User;
use App\Models\VerificationLog;
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
        // 1. Create Default Admin & Campus Users
        $admin = User::firstOrCreate(
            ['email' => 'admin@certiva.local'],
            [
                'name' => 'Administrator Kampus',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        $ricki = User::updateOrCreate(
            ['email' => 'ricki@bsi.ac.id'],
            [
                'name' => 'Dr. Ricki Gilang Saputra S.Kom. M.Kom.',
                'password' => Hash::make('rigskind'),
                'role' => 'admin',
                'identifier' => 'NIDN-0419088801',
                'email_verified_at' => now(),
            ]
        );

        $amel = User::updateOrCreate(
            ['email' => 'amel@bsi.ac.id'],
            [
                'name' => 'Amelia Dwi Oktaviani',
                'password' => Hash::make('rigskind'),
                'role' => 'mahasiswa',
                'identifier' => '1722511839',
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
                'template' => 'seminar',
                'status' => 'revoked',
                'revocation_reason' => 'Pencabutan kelulusan akibat temuan pelanggaran integritas akademik (plagiarisme karya ilmiah) berdasarkan Surat Keputusan Senat Akademik No. 412/SK/SA/2026.',
                'revoked_at' => Carbon::create(2026, 9, 22, 14, 30, 0),
            ],
            [
                'certificate_number' => 'CERT-2026-CAMPUS-00106',
                'recipient_name' => 'Amelia Dwi Oktaviani',
                'recipient_identifier' => '1722511839',
                'recipient_email' => 'amel@bsi.ac.id',
                'title' => 'Sarjana Komputer (S.Kom) - Teknologi Informasi',
                'category' => 'Ijazah & Sertifikat Kelulusan',
                'department' => 'Fakultas Teknik & Informatika',
                'description' => 'Lulus dengan Predikat Dengan Pujian (Cum Laude) - Sertifikasi Digital Berstandar Kriptografi RSA-2048',
                'institution_name' => 'Universitas Bina Sarana Informatika',
                'signatory_name' => 'Dr. Ricki Gilang Saputra S.Kom. M.Kom.',
                'signatory_title' => 'Rektor & Penanggung Jawab Akademik',
                'issued_date' => '2026-09-23',
                'template' => 'modern',
                'status' => 'active',
                'revocation_reason' => null,
                'revoked_at' => null,
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
                    'template' => $item['template'] ?? 'formal',
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

        // Sample Verification Audit Logs
        $amelCert = Certificate::where('certificate_number', 'CERT-2026-CAMPUS-00106')->first();
        if ($amelCert) {
            VerificationLog::firstOrCreate(
                ['certificate_number_queried' => 'CERT-2026-CAMPUS-00106'],
                [
                    'certificate_id' => $amelCert->id,
                    'ip_address' => '103.28.12.89',
                    'user_agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_4 like Mac OS X) AppleWebKit/605.1.15 Mobile/15E148 Safari/604.1',
                    'status' => 'valid',
                    'verified_at' => now()->subMinutes(15),
                ]
            );
        }

        $fauziCert = Certificate::where('certificate_number', 'CERT-2026-CAMPUS-001')->first();
        if ($fauziCert) {
            VerificationLog::firstOrCreate(
                ['certificate_number_queried' => 'CERT-2026-CAMPUS-001'],
                [
                    'certificate_id' => $fauziCert->id,
                    'ip_address' => '180.252.164.20',
                    'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/128.0.0.0 Safari/537.36',
                    'status' => 'valid',
                    'verified_at' => now()->subHours(2),
                ]
            );
        }

        $dimasCert = Certificate::where('certificate_number', 'CERT-2026-CAMPUS-004')->first();
        if ($dimasCert) {
            VerificationLog::firstOrCreate(
                ['certificate_number_queried' => 'CERT-2026-CAMPUS-004'],
                [
                    'certificate_id' => $dimasCert->id,
                    'ip_address' => '36.85.15.112',
                    'user_agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 Chrome/128.0.0.0 Safari/537.36',
                    'status' => 'revoked',
                    'verified_at' => now()->subHours(5),
                ]
            );
        }
    }
}

<?php

namespace Tests\Feature;

use App\Models\Certificate;
use App\Models\CryptoKey;
use App\Models\User;
use App\Models\VerificationLog;
use App\Services\CertificatePdfService;
use App\Services\CryptoService;
use App\Services\QrCodeService;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StudentPortalAndAdvancedFeaturesTest extends TestCase
{
    use RefreshDatabase;

    protected CryptoKey $activeKey;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
        $this->activeKey = CryptoKey::where('status', 'active')->first();
    }

    public function test_student_can_register_with_nim_and_redirects_to_student_portal(): void
    {
        $response = $this->post('/register', [
            'name' => 'Siti Nurhaliza',
            'email' => 'siti@student.bsi.ac.id',
            'role' => 'mahasiswa',
            'identifier' => '12220199',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $this->assertAuthenticated();
        $user = User::where('email', 'siti@student.bsi.ac.id')->first();
        $this->assertNotNull($user);
        $this->assertEquals('mahasiswa', $user->role);
        $this->assertEquals('12220199', $user->identifier);
        $response->assertRedirect(route('student.certificates'));
    }

    public function test_student_can_view_own_certificates_in_portal(): void
    {
        $student = User::factory()->create([
            'role' => 'mahasiswa',
            'email' => 'student1@bsi.ac.id',
            'identifier' => '12220001',
        ]);

        $otherStudent = User::factory()->create([
            'role' => 'mahasiswa',
            'email' => 'student2@bsi.ac.id',
            'identifier' => '12220002',
        ]);

        $cert1 = $this->createTestCertificate('CERT-2026-TEST-001', $student->name, $student->email, $student->identifier);
        $cert2 = $this->createTestCertificate('CERT-2026-TEST-002', $otherStudent->name, $otherStudent->email, $otherStudent->identifier);

        $response = $this->actingAs($student)->get('/my-certificates');

        $response->assertStatus(200);
        $response->assertSee('CERT-2026-TEST-001');
        $response->assertDontSee('CERT-2026-TEST-002');
    }

    public function test_student_can_download_and_preview_own_certificate_pdf(): void
    {
        $student = User::factory()->create([
            'role' => 'mahasiswa',
            'email' => 'student@bsi.ac.id',
            'identifier' => '12220010',
        ]);

        $cert = $this->createTestCertificate('CERT-2026-TEST-OWN', $student->name, $student->email, $student->identifier);

        $downloadResponse = $this->actingAs($student)->get("/my-certificates/{$cert->id}/pdf");
        $downloadResponse->assertStatus(200);

        $previewResponse = $this->actingAs($student)->get("/my-certificates/{$cert->id}/preview");
        $previewResponse->assertStatus(200);
    }

    public function test_student_cannot_download_other_students_certificate(): void
    {
        $student = User::factory()->create([
            'role' => 'mahasiswa',
            'email' => 'studentA@bsi.ac.id',
            'identifier' => '12220011',
        ]);

        $certOther = $this->createTestCertificate('CERT-2026-TEST-OTHER', 'Other User', 'other@bsi.ac.id', '12220099');

        $response = $this->actingAs($student)->get("/my-certificates/{$certOther->id}/pdf");
        $response->assertStatus(403);
    }

    public function test_student_is_restricted_from_accessing_admin_routes(): void
    {
        $student = User::factory()->create([
            'role' => 'mahasiswa',
            'email' => 'student.restricted@bsi.ac.id',
            'identifier' => '12220055',
        ]);

        $response = $this->actingAs($student)->get('/certificates/create');
        $response->assertRedirect(route('student.certificates'));

        $cryptoResponse = $this->actingAs($student)->get('/crypto-keys');
        $cryptoResponse->assertRedirect(route('student.certificates'));
    }

    public function test_admin_can_bulk_import_certificates_from_csv(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'email' => 'admin.test@bsi.ac.id',
        ]);

        $csvContent = "\xEF\xBB\xBF"
            ."recipient_name,recipient_identifier,recipient_email,title,category,department,description,institution_name,signatory_name,signatory_title,issued_date,expiry_date\n"
            ."Budi Santoso,12229901,budi@student.bsi.ac.id,Sarjana Komputer,Ijazah Kelulusan,Fakultas Teknik,Lulus Cum Laude,UBSI,Rektor UBSI,Rektor,2026-09-23,\n"
            ."Dewi Lestari,12229902,dewi@student.bsi.ac.id,Sarjana Manajemen,Ijazah Kelulusan,Fakultas Ekonomi,Lulus Memuaskan,UBSI,Rektor UBSI,Rektor,2026-09-23,\n";

        $file = UploadedFile::fake()->createWithContent('students.csv', $csvContent);

        $response = $this->actingAs($admin)->post('/certificates/import', [
            'csv_file' => $file,
        ]);

        $response->assertRedirect(route('certificates.index'));
        $this->assertDatabaseHas('certificates', [
            'recipient_name' => 'Budi Santoso',
            'recipient_identifier' => '12229901',
        ]);
        $this->assertDatabaseHas('certificates', [
            'recipient_name' => 'Dewi Lestari',
            'recipient_identifier' => '12229902',
        ]);
    }

    public function test_admin_can_download_csv_template(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get('/certificates/import/template');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
    }

    public function test_public_user_can_verify_certificate_via_pdf_upload(): void
    {
        $cert = $this->createTestCertificate('CERT-2026-PDF-VERIFY', 'Test Recipient', 'verify@test.local', '12227777');

        // Generate actual PDF
        $pdfService = app(CertificatePdfService::class);
        $pdfPath = $pdfService->generateAndSavePdf($cert);
        $pdfRawContent = Storage::disk('public')->get($pdfPath);

        $fakePdf = UploadedFile::fake()->createWithContent("{$cert->certificate_number}.pdf", $pdfRawContent);

        $response = $this->post('/verify/pdf-upload', [
            'pdf_file' => $fakePdf,
        ]);

        $response->assertRedirect(route('verify.show', ['certificate_number' => $cert->certificate_number]));
    }

    public function test_revoked_certificate_shows_revoked_status_and_watermark(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $cert = $this->createTestCertificate('CERT-2026-REVOKE-TEST', 'Revoked Recipient', 'rev@test.local', '12228888');

        $this->actingAs($admin)->post("/certificates/{$cert->id}/revoke", [
            'revocation_reason' => 'Pelanggaran kode etik akademik.',
        ]);

        $cert->refresh();
        $this->assertEquals('revoked', $cert->status);

        // Verification page confirms revoked status
        $verifyResponse = $this->get("/verify/{$cert->certificate_number}");
        $verifyResponse->assertStatus(200);
        $verifyResponse->assertSee('DICABUT');

        // Streaming PDF includes watermark
        $pdfResponse = $this->actingAs($admin)->get("/certificates/{$cert->id}/preview");
        $pdfResponse->assertStatus(200);
    }

    public function test_admin_certificate_create_page_shows_registered_students_for_quick_selection(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $student = User::factory()->create([
            'role' => 'mahasiswa',
            'name' => 'Bintang Pratama',
            'email' => 'bintang@student.bsi.ac.id',
            'identifier' => '12229988',
        ]);

        $response = $this->actingAs($admin)->get('/certificates/create');

        $response->assertStatus(200);
        $response->assertSee('Pilih Mahasiswa Terdaftar (Auto-Fill / Sat Set)');
        $response->assertSee('Bintang Pratama');
        $response->assertSee('12229988');
    }

    public function test_public_portfolio_displays_student_certificates_and_linkedin_button(): void
    {
        $student = User::factory()->create([
            'role' => 'mahasiswa',
            'name' => 'Amelia Test',
            'email' => 'amelia.test@student.bsi.ac.id',
            'identifier' => '17229999',
        ]);

        $this->createTestCertificate('CERT-2026-TEST-9999', 'Amelia Test', $student->email, $student->identifier);

        $response = $this->get('/p/17229999');

        $response->assertStatus(200);
        $response->assertSee('Amelia Test');
        $response->assertSee('CERT-2026-TEST-9999');
        $response->assertSee('Tambah ke LinkedIn');
    }

    public function test_admin_can_view_verification_audit_logs(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        VerificationLog::create([
            'certificate_number_queried' => 'CERT-2026-LOG-001',
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Mozilla/5.0 PHPUnit',
            'status' => 'valid',
            'verified_at' => now(),
        ]);

        $response = $this->actingAs($admin)->get('/verification-logs');

        $response->assertStatus(200);
        $response->assertSee('Audit & Log Pemindaian Verifikasi');
        $response->assertSee('CERT-2026-LOG-001');
    }

    protected function createTestCertificate(string $certNumber, string $name, string $email, string $identifier): Certificate
    {
        $cryptoService = app(CryptoService::class);
        $pdfService = app(CertificatePdfService::class);
        $qrService = app(QrCodeService::class);

        $canonicalPayload = $cryptoService->buildCanonicalPayload([
            'certificate_number' => $certNumber,
            'recipient_name' => $name,
            'recipient_identifier' => $identifier,
            'title' => 'Sarjana Komputer',
            'institution_name' => 'Universitas Bina Sarana Informatika',
            'department' => 'Fakultas Teknik & Informatika',
            'issued_date' => '2026-09-23',
            'expiry_date' => null,
            'signatory_name' => 'Prof. Dr. Ir. H. Budi Rahardjo, M.Sc.',
            'signatory_title' => 'Rektor',
        ]);

        $signData = $cryptoService->signWithActiveKey($canonicalPayload, $this->activeKey);
        $qrPath = $qrService->saveQrCode($certNumber);

        $cert = Certificate::create([
            'certificate_number' => $certNumber,
            'recipient_name' => $name,
            'recipient_identifier' => $identifier,
            'recipient_email' => $email,
            'title' => 'Sarjana Komputer',
            'category' => 'Ijazah Kelulusan',
            'department' => 'Fakultas Teknik & Informatika',
            'description' => 'Lulus dengan predikat sangat memuaskan.',
            'institution_name' => 'Universitas Bina Sarana Informatika',
            'signatory_name' => 'Prof. Dr. Ir. H. Budi Rahardjo, M.Sc.',
            'signatory_title' => 'Rektor',
            'issued_date' => '2026-09-23',
            'expiry_date' => null,
            'crypto_key_id' => $this->activeKey->id,
            'canonical_payload' => $canonicalPayload,
            'hash_sha256' => $signData['hash_sha256'],
            'signature_rsapss' => $signData['signature_rsapss'],
            'status' => 'active',
            'qr_path' => $qrPath,
        ]);

        $pdfService->generateAndSavePdf($cert);

        return $cert;
    }
}

<?php

namespace Tests\Feature;

use App\Models\Certificate;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CertificateManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_authenticated_user_can_access_dashboard_and_see_crypto_status(): void
    {
        $user = User::first();

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Dashboard Otoritas Kampus');
        $response->assertSee('RSA-2048');
        $response->assertSee('RSA-PSS');
        $response->assertSee('SHA-256');
        $response->assertSee('Active');
    }

    public function test_authenticated_user_can_issue_new_cryptographic_certificate(): void
    {
        $this->withoutExceptionHandling();
        $user = User::first();

        $certNumber = 'CERT-2026-TEST-99999';

        $response = $this->actingAs($user)->post('/certificates', [
            'certificate_number' => $certNumber,
            'recipient_name' => 'Kurniawan Dwi, S.Kom.',
            'recipient_identifier' => '20220801999',
            'recipient_email' => 'kurniawan@student.certiva.ac.id',
            'title' => 'Sarjana Komputer (S.Kom)',
            'category' => 'Ijazah & Sertifikat Kelulusan',
            'department' => 'Teknik Informatika',
            'description' => 'Lulus Cum Laude',
            'institution_name' => 'Universitas Bina Sarana Informatika',
            'signatory_name' => 'Prof. Dr. Ir. H. Budi Rahardjo, M.Sc.',
            'signatory_title' => 'Rektor',
            'issued_date' => '2026-09-22',
        ]);

        $cert = Certificate::where('certificate_number', $certNumber)->first();
        $this->assertNotNull($cert);
        $this->assertNotEmpty($cert->signature_rsapss);
        $this->assertNotEmpty($cert->hash_sha256);
        $this->assertEquals('active', $cert->status);

        $response->assertRedirect(route('certificates.show', $cert));
    }

    public function test_authenticated_user_can_revoke_certificate(): void
    {
        $user = User::first();
        $cert = Certificate::where('status', 'active')->first();

        $response = $this->actingAs($user)->post("/certificates/{$cert->id}/revoke", [
            'revocation_reason' => 'Pelanggaran etika akademik mahasiswa',
        ]);

        $cert->refresh();
        $this->assertEquals('revoked', $cert->status);
        $this->assertEquals('Pelanggaran etika akademik mahasiswa', $cert->revocation_reason);
        $this->assertNotNull($cert->revoked_at);
    }

    public function test_authenticated_user_can_download_certificate_pdf(): void
    {
        $user = User::first();
        $cert = Certificate::where('status', 'active')->first();

        $response = $this->actingAs($user)->get("/certificates/{$cert->id}/pdf");

        $response->assertStatus(200);
        $response->assertHeader('content-disposition');
    }

    public function test_authenticated_user_can_preview_certificate_pdf(): void
    {
        $user = User::first();
        $cert = Certificate::where('status', 'active')->first();

        $response = $this->actingAs($user)->get("/certificates/{$cert->id}/preview");

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }
}

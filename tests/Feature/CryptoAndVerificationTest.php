<?php

namespace Tests\Feature;

use App\Models\Certificate;
use App\Services\CryptoService;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CryptoAndVerificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_crypto_service_generates_valid_rsapss_signature_and_detects_tampering(): void
    {
        $cryptoService = app(CryptoService::class);
        $key = $cryptoService->getActiveKey();

        $this->assertNotNull($key);
        $this->assertEquals('RSA-2048', $key->algorithm);
        $this->assertEquals('SHA-256', $key->hash_algorithm);
        $this->assertEquals('RSA-PSS', $key->signature_scheme);
        $this->assertEquals('active', $key->status);

        $payload = $cryptoService->buildCanonicalPayload([
            'certificate_number' => 'TEST-001',
            'recipient_name' => 'Ahmad Fauzi, S.Kom.',
            'recipient_identifier' => '20220801045',
            'title' => 'Sarjana Komputer',
            'institution_name' => 'Universitas Bina Sarana Informatika',
            'department' => 'Informatika',
            'issued_date' => '2026-09-22',
            'signatory_name' => 'Prof. Budi',
            'signatory_title' => 'Rektor',
        ]);

        $signData = $cryptoService->signWithActiveKey($payload, $key);
        $this->assertNotEmpty($signData['signature_rsapss']);
        $this->assertNotEmpty($signData['hash_sha256']);

        // 1. Verify authentic payload passes
        $valid = $cryptoService->verifySignature($payload, $signData['signature_rsapss'], $key->public_key);
        $this->assertTrue($valid, 'Authentic payload must verify successfully.');

        // 2. Tampering recipient name must fail
        $tamperedName = $payload;
        $tamperedName['recipient_name'] = 'Budi Penipu';
        $this->assertFalse($cryptoService->verifySignature($tamperedName, $signData['signature_rsapss'], $key->public_key));

        // 3. Tampering date must fail
        $tamperedDate = $payload;
        $tamperedDate['issued_date'] = '2025-01-01';
        $this->assertFalse($cryptoService->verifySignature($tamperedDate, $signData['signature_rsapss'], $key->public_key));

        // 4. Tampering degree must fail
        $tamperedDegree = $payload;
        $tamperedDegree['title'] = 'Doktor Kehormatan';
        $this->assertFalse($cryptoService->verifySignature($tamperedDegree, $signData['signature_rsapss'], $key->public_key));
    }

    public function test_public_verification_page_shows_authentic_certificate_and_cryptographic_details(): void
    {
        $response = $this->get('/verify/CERT-2026-CAMPUS-001');

        $response->assertStatus(200);
        $response->assertSee('Ahmad Fauzi, S.Kom.');
        $response->assertSee('Terverifikasi Sah & Asli', false);
        $response->assertSee('RSA-2048');
        $response->assertSee('SHA-256');
        $response->assertSee('RSA-PSS');
        $response->assertSee('Active');
        $response->assertSee('22 September 2026');
    }

    public function test_public_verification_page_shows_revocation_notice_for_revoked_certificate(): void
    {
        $response = $this->get('/verify/CERT-2026-CAMPUS-004');

        $response->assertStatus(200);
        $response->assertSee('Dimas Anggara');
        $response->assertSee('Status: DICABUT (REVOKED)');
        $response->assertSee('plagiarisme karya ilmiah');
    }

    public function test_public_verification_page_handles_non_existent_certificate(): void
    {
        $response = $this->get('/verify/CERT-UNKNOWN-999');

        $response->assertStatus(200);
        $response->assertSee('Sertifikat Tidak Ditemukan');
    }

    public function test_raw_proof_endpoint_returns_json_cryptographic_evidence(): void
    {
        $response = $this->getJson('/verify/CERT-2026-CAMPUS-001/proof');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'certificate_number',
                'recipient',
                'award',
                'status',
                'cryptography' => [
                    'algorithm',
                    'hash_algorithm',
                    'signature_scheme',
                    'key_status',
                    'key_last_rotated',
                    'key_fingerprint',
                    'sha256_digest',
                    'signature_rsapss_base64',
                    'canonical_payload',
                    'public_key_pem',
                ],
            ]);
    }

    public function test_tampered_certificate_record_is_detected_on_public_verification_page(): void
    {
        $cert = Certificate::where('certificate_number', 'CERT-2026-CAMPUS-001')->first();
        $this->assertNotNull($cert);

        // Intentionally alter recipient name in DB directly (simulating unauthorized edit)
        $cert->update(['recipient_name' => 'Nama Palsu Hacker']);

        $response = $this->get('/verify/CERT-2026-CAMPUS-001');

        $response->assertStatus(200);
        $response->assertSee('Peringatan: Integritas Gagal');
        $response->assertSee('Sertifikat Terindikasi Dimanipulasi / Dipalsukan');
        $response->assertSee('DIFFERENT');
        $response->assertSee('TAMPERED');
    }
}

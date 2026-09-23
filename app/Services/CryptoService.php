<?php

namespace App\Services;

use App\Models\Certificate;
use App\Models\CryptoKey;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use phpseclib4\Crypt\RSA;

class CryptoService
{
    /**
     * Directory path where encrypted private keys are stored.
     */
    protected string $keyStoragePath;

    public function __construct()
    {
        $this->keyStoragePath = storage_path('app/crypto');
        if (! File::exists($this->keyStoragePath)) {
            File::makeDirectory($this->keyStoragePath, 0700, true);
        }
    }

    /**
     * Generate an RSA-2048 key pair, save the private key, and register the public key in DB.
     */
    public function generateKeyPair(
        string $name = 'Universitas Bina Sarana Informatika Primary RSA Signing Key',
        ?Carbon $lastRotatedAt = null,
        bool $makeActive = true
    ): CryptoKey {
        // Generate RSA-2048 key
        $privateKey = RSA::createKey(2048);
        $publicKey = $privateKey->getPublicKey();

        $privatePem = $privateKey->toString('PKCS8');
        $publicPem = $publicKey->toString('PKCS8');

        // Fingerprint: SHA-256 of public key PEM
        $fingerprint = hash('sha256', trim($publicPem));

        $keyId = 'KEY-'.strtoupper(Str::random(10));
        $privateKeyFile = 'crypto/'.$keyId.'.key';
        $fullPrivateKeyPath = storage_path('app/'.$privateKeyFile);

        // Store private key securely
        File::put($fullPrivateKeyPath, $privatePem);
        chmod($fullPrivateKeyPath, 0600);

        if ($makeActive) {
            // Set any currently active keys to rotated
            CryptoKey::where('status', 'active')->update(['status' => 'rotated']);
        }

        return CryptoKey::create([
            'key_id' => $keyId,
            'name' => $name,
            'algorithm' => 'RSA-2048',
            'hash_algorithm' => 'SHA-256',
            'signature_scheme' => 'RSA-PSS',
            'public_key' => $publicPem,
            'private_key_path' => $privateKeyFile,
            'fingerprint' => $fingerprint,
            'status' => $makeActive ? 'active' : 'rotated',
            'last_rotated_at' => $lastRotatedAt ?? Carbon::create(2026, 9, 22, 0, 0, 0),
        ]);
    }

    /**
     * Retrieve the current active RSA key.
     */
    public function getActiveKey(): CryptoKey
    {
        $activeKey = CryptoKey::where('status', 'active')->latest()->first();

        if (! $activeKey) {
            $activeKey = $this->generateKeyPair(
                'Universitas Bina Sarana Informatika Primary RSA Signing Key',
                Carbon::create(2026, 9, 22, 0, 0, 0),
                true
            );
        }

        return $activeKey;
    }

    /**
     * Build deterministic canonical JSON payload from certificate attributes.
     * Prevents any whitespace, order, or encoding tampering.
     */
    public function buildCanonicalPayload(array $attributes): array
    {
        // Whitelist critical fields that define certificate authenticity
        $canonical = [
            'certificate_number' => (string) ($attributes['certificate_number'] ?? ''),
            'recipient_name' => trim((string) ($attributes['recipient_name'] ?? '')),
            'recipient_identifier' => trim((string) ($attributes['recipient_identifier'] ?? '')),
            'title' => trim((string) ($attributes['title'] ?? '')),
            'institution_name' => trim((string) ($attributes['institution_name'] ?? '')),
            'department' => trim((string) ($attributes['department'] ?? '')),
            'issued_date' => (string) ($attributes['issued_date'] ?? ''),
            'expiry_date' => ! empty($attributes['expiry_date']) ? (string) $attributes['expiry_date'] : null,
            'signatory_name' => trim((string) ($attributes['signatory_name'] ?? '')),
            'signatory_title' => trim((string) ($attributes['signatory_title'] ?? '')),
        ];

        ksort($canonical);

        return $canonical;
    }

    /**
     * Build canonical payload directly from a Certificate model instance.
     */
    public function buildCanonicalPayloadFromCertificate(Certificate $certificate): array
    {
        return $this->buildCanonicalPayload([
            'certificate_number' => $certificate->certificate_number,
            'recipient_name' => $certificate->recipient_name,
            'recipient_identifier' => $certificate->recipient_identifier ?? '',
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
        ]);
    }

    /**
     * Calculate SHA-256 hash of canonical payload string.
     */
    public function calculateSha256(array|string $payload): string
    {
        if (is_array($payload)) {
            ksort($payload);
            $payloadString = json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        } else {
            $payloadString = $payload;
        }

        return hash('sha256', $payloadString);
    }

    /**
     * Sign canonical payload using RSA-PSS padding and SHA-256 hash.
     */
    public function signWithActiveKey(array $canonicalPayload, ?CryptoKey $key = null): array
    {
        $key = $key ?? $this->getActiveKey();
        ksort($canonicalPayload);
        $payloadString = json_encode($canonicalPayload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $hashSha256 = hash('sha256', $payloadString);

        $privateKeyPath = storage_path('app/'.$key->private_key_path);
        if (! File::exists($privateKeyPath)) {
            throw new \RuntimeException("Private key file not found: {$key->private_key_path}");
        }

        $privatePem = File::get($privateKeyPath);
        $privateKey = RSA::loadPrivateKey($privatePem);

        // Configure RSA-PSS with SHA-256 and MGF1 SHA-256
        $pssSigner = $privateKey
            ->withPadding(RSA::SIGNATURE_PSS)
            ->withHash('sha256')
            ->withMGFHash('sha256');

        $binarySignature = $pssSigner->sign($payloadString);
        $signatureBase64 = base64_encode($binarySignature);

        return [
            'hash_sha256' => $hashSha256,
            'signature_rsapss' => $signatureBase64,
            'crypto_key_id' => $key->id,
            'canonical_payload' => $canonicalPayload,
        ];
    }

    /**
     * Cryptographically verify an RSA-PSS signature against canonical payload and public key.
     */
    public function verifySignature(array $canonicalPayload, string $signatureBase64, string $publicKeyPem): bool
    {
        try {
            ksort($canonicalPayload);
            $payloadString = json_encode($canonicalPayload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            $publicKey = RSA::loadPublicKey($publicKeyPem);

            $pssVerifier = $publicKey
                ->withPadding(RSA::SIGNATURE_PSS)
                ->withHash('sha256')
                ->withMGFHash('sha256');

            $binarySignature = base64_decode($signatureBase64);

            return (bool) $pssVerifier->verify($payloadString, $binarySignature);
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Complete end-to-end verification of a certificate's cryptographic integrity.
     */
    public function verifyCertificateIntegrity(Certificate $certificate): array
    {
        // 1. Recalculate canonical payload from current certificate attributes
        $recalculatedPayload = $this->buildCanonicalPayloadFromCertificate($certificate);

        // 2. Recalculate SHA-256 hash
        $recomputedHash = $this->calculateSha256($recalculatedPayload);

        // 3. Compare with stored hash
        $isHashValid = hash_equals($certificate->hash_sha256, $recomputedHash);

        // 4. Verify RSA-PSS signature against public key
        $isSignatureValid = $this->verifySignature(
            $recalculatedPayload,
            $certificate->signature_rsapss,
            $certificate->cryptoKey->public_key
        );

        $isTampered = (! $isHashValid || ! $isSignatureValid);

        return [
            'isValid' => ($isHashValid && $isSignatureValid),
            'isHashMatch' => $isHashValid,
            'isSignatureValid' => $isSignatureValid,
            'isTampered' => $isTampered,
            'recomputedHash' => $recomputedHash,
            'storedHash' => $certificate->hash_sha256,
            'canonicalPayload' => $recalculatedPayload,
        ];
    }

    /**
     * Rotate current active key to rotated, and generate a fresh RSA-2048 keypair.
     */
    public function rotateKey(string $name = 'Universitas Bina Sarana Informatika Rotated Key'): CryptoKey
    {
        return $this->generateKeyPair($name, Carbon::now(), true);
    }
}

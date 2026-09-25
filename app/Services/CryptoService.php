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
        try {
            $storageDir = dirname($fullPrivateKeyPath);
            if (! File::exists($storageDir)) {
                File::makeDirectory($storageDir, 0755, true);
            }
            File::put($fullPrivateKeyPath, $privatePem);
            @chmod($fullPrivateKeyPath, 0600);
        } catch (\Throwable $e) {
            // Serverless /tmp might have different permissions
        }

        // Also store in database/crypto directory for deployment bundling
        try {
            $bundledDir = database_path('crypto');
            if (! File::exists($bundledDir)) {
                File::makeDirectory($bundledDir, 0755, true);
            }
            File::put(database_path('crypto/'.$keyId.'.key'), $privatePem);
        } catch (\Throwable $e) {
            // Ignore if filesystem is read-only
        }

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
            'private_key' => $privatePem,
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
     * Resolve private key PEM using multi-tier fallback:
     * 1. Direct model attribute (decrypted via Eloquent)
     * 2. Config / Environment variable CRYPTO_PRIVATE_KEY_BASE64
     * 3. Bundled database/crypto/{key_id}.key (accessible on Vercel)
     * 4. storage/app/{private_key_path}
     * 5. storage/app/crypto/{key_id}.key
     * 6. Self-healing: generate and associate a valid key pair if missing
     */
    public function getPrivateKeyPem(CryptoKey $key): string
    {
        // 1. Check encrypted model attribute
        if (! empty($key->private_key)) {
            return $key->private_key;
        }

        // 2. Check environment config
        $privateKeyBase64 = config('services.crypto.private_key_base64');
        if ($privateKeyBase64) {
            $decoded = base64_decode($privateKeyBase64, true);
            if ($decoded !== false && $decoded !== '') {
                return $decoded;
            }
        }

        // 3. Check bundled database/crypto directory
        $bundledPath = database_path('crypto/'.$key->key_id.'.key');
        if (File::exists($bundledPath)) {
            return File::get($bundledPath);
        }

        // 4. Check primary storage path
        $primaryStoragePath = storage_path('app/'.$key->private_key_path);
        if (File::exists($primaryStoragePath)) {
            return File::get($primaryStoragePath);
        }

        // 5. Check alternative storage path
        $altStoragePath = storage_path('app/crypto/'.$key->key_id.'.key');
        if (File::exists($altStoragePath)) {
            return File::get($altStoragePath);
        }

        // 6. Self-healing fallback: generate and persist a fresh key pair
        $newKey = RSA::createKey(2048);
        $privatePem = $newKey->toString('PKCS8');
        $publicPem = $newKey->getPublicKey()->toString('PKCS8');
        $fingerprint = hash('sha256', trim($publicPem));

        $key->update([
            'public_key' => $publicPem,
            'private_key' => $privatePem,
            'fingerprint' => $fingerprint,
        ]);

        try {
            @File::put(database_path('crypto/'.$key->key_id.'.key'), $privatePem);
            @File::put(storage_path('app/'.$key->private_key_path), $privatePem);
        } catch (\Throwable $e) {
            // Ignore filesystem write restrictions on serverless
        }

        return $privatePem;
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

        $privatePem = $this->getPrivateKeyPem($key);
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

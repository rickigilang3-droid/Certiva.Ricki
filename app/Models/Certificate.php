<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Certificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'certificate_number',
        'recipient_name',
        'recipient_identifier',
        'recipient_email',
        'title',
        'category',
        'description',
        'institution_name',
        'department',
        'signatory_name',
        'signatory_title',
        'issued_date',
        'expiry_date',
        'crypto_key_id',
        'canonical_payload',
        'hash_sha256',
        'signature_rsapss',
        'status',
        'revocation_reason',
        'revoked_at',
        'pdf_path',
        'qr_path',
    ];

    protected $casts = [
        'issued_date' => 'date',
        'expiry_date' => 'date',
        'revoked_at' => 'datetime',
        'canonical_payload' => 'array',
    ];

    public function cryptoKey(): BelongsTo
    {
        return $this->belongsTo(CryptoKey::class);
    }

    public function verificationLogs(): HasMany
    {
        return $this->hasMany(VerificationLog::class);
    }

    public function isRevoked(): bool
    {
        return $this->status === 'revoked';
    }
}

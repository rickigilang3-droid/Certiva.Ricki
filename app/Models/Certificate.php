<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $certificate_number
 * @property string $recipient_name
 * @property string|null $recipient_identifier
 * @property string|null $recipient_email
 * @property string $title
 * @property string|null $category
 * @property string|null $template
 * @property string|null $description
 * @property string $institution_name
 * @property string|null $department
 * @property string $signatory_name
 * @property string $signatory_title
 * @property Carbon $issued_date
 * @property Carbon|null $expiry_date
 * @property int|null $crypto_key_id
 * @property array|null $canonical_payload
 * @property string|null $hash_sha256
 * @property string|null $signature_rsapss
 * @property string $status
 * @property string|null $revocation_reason
 * @property Carbon|null $revoked_at
 * @property string|null $pdf_path
 * @property string|null $qr_path
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read CryptoKey|null $cryptoKey
 */
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
        'template',
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

<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $key_id
 * @property string $name
 * @property string $algorithm
 * @property string $hash_algorithm
 * @property string $signature_scheme
 * @property string $public_key
 * @property string $private_key_path
 * @property string $fingerprint
 * @property string $status
 * @property Carbon|null $last_rotated_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class CryptoKey extends Model
{
    use HasFactory;

    protected $fillable = [
        'key_id',
        'name',
        'algorithm',
        'hash_algorithm',
        'signature_scheme',
        'public_key',
        'private_key_path',
        'fingerprint',
        'status',
        'last_rotated_at',
    ];

    protected $casts = [
        'last_rotated_at' => 'datetime',
    ];

    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }
}

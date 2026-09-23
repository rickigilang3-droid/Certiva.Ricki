<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

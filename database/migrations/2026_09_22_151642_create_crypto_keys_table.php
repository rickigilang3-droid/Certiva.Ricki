<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('crypto_keys', function (Blueprint $table) {
            $table->id();
            $table->string('key_id')->unique();
            $table->string('name');
            $table->string('algorithm')->default('RSA-2048');
            $table->string('hash_algorithm')->default('SHA-256');
            $table->string('signature_scheme')->default('RSA-PSS');
            $table->text('public_key');
            $table->string('private_key_path');
            $table->string('fingerprint', 64);
            $table->enum('status', ['active', 'rotated', 'revoked'])->default('active');
            $table->timestamp('last_rotated_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crypto_keys');
    }
};

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
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->string('certificate_number')->unique()->index();
            $table->string('recipient_name');
            $table->string('recipient_identifier')->nullable()->comment('NIM / Student ID / NIK');
            $table->string('recipient_email')->nullable();
            $table->string('title');
            $table->string('category')->default('Ijazah / Kelulusan');
            $table->text('description')->nullable();
            $table->string('institution_name')->default('Universitas Bina Sarana Informatika');
            $table->string('department')->nullable();
            $table->string('signatory_name')->nullable();
            $table->string('signatory_title')->nullable();
            $table->date('issued_date');
            $table->date('expiry_date')->nullable();
            $table->foreignId('crypto_key_id')->constrained('crypto_keys')->cascadeOnDelete();
            $table->json('canonical_payload');
            $table->string('hash_sha256', 64);
            $table->text('signature_rsapss');
            $table->enum('status', ['active', 'revoked'])->default('active');
            $table->text('revocation_reason')->nullable();
            $table->timestamp('revoked_at')->nullable();
            $table->string('pdf_path')->nullable();
            $table->string('qr_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};

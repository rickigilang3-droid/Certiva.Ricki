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
        Schema::table('crypto_keys', function (Blueprint $table) {
            if (! Schema::hasColumn('crypto_keys', 'private_key')) {
                $table->text('private_key')->nullable()->after('public_key');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('crypto_keys', function (Blueprint $table) {
            if (Schema::hasColumn('crypto_keys', 'private_key')) {
                $table->dropColumn('private_key');
            }
        });
    }
};

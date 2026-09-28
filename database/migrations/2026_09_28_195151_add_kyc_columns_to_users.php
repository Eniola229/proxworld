<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('kyc_status', 20)->default('unverified')->index();
            $table->string('kyc_session_id', 64)->nullable();
            $table->timestamp('kyc_verified_at')->nullable();
            $table->timestamp('kyc_last_attempt_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['kyc_status']);
            $table->dropColumn(['kyc_status', 'kyc_session_id', 'kyc_verified_at', 'kyc_last_attempt_at']);
        });
    }
};

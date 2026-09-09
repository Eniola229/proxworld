<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('resellers', function (Blueprint $table) {
            $table->timestamp('custom_domain_verified_at')->nullable()->after('custom_domain_status');
            $table->string('custom_domain_error')->nullable()->after('custom_domain_verified_at');
        });
    }

    public function down(): void
    {
        Schema::table('resellers', function (Blueprint $table) {
            $table->dropColumn(['custom_domain_verified_at', 'custom_domain_error']);
        });
    }
};
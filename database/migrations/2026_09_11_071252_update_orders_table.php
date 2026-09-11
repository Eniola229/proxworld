<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->json('proxy_data')->nullable()->after('provider_synced_at');
            $table->timestamp('proxy_synced_at')->nullable()->after('proxy_data');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['proxy_data', 'proxy_synced_at']);
        });
    }
};
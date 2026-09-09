<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Adds a `config` JSON column used only by ConfigurableHttpProviderDriver.
     * Every other driver ignores it. Nullable so existing providers
     * (BearerTokenProviderDriver / HeaderTokenProviderDriver) are unaffected.
     */
    public function up(): void
    {
        Schema::table('providers', function (Blueprint $table) {
            $table->json('config')->nullable()->after('api_secret');
        });
    }

    public function down(): void
    {
        Schema::table('providers', function (Blueprint $table) {
            $table->dropColumn('config');
        });
    }
};
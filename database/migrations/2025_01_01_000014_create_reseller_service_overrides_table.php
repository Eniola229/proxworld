<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reseller_service_overrides', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('reseller_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('provider_id')->constrained()->cascadeOnDelete();
            $table->string('external_service_id');
            $table->decimal('markup_percent', 5, 2)->nullable(); // null = fall back to reseller's default_markup_percent
            $table->boolean('is_hidden')->default(false); // reseller can hide a plan from their own storefront
            $table->timestamps();

            $table->unique(['reseller_id', 'provider_id', 'external_service_id'], 'reseller_service_override_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reseller_service_overrides');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * A refreshed-periodically cache of each provider's own catalog (plans/
 * products), pulled live via Driver::getProducts(). We deliberately do NOT
 * hand-maintain a "products" table admin-side — the catalog is whatever the
 * provider currently sells; we just mark it up. Refreshed by the
 * `providers:sync-services` scheduled command.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('provider_services_cache', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('provider_id')->constrained()->cascadeOnDelete();
            $table->string('external_service_id'); // the provider's own product/plan id
            $table->string('name');
            $table->string('type')->nullable(); // App\Types\ProductType — best-effort guess from provider category
            $table->string('unit')->default('unit'); // "GB", "proxy", "IP", etc — for display only
            $table->decimal('raw_rate', 16, 6); // provider's own price, in raw_currency, per unit
            $table->string('raw_currency', 3)->default('USD');
            $table->json('raw_payload')->nullable(); // full provider response, for debugging/extension
            $table->boolean('is_active')->default(true);
            $table->timestamp('synced_at');
            $table->timestamps();

            $table->unique(['provider_id', 'external_service_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('provider_services_cache');
    }
};

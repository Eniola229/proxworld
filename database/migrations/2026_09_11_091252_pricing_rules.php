<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pricing_rules', function (Blueprint $table) {
            $table->uuid('id')->primary();
            // 'product_type' | 'protocol' | 'combined' (product_type+protocol) | 'provider' | 'provider_product_type'
            $table->string('scope_type');
            $table->string('product_type')->nullable();
            $table->string('protocol')->nullable();
            $table->foreignUuid('provider_id')->nullable()->constrained('providers')->nullOnDelete();
            $table->decimal('markup_percentage', 6, 2);
            $table->boolean('is_active')->default(true);
            $table->string('notes')->nullable();
            $table->timestamps();

            $table->unique(['scope_type', 'product_type', 'protocol', 'provider_id'], 'pricing_rules_scope_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pricing_rules');
    }
};
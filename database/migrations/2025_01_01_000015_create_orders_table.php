<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('reseller_id')->nullable()->constrained('resellers')->nullOnDelete();
            $table->foreignId('provider_id')->nullable()->constrained('providers')->nullOnDelete();
            $table->string('api_order_id')->nullable(); // the provider's own order/reference id
            $table->string('external_service_id')->nullable(); // ties back to provider_services_cache
            $table->string('service_name'); // snapshot — survives provider catalog changes/renames
            $table->string('product_type')->nullable(); // App\Types\ProductType, snapshot
            $table->unsignedInteger('quantity')->default(1);

            // --- Price snapshots, captured at purchase time. Changing prices
            // later never rewrites historical profit numbers.
            $table->decimal('cost_price_snapshot', 16, 6); // what we pay the provider, in base currency
            $table->decimal('platform_price_snapshot', 16, 6); // what we charge (direct sell price OR reseller wholesale price)
            $table->decimal('charge', 16, 4); // what the end customer actually paid, in `currency`
            $table->string('currency', 3)->default('NGN');
            $table->decimal('exchange_rate_snapshot', 20, 8)->default(1);

            $table->decimal('markup_percentage', 5, 2)->nullable();
            $table->decimal('profit', 16, 4)->nullable(); // platform's own profit on this order
            $table->decimal('reseller_profit', 16, 4)->nullable(); // reseller's markup profit, only when reseller_id set

            $table->string('channel')->default('direct'); // App\Types\OrderChannel
            $table->string('status')->default('pending'); // App\Types\OrderStatus
            $table->text('admin_note')->nullable();
            $table->text('failure_reason')->nullable();
            $table->unsignedTinyInteger('retry_count')->default(0);
            $table->timestamp('provider_synced_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index(['reseller_id']);
            $table->index(['status']);
            $table->index(['channel']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};

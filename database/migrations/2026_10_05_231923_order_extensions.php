<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('order_extensions', function (Blueprint $t) {
            $t->uuid('id')->primary();
            $t->foreignUuid('order_id')->constrained()->cascadeOnDelete();
            $t->foreignUuid('user_id')->constrained()->cascadeOnDelete();
            $t->foreignUuid('provider_id')->nullable()->constrained()->nullOnDelete();

            $t->unsignedInteger('quantity');

            $t->decimal('cost_price_snapshot', 18, 4);      // NGN
            $t->decimal('platform_price_snapshot', 18, 4);  // NGN
            $t->decimal('profit', 18, 4);                   // NGN
            $t->decimal('markup_percentage', 8, 4)->nullable();

            $t->decimal('charge', 18, 4);                   // customer's currency
            $t->string('currency', 3);
            $t->decimal('exchange_rate_snapshot', 18, 8)->nullable();

            $t->string('status', 20)->default('pending');   // pending | completed | failed
            $t->text('failure_reason')->nullable();
            $t->string('wallet_transaction_id')->nullable();

            $t->timestamps();
            $t->index(['order_id', 'status']);
            $t->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_extensions');
    }
};
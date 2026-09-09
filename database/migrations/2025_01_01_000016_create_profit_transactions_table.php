<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Platform-level profit ledger — every order's platform profit, append-only, for reporting. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('profit_transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('order_id');
            $table->foreignUuid('provider_id')->nullable()->constrained('providers')->nullOnDelete();
            $table->foreignUuid('reseller_id')->nullable()->constrained('resellers')->nullOnDelete();
            $table->string('channel'); // App\Types\OrderChannel — snapshot for fast filtering
            $table->decimal('amount', 16, 4); // platform profit for this order
            $table->string('currency', 3)->default('NGN');
            $table->timestamps();

            $table->index(['created_at']);
            $table->index(['provider_id']);
            $table->index(['channel']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profit_transactions');
    }
};

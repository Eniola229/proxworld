<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Ledger backing resellers.profit_balance (their withdrawable markup earnings). */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reseller_profit_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reseller_id')->constrained()->cascadeOnDelete();
            $table->uuid('order_id')->nullable();
            $table->string('type'); // App\Types\ProfitTransactionType
            $table->decimal('amount', 16, 4);
            $table->decimal('balance_before', 16, 4);
            $table->decimal('balance_after', 16, 4);
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index(['reseller_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reseller_profit_transactions');
    }
};

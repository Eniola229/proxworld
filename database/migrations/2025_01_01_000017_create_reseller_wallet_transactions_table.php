<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Ledger backing resellers.balance (their spendable storefront wallet). */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reseller_wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('reseller_id')->constrained()->cascadeOnDelete();
            $table->string('reference')->unique();
            $table->string('type'); // App\Types\WalletTransactionDirection
            $table->decimal('amount', 16, 4);
            $table->decimal('balance_before', 16, 4);
            $table->decimal('balance_after', 16, 4);
            $table->string('currency', 3)->default('NGN');
            $table->string('payment_method')->nullable();
            $table->string('status')->default('success');
            $table->text('description')->nullable();
            $table->uuid('order_id')->nullable();
            $table->timestamps();

            $table->index(['reseller_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reseller_wallet_transactions');
    }
};

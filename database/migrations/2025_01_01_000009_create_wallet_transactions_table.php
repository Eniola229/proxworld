<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The customer/reseller-owner personal wallet ledger (backs users.balance).
 * Append-only — this is the source of truth; users.balance is a cached
 * total kept in sync only by App\Services\WalletService.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained()->cascadeOnDelete();
            $table->string('reference')->unique();
            $table->string('type'); // App\Types\WalletTransactionDirection (credit/debit)
            $table->string('purpose')->nullable(); // App\Types\TransactionType (topup/order_debit/...)
            $table->decimal('amount', 16, 4);
            $table->decimal('balance_before', 16, 4);
            $table->decimal('balance_after', 16, 4);
            $table->string('currency', 3)->default('NGN');
            $table->string('payment_method')->nullable(); // flutterwave, admin, order, referral_bonus
            $table->string('status')->default('success'); // App\Types\WalletTransactionStatus
            $table->text('description')->nullable();
            $table->json('meta')->nullable(); // raw gateway payload, order_id, etc.
            // Not a DB-level FK (orders table is created later in the migration
            // order, and orders.id is a UUID) — just an indexed pointer,
            // validated at the app layer.
            $table->uuid('order_id')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index(['status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wallet_transactions');
    }
};

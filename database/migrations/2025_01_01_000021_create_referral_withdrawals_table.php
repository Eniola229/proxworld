<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('referral_withdrawals', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained()->cascadeOnDelete();
            $table->string('method')->default('bank'); // "bank" or "wallet" — see admin.referral.withdrawals.approve-bank/approve-wallet
            $table->decimal('amount', 16, 4);
            $table->string('currency', 3)->default('NGN');
            $table->string('bank_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('account_name')->nullable();
            $table->string('reference')->unique();
            $table->string('status')->default('pending'); // App\Types\WithdrawalStatus
            $table->text('failure_reason')->nullable();
            $table->foreignUuid('processed_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('referral_withdrawals');
    }
};

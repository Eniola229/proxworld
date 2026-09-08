<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reseller_withdrawals', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('reseller_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 16, 4);
            $table->string('currency', 3)->default('NGN');
            $table->string('bank_name');
            $table->string('account_number');
            $table->string('account_name');
            $table->string('reference')->unique();
            $table->string('status')->default('pending'); // App\Types\WithdrawalStatus
            $table->text('failure_reason')->nullable();
            $table->foreignId('processed_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reseller_withdrawals');
    }
};

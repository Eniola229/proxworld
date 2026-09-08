<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('referral_withdrawals', function (Blueprint $table) {
            $table->string('bank_code')->nullable()->after('bank_name');
            $table->string('description')->nullable();
            $table->decimal('balance_before', 20, 4)->nullable();
            $table->decimal('balance_after', 20, 4)->nullable();
            $table->string('flutterwave_transfer_id')->nullable();
        });

        Schema::table('reseller_withdrawals', function (Blueprint $table) {
            $table->string('bank_code')->nullable()->after('bank_name');
            $table->string('flutterwave_transfer_id')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('referral_withdrawals', function (Blueprint $table) {
            $table->dropColumn([
                'method',
                'bank_code',
                'description',
                'balance_before',
                'balance_after',
                'flutterwave_transfer_id',
            ]);
        });

        Schema::table('reseller_withdrawals', function (Blueprint $table) {
            $table->dropColumn([
                'bank_code',
                'flutterwave_transfer_id',
            ]);
        });
    }
};
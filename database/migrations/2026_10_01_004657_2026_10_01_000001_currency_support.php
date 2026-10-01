<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'preferred_currency')) {
            Schema::table('users', fn (Blueprint $t) => $t->string('preferred_currency', 3)->default('NGN')->after('country'));
        }
        if (! Schema::hasColumn('wallet_transactions', 'currency')) {
            Schema::table('wallet_transactions', fn (Blueprint $t) => $t->string('currency', 3)->default('NGN'));
        }
        if (! Schema::hasColumn('orders', 'currency')) {
            Schema::table('orders', fn (Blueprint $t) => $t->string('currency', 3)->default('NGN'));
        }
    }

    public function down(): void {}
};
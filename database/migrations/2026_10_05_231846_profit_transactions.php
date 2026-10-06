<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasColumn('profit_transactions', 'order_extension_id')) {
            return;
        }

        Schema::table('profit_transactions', function (Blueprint $t) {
            // Plain unique UUID column: it keeps the ledger idempotent without a fragile FK.
            $t->uuid('order_extension_id')->nullable()->unique()->after('order_id');
        });
    }

    public function down(): void
    {
        Schema::table('profit_transactions', function (Blueprint $t) {
            $t->dropUnique(['order_extension_id']);
            $t->dropColumn('order_extension_id');
        });
    }
};
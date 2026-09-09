<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignUuid('reseller_id')->nullable()->after('id')
                ->constrained('resellers')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // No dropConstrainedForeignUuid() helper in Laravel, so drop the
            // FK and column explicitly instead.
            $table->dropForeign(['reseller_id']);
            $table->dropColumn('reseller_id');
        });
    }
};
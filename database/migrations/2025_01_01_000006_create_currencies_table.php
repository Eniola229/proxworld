<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('currencies', function (Blueprint $table) {
            $table->uuid('id')->primary();
            // Column named `currency` (not `code`) to match the admin
            // exchange-rates view, which iterates these as $provider->currency.
            $table->string('currency', 3)->unique();
            $table->string('name');
            $table->string('symbol', 8);
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('currencies');
    }
};

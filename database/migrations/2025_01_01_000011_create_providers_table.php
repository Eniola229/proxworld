<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * A single row per proxy supplier (Smartproxy, IPRoyal, your next one...).
 * To add a new supplier: write one Driver class implementing
 * App\ProxyProviders\Contracts\ProxyProviderContract, add one row here
 * pointing `driver` at that class, paste in the API key. Nothing else
 * in the app changes. See App\ProxyProviders\ProxyProviderFactory.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('providers', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('driver'); // FQCN of the Driver class, e.g. App\ProxyProviders\Drivers\SmartproxyDriver
            $table->string('auth_type')->default('bearer_token'); // App\Types\ProviderAuthType
            $table->string('api_url');
            $table->text('api_key')->nullable(); // encrypted cast on the model
            $table->text('api_secret')->nullable(); // encrypted cast — some providers need a key+secret pair
            $table->unsignedInteger('priority')->default(0); // lower = tried first on failover
            $table->boolean('is_active')->default(true);
            $table->decimal('cached_balance', 16, 4)->nullable();
            $table->string('cached_balance_currency', 3)->nullable();
            $table->timestamp('balance_checked_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['is_active', 'priority']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('providers');
    }
};

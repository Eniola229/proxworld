<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable(); // nullable for Google-only accounts
            $table->string('google_id')->nullable()->unique();
            $table->string('avatar')->nullable();
            $table->string('phone')->nullable();
            $table->string('country', 2)->nullable(); // ISO 3166-1 alpha-2
            $table->string('preferred_currency', 3)->default('NGN');

            // --- Wallet (spendable) — write-protected at the app layer.
            // Never mass-assign; only App\Services\WalletService may change this,
            // inside a locked DB transaction, always mirrored to wallet_transactions.
            $table->decimal('balance', 16, 4)->default(0);

            // --- Reseller earnings (withdrawable) — same write protection,
            // via App\Services\ProfitService + profit_transactions ledger.
            $table->decimal('profit_balance', 16, 4)->default(0);

            $table->string('status')->default('active'); // App\Types\AccountStatus
            $table->boolean('is_reseller')->default(false);
            $table->string('reseller_status')->default('none'); // App\Types\ResellerStatus
            $table->decimal('reseller_discount_percent', 5, 2)->default(0);
            $table->timestamp('reseller_approved_at')->nullable();

            $table->string('referral_code')->unique()->nullable();
            $table->foreignUuid('referred_by_id')->nullable()->constrained('users')->nullOnDelete();
            // Referral program earnings — separate again from wallet `balance` and
            // reseller `profit_balance`. Also write-protected; only
            // App\Services\ReferralService may touch this.
            $table->decimal('referral_balance', 16, 4)->default(0);

            $table->boolean('two_factor_enabled')->default(false);
            $table->string('two_factor_secret')->nullable();
            $table->timestamp('terms_accepted_at')->nullable();
            $table->timestamp('welcome_modal_seen_at')->nullable();

            $table->rememberToken();
            $table->timestamps();

            $table->index(['status']);
            $table->index(['is_reseller', 'reseller_status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};

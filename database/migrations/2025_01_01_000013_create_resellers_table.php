<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * A white-label storefront owned by a User. The owner logs in with their
 * normal customer account (no separate reseller auth needed) and manages
 * this panel at /reseller/manage/*. Their own end-customers reach the
 * storefront via the reseller's subdomain and register/order as regular
 * platform Users, tagged reseller_id on the order.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resellers', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();
            $table->string('panel_name');
            $table->string('subdomain')->unique();
            $table->string('custom_domain')->nullable()->unique();
            $table->string('custom_domain_status')->nullable(); // null=unverified, "verified"
            $table->string('logo_path')->nullable();
            $table->string('primary_color')->default('#16a34a');
            $table->decimal('default_markup_percent', 5, 2)->default(20);

            // Support channels — every one optional; the storefront only
            // renders the ones a reseller actually fills in.
            $table->string('support_email')->nullable();
            $table->string('support_telegram')->nullable();
            $table->string('support_whatsapp')->nullable();

            $table->string('status')->default('pending'); // App\Types\ResellerStatus (reusing: pending/approved/rejected + "suspended" handled via is_active below)
            $table->boolean('is_suspended')->default(false);
            $table->text('rejection_reason')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->string('server_ip')->nullable(); // shown to reseller for DNS pointing, informational only

            // Reseller's own spendable wallet (funds their storefront's order
            // fulfillment) — write-protected exactly like users.balance, only
            // via App\Services\ResellerWalletService.
            $table->decimal('balance', 16, 4)->default(0);

            // Reseller's withdrawable earnings from customer markups — write
            // protected via App\Services\ResellerProfitService.
            $table->decimal('profit_balance', 16, 4)->default(0);
            $table->decimal('total_profit_earned', 16, 4)->default(0); // lifetime, never decremented

            $table->timestamps();

            $table->index(['status', 'is_suspended']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resellers');
    }
};

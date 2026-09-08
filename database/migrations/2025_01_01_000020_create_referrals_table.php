<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('referrals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('referrer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('referred_user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->boolean('has_deposited')->default(false);
            $table->boolean('has_ordered')->default(false);
            $table->boolean('bonus_paid')->default(false);
            $table->decimal('bonus_amount', 16, 4)->default(100); // NGN, configurable via settings
            $table->timestamp('bonus_paid_at')->nullable();
            $table->timestamps();

            $table->index(['referrer_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('referrals');
    }
};

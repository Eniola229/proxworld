<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Standard spatie/laravel-activitylog schema, plus two extra columns
 * (causer_guard, ip_address) so the admin log viewer can filter "was this
 * a customer or an admin?" and "from what IP" without joining anything.
 * Populated by:
 *   - App\Http\Middleware\LogActivity (every state-changing request)
 *   - LogsActivity trait on key models (Order, WithdrawalRequest, etc.) for
 *     automatic before/after field diffs
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_log', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('log_name')->nullable();
            $table->text('description');
            $table->nullableMorphs('subject', 'subject');
            $table->string('event')->nullable();
            $table->nullableMorphs('causer', 'causer');
            $table->string('causer_guard')->nullable(); // "web" or "admin"
            $table->string('ip_address')->nullable();
            $table->string('method')->nullable();
            $table->string('url')->nullable();
            $table->json('properties')->nullable();
            $table->uuid('batch_uuid')->nullable();
            $table->timestamps();

            $table->index('log_name');
            $table->index(['causer_type', 'causer_id']);
            $table->index(['subject_type', 'subject_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_log');
    }
};

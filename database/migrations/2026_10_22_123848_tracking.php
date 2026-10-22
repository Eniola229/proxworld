<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tracking_links', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('label', 120)->nullable();
            $table->string('source', 120)->index();
            $table->string('medium', 120)->nullable();
            $table->string('campaign', 120)->nullable();
            $table->string('content', 150)->nullable();
            $table->string('term', 150)->nullable();
            $table->string('landing_path', 255)->default('/');
            $table->timestamps();
        });

        Schema::create('tracking_sessions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('visitor_token')->unique();

            $table->foreignUuid('user_id')->nullable()->index()
                ->constrained('users')->nullOnDelete();

            $table->string('first_source', 120)->default('direct')->index();
            $table->string('first_medium', 120)->nullable();
            $table->string('first_campaign', 120)->nullable();

            $table->string('ip_address', 45)->nullable()->index();
            $table->char('country_code', 2)->nullable()->index();
            $table->string('country_name', 100)->nullable();
            $table->string('device_type', 20)->nullable()->index();
            $table->string('device_name', 120)->nullable();
            $table->string('os', 60)->nullable();
            $table->string('browser', 60)->nullable();
            $table->text('user_agent')->nullable();

            $table->unsignedInteger('hits_count')->default(0);
            $table->timestamp('linked_at')->nullable();
            $table->timestamp('last_hit_at')->nullable();
            $table->timestamps();

            $table->index('created_at');
        });

        Schema::create('tracking_hits', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tracking_session_id')
                ->constrained('tracking_sessions')->cascadeOnDelete();

            $table->string('source', 120)->index();
            $table->string('medium', 120)->nullable();
            $table->string('campaign', 120)->nullable();
            $table->string('content', 150)->nullable();
            $table->string('term', 150)->nullable();

            $table->text('landing_url')->nullable();
            $table->text('referrer')->nullable();
            $table->string('laravel_session_id', 100)->nullable();

            $table->string('ip_address', 45)->nullable()->index();
            $table->char('country_code', 2)->nullable();
            $table->string('country_name', 100)->nullable();
            $table->string('device_type', 20)->nullable();
            $table->string('device_name', 120)->nullable();
            $table->string('os', 60)->nullable();
            $table->string('browser', 60)->nullable();
            $table->text('user_agent')->nullable();

            $table->timestamp('created_at')->useCurrent()->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tracking_hits');
        Schema::dropIfExists('tracking_sessions');
        Schema::dropIfExists('tracking_links');
    }
};
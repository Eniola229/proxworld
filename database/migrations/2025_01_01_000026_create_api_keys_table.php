<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Custom API key table for "pay for API" customers (public API surface),
 * separate from Sanctum. The admin UI displays the full key value on
 * request, so we store it `encrypted` (decryptable) rather than hash-only —
 * verified on each request against key_hash for a fast indexed lookup, and
 * decrypted only when the owner explicitly views/copies it.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('api_keys', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('key_hash')->unique(); // sha256, used for fast lookup on every API request
            $table->text('key_encrypted'); // encrypted cast, decrypted only for display/copy
            $table->string('key_preview', 8); // last few chars, shown in lists without decrypting
            $table->string('status')->default('active'); // active/inactive
            $table->json('abilities')->nullable(); // e.g. ["orders.create","orders.read"] — null = full access
            $table->timestamp('last_used_at')->nullable();
            $table->string('last_used_ip')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('api_keys');
    }
};

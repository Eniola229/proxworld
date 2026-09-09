<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Backs the "add admin -> they get an email to set their own password"
 * flow. A row is created alongside the (passwordless) admin record; the
 * signed link in the email points back at token+admin_id and is single-use.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_invitations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('admin_id')->constrained('admins')->cascadeOnDelete();
            $table->string('token')->unique();
            $table->foreignUuid('invited_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamp('expires_at');
            $table->timestamp('accepted_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_invitations');
    }
};

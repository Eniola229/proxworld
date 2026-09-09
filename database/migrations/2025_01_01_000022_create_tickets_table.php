<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained()->cascadeOnDelete();
            $table->string('subject');
            $table->string('status')->default('open'); // App\Types\TicketStatus
            $table->foreignUuid('assigned_admin_id')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'updated_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};

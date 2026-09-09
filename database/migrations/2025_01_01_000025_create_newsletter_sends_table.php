<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('newsletter_sends', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('newsletter_id');
            $table->foreign('newsletter_id')->references('id')->on('newsletters')->cascadeOnDelete();
            $table->foreignUuid('user_id')->constrained()->cascadeOnDelete();
            $table->string('status')->default('pending'); // pending/sent/failed
            $table->text('error')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->unique(['newsletter_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('newsletter_sends');
    }
};

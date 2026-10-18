<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ticket_messages', function (Blueprint $table) {
            // A message can now be just an image/video, so the text becomes optional.
            // (Change text() to whatever type your column already is.)
            $table->text('message')->nullable()->change();

            $table->string('attachment_url', 1000)->nullable();
            $table->string('attachment_type', 10)->nullable();      // image | video
            $table->string('attachment_public_id')->nullable();     // for deleting from Cloudinary
            $table->string('attachment_name')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('ticket_messages', function (Blueprint $table) {
            $table->dropColumn(['attachment_url', 'attachment_type', 'attachment_public_id', 'attachment_name']);
        });
    }
};
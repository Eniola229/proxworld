<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Doubles as a blog post. Sending it as a newsletter (queued, per-recipient
 * batch) and showing it on the public /blog listing are two independent
 * toggles — a draft can be a blog post without ever being emailed, and vice
 * versa, though normally you'll want both.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('newsletters', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('subject');
            $table->string('slug')->unique(); // public blog URL: /blog/{slug}
            $table->text('excerpt')->nullable();
            $table->longText('body'); // rich text HTML from the Trix/TinyMCE editor
            $table->string('featured_image_url')->nullable(); // Cloudinary URL
            $table->string('featured_video_url')->nullable(); // Cloudinary URL
            $table->string('audience')->default('all'); // App\Types\NewsletterAudience
            $table->string('status')->default('draft'); // App\Types\NewsletterStatus
            $table->boolean('show_on_blog')->default(true);
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->unsignedInteger('recipients_total')->default(0);
            $table->unsignedInteger('recipients_sent')->default(0);
            $table->unsignedInteger('recipients_failed')->default(0);
            $table->timestamps();

            $table->index(['status', 'scheduled_at']);
            $table->index(['show_on_blog', 'sent_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('newsletters');
    }
};

<?php

namespace App\Jobs;

use App\Mail\NewsletterMail;
use App\Models\Newsletter;
use App\Models\NewsletterSend;
use App\Models\User;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendSingleNewsletterEmail implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(public string $newsletterId, public int $userId)
    {
    }

    public function handle(): void
    {
        if ($this->batch()?->cancelled()) {
            return;
        }

        $newsletter = Newsletter::find($this->newsletterId);
        $user = User::find($this->userId);
        $send = NewsletterSend::where('newsletter_id', $this->newsletterId)->where('user_id', $this->userId)->first();

        if (! $newsletter || ! $user || ! $send) {
            return;
        }

        try {
            Mail::to($user->email)->send(new NewsletterMail($newsletter));
            $send->update(['status' => 'sent', 'sent_at' => now()]);
            $newsletter->increment('recipients_sent');
        } catch (\Throwable $e) {
            $send->update(['status' => 'failed', 'error' => $e->getMessage()]);
            $newsletter->increment('recipients_failed');
        }
    }
}

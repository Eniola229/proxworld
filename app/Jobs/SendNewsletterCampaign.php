<?php

namespace App\Jobs;

use App\Models\Newsletter;
use App\Models\NewsletterSend;
use App\Models\User;
use App\Types\NewsletterStatus;
use Illuminate\Bus\Batch;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Bus;

/**
 * Dispatches one SendSingleNewsletterEmail job per recipient inside a
 * Bus::batch, so a single slow/failed recipient never blocks the rest, and
 * Brevo's rate limits are respected via the queue's own throttling rather
 * than sending everything at once.
 */
class SendNewsletterCampaign implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public string $newsletterId)
    {
    }

    public function handle(): void
    {
        $newsletter = Newsletter::find($this->newsletterId);

        if (! $newsletter) {
            return;
        }

        $recipients = User::audience($newsletter->audience)->get();

        $newsletter->update(['recipients_total' => $recipients->count()]);

        $jobs = $recipients->map(function (User $user) use ($newsletter) {
            NewsletterSend::firstOrCreate(['newsletter_id' => $newsletter->id, 'user_id' => $user->id]);

            return new SendSingleNewsletterEmail($newsletter->id, $user->id);
        });

        Bus::batch($jobs)
            ->then(function (Batch $batch) use ($newsletter) {
                $newsletter->update(['status' => NewsletterStatus::SENT, 'sent_at' => now()]);
            })
            ->catch(function (Batch $batch, \Throwable $e) use ($newsletter) {
                $newsletter->update(['status' => NewsletterStatus::FAILED]);
            })
            ->name("newsletter-{$newsletter->id}")
            ->dispatch();
    }
}

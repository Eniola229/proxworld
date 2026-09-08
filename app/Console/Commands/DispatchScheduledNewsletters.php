<?php

namespace App\Console\Commands;

use App\Jobs\SendNewsletterCampaign;
use App\Models\Newsletter;
use App\Types\NewsletterStatus;
use Illuminate\Console\Command;

class DispatchScheduledNewsletters extends Command
{
    protected $signature = 'newsletter:dispatch-scheduled';
    protected $description = 'Send newsletters that were scheduled for a time that has now arrived.';

    public function handle(): int
    {
        $due = Newsletter::where('status', NewsletterStatus::SCHEDULED)
            ->where('scheduled_at', '<=', now())
            ->get();

        foreach ($due as $newsletter) {
            $this->info("Dispatching newsletter: {$newsletter->subject}");
            SendNewsletterCampaign::dispatch($newsletter->id);
        }

        return self::SUCCESS;
    }
}

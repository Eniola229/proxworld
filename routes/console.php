<?php

use Illuminate\Support\Facades\Schedule;

// The safety net for stuck orders — runs frequently since this is the
// user-facing "did my money disappear" risk.
Schedule::command('orders:check-pending')->everyFiveMinutes()->withoutOverlapping();

Schedule::command('exchange-rates:sync')->everySixHours();

Schedule::command('providers:sync-balances')->hourly();

Schedule::command('providers:sync-services')->daily();

Schedule::command('newsletter:dispatch-scheduled')->everyMinute()->withoutOverlapping();

Schedule::command('queue:prune-failed --hours=168')->daily();

Schedule::command('queue:prune-batches --hours=48')->daily();

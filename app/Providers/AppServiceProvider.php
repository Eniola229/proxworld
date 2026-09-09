<?php

namespace App\Providers;

use App\Mail\Transport\BrevoApiTransport;
use Illuminate\Mail\MailManager;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;


class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->app->make(MailManager::class)->extend('brevo', function () {
            return new BrevoApiTransport(config('services.brevo.api_key'));
        });

        Paginator::useBootstrapFive();

    }
}

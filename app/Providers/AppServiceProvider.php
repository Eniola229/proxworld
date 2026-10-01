<?php

namespace App\Providers;

use App\Mail\Transport\BrevoApiTransport;
use App\Services\CurrencyService;
use App\Services\ExchangeRateService;
use Illuminate\Mail\MailManager;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(CurrencyService::class);
    }

    public function boot(): void
    {
        $this->app->make(MailManager::class)->extend('brevo', function () {
            return new BrevoApiTransport(config('services.brevo.api_key'));
        });

        Paginator::useBootstrapFive();

        // @money($amount)  -> user's wallet currency  |  @money($amount, $currency) -> that currency
        Blade::directive('money', function (string $expression) {
            return "<?php echo app(\\App\\Services\\CurrencyService::class)->format({$expression}); ?>";
        });

        View::composer('components.nav', function ($view) {
            $currencies = app(CurrencyService::class);

            $view->with([
                'walletCurrency' => $currencies->walletCurrency(auth()->user()),
                'currencyOptions' => array_values($currencies->supported()),
            ]);
        });

        // Home page prices: your NGN list prices, shown in the visitor's currency (by IP, USD fallback)
        View::composer('welcome', function ($view) {
            $currencies = app(CurrencyService::class);
            $rates = app(ExchangeRateService::class);

            $code = $currencies->forVisitor(request());

            $ngn = ['residential' => 2408, 'isp' => 2477, 'datacenter' => 1913, 'mobile' => 7911];

            $prices = [];
            foreach ($ngn as $key => $amount) {
                $prices[$key] = $currencies->formatCompact($rates->convert((float) $amount, 'NGN', $code), $code);
            }

            $view->with(['homePrices' => $prices, 'homeCurrency' => $code]);
        });
    }
}
<?php

namespace Xgrz\PayNow;

use Illuminate\Support\ServiceProvider;
use Xgrz\PayNow\Console\Commands\PayNowPaymentStatesCommand;
use Xgrz\PayNow\Console\Commands\PayNowRefundsStatesCommand;

class PayNowServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        self::setupMigrations();
        self::setupNotificationRouting();
        self::setupTranslations();

        $this->commands([
            PayNowPaymentStatesCommand::class,
            PayNowRefundsStatesCommand::class,
        ]);

        $this->publishes(
            [
                __DIR__ . '/../config/paynow.php' => config_path('paynow.php'),
            ],
            'paynow-config'
        );
    }

    private function setupMigrations(): void
    {
        if ($this->app->runningInConsole()) {
            $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        }
    }

    private function setupNotificationRouting(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');
    }

    private function setupTranslations(): void
    {
        $this->loadTranslationsFrom(__DIR__ . '/../lang', 'paynow');
    }
}

<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureStripe();
    }

    protected function configureStripe(): void
    {
        if (config('cashier.calculate_taxes', false)) {
            \Laravel\Cashier\Cashier::calculateTaxes();
        }
    }
}

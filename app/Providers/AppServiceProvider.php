<?php

namespace App\Providers;

use App\Services\SettingsService;
use Illuminate\Support\ServiceProvider;
use Laravel\Cashier\Cashier;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->configureHttps();
        $this->configureStripe();
    }

    protected function configureHttps(): void
    {
        if (config('app.env') === 'production' || request()->header('X-Forwarded-Proto') === 'https') {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }
    }

    protected function configureStripe(): void
    {
        try {
            $settings = app(SettingsService::class);

            $stripeKey = $settings->get('stripe_key', env('STRIPE_KEY'));
            $stripeSecret = $settings->get('stripe_secret', env('STRIPE_SECRET'));

            if ($stripeKey && $stripeSecret) {
                config(['cashier.key' => $stripeKey]);
                config(['cashier.secret' => $stripeSecret]);
            }

            $webhookSecret = $settings->get('stripe_webhook_secret', env('STRIPE_WEBHOOK_SECRET'));
            if ($webhookSecret) {
                config(['cashier.webhook.secret' => $webhookSecret]);
            }

            $priceMonthly = $settings->get('stripe_price_monthly', env('STRIPE_PRICE_MONTHLY'));
            $priceYearly = $settings->get('stripe_price_yearly', env('STRIPE_PRICE_YEARLY'));
            $trialDays = $settings->get('trial_days', env('CASHIER_TRIAL_DAYS', 0));

            if ($priceMonthly) {
                config(['cashier.price_monthly' => $priceMonthly]);
            }
            if ($priceYearly) {
                config(['cashier.price_yearly' => $priceYearly]);
            }
            if ($trialDays) {
                config(['cashier.trial_days' => $trialDays]);
            }

            $calculateTaxes = $settings->get('stripe_calculate_taxes', env('CASHIER_CALCULATE_TAXES', false));
            if ($calculateTaxes) {
                Cashier::calculateTaxes();
            }
        } catch (\Exception $e) {
            \Log::warning('Could not load Stripe settings from database', [
                'error' => $e->getMessage()
            ]);
        }
    }
}

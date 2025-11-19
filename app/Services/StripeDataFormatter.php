<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Laravel\Cashier\Cashier;
use Laravel\Cashier\Subscription;

class StripeDataFormatter
{
    /**
     * Format subscription data for display
     *
     * @param Subscription $subscription
     * @return array|null
     */
    public function formatSubscription(Subscription $subscription): ?array
    {
        try {
            $stripeSubscription = $subscription->asStripeSubscription();
            $user = $subscription->user;

            return [
                'id' => $subscription->id,
                'stripe_id' => $subscription->stripe_id,
                'plan' => $user->planName(),
                'plan_display' => $user->planDisplayName(),
                'price' => $user->planPrice(),
                'price_id' => $subscription->stripe_price,
                'status' => $user->subscriptionStatus(),
                'stripe_status' => $subscription->stripe_status,
                'quantity' => $subscription->quantity,
                'current_period_start' => $stripeSubscription->current_period_start,
                'current_period_start_formatted' => date('d/m/Y', $stripeSubscription->current_period_start),
                'current_period_end' => $stripeSubscription->current_period_end,
                'current_period_end_formatted' => date('d/m/Y', $stripeSubscription->current_period_end),
                'cancel_at_period_end' => $subscription->onGracePeriod(),
                'on_grace_period' => $subscription->onGracePeriod(),
                'ends_at' => $subscription->ends_at?->format('d/m/Y'),
                'trial_ends_at' => $subscription->trial_ends_at?->format('d/m/Y'),
                'on_trial' => $subscription->onTrial(),
                'recurring' => $subscription->recurring(),
                'active' => $subscription->active(),
                'past_due' => $subscription->pastDue(),
                'incomplete' => $subscription->incomplete(),
                'canceled' => $subscription->canceled(),
                'created_at' => $subscription->created_at?->format('d/m/Y H:i') ?? null,
                'updated_at' => $subscription->updated_at?->format('d/m/Y H:i') ?? null,
            ];
        } catch (\Exception $e) {
            Log::error('Failed to format subscription', [
                'subscription_id' => $subscription->id ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return null;
        }
    }

    /**
     * Format invoice data for display
     *
     * @param mixed $invoice
     * @return array|null
     */
    public function formatInvoice($invoice): ?array
    {
        try {
            $stripeInvoice = $invoice->asStripeInvoice();

            return [
                'id' => $invoice->id,
                'stripe_id' => $stripeInvoice->id,
                'number' => $stripeInvoice->number,
                'date' => $invoice->date()->toDateTimeString(),
                'date_formatted' => $invoice->date()->format('d/m/Y'),
                'total' => $invoice->total(),
                'total_formatted' => number_format($invoice->total() / 100, 2, ',', ' ') . ' €',
                'subtotal' => $invoice->subtotal(),
                'subtotal_formatted' => number_format($invoice->subtotal() / 100, 2, ',', ' ') . ' €',
                'tax' => $invoice->tax(),
                'tax_formatted' => number_format($invoice->tax() / 100, 2, ',', ' ') . ' €',
                'status' => $invoice->status,
                'paid' => $stripeInvoice->paid,
                'currency' => strtoupper($stripeInvoice->currency),
                'download_url' => $invoice->id ? route('subscription.invoice', $invoice->id) : null,
                'invoice_pdf' => $stripeInvoice->invoice_pdf ?? null,
                'hosted_invoice_url' => $stripeInvoice->hosted_invoice_url ?? null,
            ];
        } catch (\Exception $e) {
            Log::error('Failed to format invoice', [
                'invoice_id' => $invoice->id ?? 'unknown',
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Format payment method data for display
     *
     * @param mixed $paymentMethod
     * @return array|null
     */
    public function formatPaymentMethod($paymentMethod): ?array
    {
        try {
            $formatted = [
                'id' => $paymentMethod->id,
                'type' => $paymentMethod->type,
            ];

            // Add card-specific data if available
            if ($paymentMethod->type === 'card' && isset($paymentMethod->card)) {
                $formatted['card'] = [
                    'brand' => ucfirst($paymentMethod->card->brand),
                    'last4' => $paymentMethod->card->last4,
                    'exp_month' => str_pad($paymentMethod->card->exp_month, 2, '0', STR_PAD_LEFT),
                    'exp_year' => $paymentMethod->card->exp_year,
                    'expiration' => str_pad($paymentMethod->card->exp_month, 2, '0', STR_PAD_LEFT) . '/' . $paymentMethod->card->exp_year,
                    'fingerprint' => $paymentMethod->card->fingerprint ?? null,
                ];
            }

            return $formatted;
        } catch (\Exception $e) {
            Log::error('Failed to format payment method', [
                'payment_method_id' => $paymentMethod->id ?? 'unknown',
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Format all invoices for a user
     *
     * @param User $user
     * @return array
     */
    public function formatUserInvoices(User $user): array
    {
        try {
            $invoices = $user->invoices();

            return collect($invoices)->map(function ($invoice) {
                return $this->formatInvoice($invoice);
            })->filter()->toArray();
        } catch (\Exception $e) {
            Log::error('Failed to format user invoices', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return [];
        }
    }

    /**
     * Format all payment methods for a user
     *
     * @param User $user
     * @return array
     */
    public function formatUserPaymentMethods(User $user): array
    {
        try {
            $paymentMethods = $user->paymentMethods();

            return collect($paymentMethods)->map(function ($paymentMethod) {
                return $this->formatPaymentMethod($paymentMethod);
            })->filter()->toArray();
        } catch (\Exception $e) {
            Log::error('Failed to format user payment methods', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return [];
        }
    }

    /**
     * Format default payment method for a user
     *
     * @param User $user
     * @return array|null
     */
    public function formatDefaultPaymentMethod(User $user): ?array
    {
        try {
            if (!$user->hasDefaultPaymentMethod()) {
                return null;
            }

            $paymentMethod = $user->defaultPaymentMethod();
            return $this->formatPaymentMethod($paymentMethod);
        } catch (\Exception $e) {
            Log::error('Failed to format default payment method', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Format checkout session data
     *
     * @param mixed $session
     * @return array|null
     */
    public function formatCheckoutSession($session): ?array
    {
        try {
            return [
                'id' => $session->id,
                'payment_status' => $session->payment_status,
                'status' => $session->status,
                'customer' => $session->customer,
                'subscription' => $session->subscription,
                'amount_total' => $session->amount_total,
                'amount_total_formatted' => number_format($session->amount_total / 100, 2, ',', ' ') . ' €',
                'currency' => strtoupper($session->currency),
                'created' => $session->created,
                'created_formatted' => date('d/m/Y H:i', $session->created),
            ];
        } catch (\Exception $e) {
            Log::error('Failed to format checkout session', [
                'session_id' => $session->id ?? 'unknown',
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Format user's complete Stripe data
     *
     * @param User $user
     * @return array
     */
    public function formatUserStripeData(User $user): array
    {
        $data = [
            'has_stripe_id' => $user->hasStripeId(),
            'stripe_id' => $user->stripe_id,
            'is_premium' => $user->isPremium(),
            'is_subscribed' => $user->subscribed('default'),
            'subscription' => null,
            'invoices' => [],
            'payment_methods' => [],
            'default_payment_method' => null,
        ];

        if ($user->subscribed('default')) {
            $subscription = $user->subscription('default');
            $data['subscription'] = $this->formatSubscription($subscription);
        }

        $data['invoices'] = $this->formatUserInvoices($user);
        $data['payment_methods'] = $this->formatUserPaymentMethods($user);
        $data['default_payment_method'] = $this->formatDefaultPaymentMethod($user);

        return $data;
    }

    /**
     * Format subscription item for export
     *
     * @param Subscription $subscription
     * @return array
     */
    public function formatSubscriptionForExport(Subscription $subscription): array
    {
        return [
            'stripe_id' => $subscription->stripe_id,
            'stripe_status' => $subscription->stripe_status,
            'stripe_price' => $subscription->stripe_price,
            'quantity' => $subscription->quantity,
            'trial_ends_at' => $subscription->trial_ends_at?->toDateTimeString(),
            'ends_at' => $subscription->ends_at?->toDateTimeString(),
            'created_at' => $subscription->created_at->toDateTimeString(),
            'updated_at' => $subscription->updated_at->toDateTimeString(),
        ];
    }

    /**
     * Format invoice for export
     *
     * @param mixed $invoice
     * @return array
     */
    public function formatInvoiceForExport($invoice): array
    {
        return [
            'id' => $invoice->id,
            'date' => $invoice->date()->toDateTimeString(),
            'total' => $invoice->total() / 100,
            'status' => $invoice->status,
        ];
    }

    /**
     * Format payment method for export
     *
     * @param mixed $paymentMethod
     * @return array
     */
    public function formatPaymentMethodForExport($paymentMethod): array
    {
        $data = [
            'id' => $paymentMethod->id,
            'type' => $paymentMethod->type,
        ];

        if ($paymentMethod->type === 'card' && isset($paymentMethod->card)) {
            $data['last4'] = $paymentMethod->card->last4;
            $data['brand'] = $paymentMethod->card->brand;
        }

        return $data;
    }

    /**
     * Calculate total spent by user
     *
     * @param User $user
     * @return float
     */
    public function calculateTotalSpent(User $user): float
    {
        try {
            if (!$user->hasStripeId()) {
                return 0.0;
            }

            $total = 0;
            $invoices = $user->invoices();

            foreach ($invoices as $invoice) {
                if ($invoice->status === 'paid') {
                    $total += $invoice->total();
                }
            }

            return $total / 100; // Convert cents to euros
        } catch (\Exception $e) {
            Log::error('Failed to calculate total spent', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return 0.0;
        }
    }
}

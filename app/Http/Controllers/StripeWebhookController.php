<?php

namespace App\Http\Controllers;

use App\Models\StripeWebhookLog;
use App\Models\User;
use App\Notifications\SubscriptionCanceled;
use App\Notifications\SubscriptionRenewed;
use App\Notifications\PaymentFailed;
use App\Services\SubscriptionStatsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Laravel\Cashier\Http\Controllers\WebhookController as CashierController;

class StripeWebhookController extends CashierController
{
    protected $subscriptionStatsService;

    public function __construct(SubscriptionStatsService $subscriptionStatsService)
    {
        $this->subscriptionStatsService = $subscriptionStatsService;
    }

    public function handleWebhook(Request $request)
    {
        $payload = json_decode($request->getContent(), true);
        $eventId = $payload['id'] ?? null;
        $eventType = $payload['type'] ?? null;

        if ($eventId && $eventType) {
            $log = StripeWebhookLog::firstOrCreate(
                ['event_id' => $eventId],
                [
                    'event_type' => $eventType,
                    'payload' => $payload,
                    'status' => 'pending',
                ]
            );

            Log::info('Stripe webhook received', [
                'event_id' => $eventId,
                'event_type' => $eventType,
            ]);
        }

        try {
            $response = parent::handleWebhook($request);

            if (isset($log)) {
                $log->markAsProcessed();
                $this->subscriptionStatsService->clearCache();
            }

            return $response;
        } catch (\Exception $e) {
            if (isset($log)) {
                $log->markAsFailed($e->getMessage());
            }

            Log::error('Stripe webhook processing error', [
                'event_id' => $eventId ?? null,
                'event_type' => $eventType ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }

    protected function handleCustomerSubscriptionCreated(array $payload)
    {
        $data = $payload['data']['object'];
        $customerId = $data['customer'];

        $user = User::where('stripe_id', $customerId)->first();

        if ($user) {
            Log::info('Subscription created for user', [
                'user_id' => $user->id,
                'subscription_id' => $data['id'],
                'status' => $data['status'],
            ]);
        }

        return $this->successMethod();
    }

    protected function handleCustomerSubscriptionUpdated(array $payload)
    {
        $data = $payload['data']['object'];
        $customerId = $data['customer'];

        $user = User::where('stripe_id', $customerId)->first();

        if ($user) {
            Log::info('Subscription updated for user', [
                'user_id' => $user->id,
                'subscription_id' => $data['id'],
                'status' => $data['status'],
                'cancel_at_period_end' => $data['cancel_at_period_end'],
            ]);

            if ($data['cancel_at_period_end']) {
                try {
                    $user->notify(new SubscriptionCanceled());
                } catch (\Exception $e) {
                    Log::error('Failed to send cancellation notification', [
                        'user_id' => $user->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }

        return $this->successMethod();
    }

    protected function handleCustomerSubscriptionDeleted(array $payload)
    {
        $data = $payload['data']['object'];
        $customerId = $data['customer'];

        $user = User::where('stripe_id', $customerId)->first();

        if ($user) {
            Log::info('Subscription deleted for user', [
                'user_id' => $user->id,
                'subscription_id' => $data['id'],
            ]);
        }

        return $this->successMethod();
    }

    protected function handleInvoicePaymentSucceeded(array $payload)
    {
        $data = $payload['data']['object'];
        $customerId = $data['customer'];

        $user = User::where('stripe_id', $customerId)->first();

        if ($user && $user->subscribed('default')) {
            Log::info('Invoice payment succeeded', [
                'user_id' => $user->id,
                'invoice_id' => $data['id'],
                'amount' => $data['amount_paid'],
            ]);

            if ($data['billing_reason'] === 'subscription_cycle') {
                try {
                    $user->notify(new SubscriptionRenewed(
                        $data['amount_paid'] / 100,
                        $data['currency']
                    ));
                } catch (\Exception $e) {
                    Log::error('Failed to send renewal notification', [
                        'user_id' => $user->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }

        return $this->successMethod();
    }

    protected function handleInvoicePaymentFailed(array $payload)
    {
        $data = $payload['data']['object'];
        $customerId = $data['customer'];

        $user = User::where('stripe_id', $customerId)->first();

        if ($user) {
            Log::warning('Invoice payment failed', [
                'user_id' => $user->id,
                'invoice_id' => $data['id'],
                'amount' => $data['amount_due'],
                'attempt_count' => $data['attempt_count'] ?? 0,
            ]);

            try {
                $user->notify(new PaymentFailed(
                    $data['amount_due'] / 100,
                    $data['currency'],
                    $data['hosted_invoice_url'] ?? null
                ));
            } catch (\Exception $e) {
                Log::error('Failed to send payment failed notification', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $this->successMethod();
    }

    protected function handlePaymentMethodAutomaticallyUpdated(array $payload)
    {
        $data = $payload['data']['object'];

        Log::info('Payment method automatically updated', [
            'payment_method_id' => $data['id'],
            'customer' => $data['customer'],
        ]);

        return $this->successMethod();
    }

    protected function handleCustomerUpdated(array $payload)
    {
        $data = $payload['data']['object'];
        $customerId = $data['id'];

        $user = User::where('stripe_id', $customerId)->first();

        if ($user) {
            Log::info('Customer updated', [
                'user_id' => $user->id,
                'customer_id' => $customerId,
            ]);
        }

        return $this->successMethod();
    }

    protected function handleCustomerDeleted(array $payload)
    {
        $data = $payload['data']['object'];
        $customerId = $data['id'];

        $user = User::where('stripe_id', $customerId)->first();

        if ($user) {
            Log::warning('Customer deleted in Stripe', [
                'user_id' => $user->id,
                'customer_id' => $customerId,
            ]);

            $user->stripe_id = null;
            $user->pm_type = null;
            $user->pm_last_four = null;
            $user->save();
        }

        return $this->successMethod();
    }
}

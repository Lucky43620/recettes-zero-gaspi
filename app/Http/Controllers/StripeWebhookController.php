<?php

namespace App\Http\Controllers;

use App\Models\StripeWebhookLog;
use App\Models\User;
use App\Services\SubscriptionStatsService;
use Illuminate\Http\Request;
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
            throw $e;
        }
    }

    protected function handleCustomerSubscriptionCreated(array $payload)
    {
        $data = $payload['data']['object'];
        $userId = $data['metadata']['user_id'] ?? null;

        if ($userId) {
            $user = User::find($userId);
            if ($user) {
            }
        }

        return $this->successMethod();
    }

    protected function handleCustomerSubscriptionUpdated(array $payload)
    {
        $data = $payload['data']['object'];

        return $this->successMethod();
    }

    protected function handleCustomerSubscriptionDeleted(array $payload)
    {
        $data = $payload['data']['object'];

        return $this->successMethod();
    }

    protected function handleInvoicePaymentSucceeded(array $payload)
    {
        $data = $payload['data']['object'];
        $customerId = $data['customer'];

        $user = User::where('stripe_id', $customerId)->first();

        if ($user && $user->subscribed('default')) {
        }

        return $this->successMethod();
    }

    protected function handleInvoicePaymentFailed(array $payload)
    {
        $data = $payload['data']['object'];
        $customerId = $data['customer'];

        $user = User::where('stripe_id', $customerId)->first();

        if ($user) {
        }

        return $this->successMethod();
    }
}

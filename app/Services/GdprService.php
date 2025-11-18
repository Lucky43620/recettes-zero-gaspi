<?php

namespace App\Services;

use App\Models\DeletedUserAudit;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class GdprService
{
    public function exportUserData(User $user): string
    {
        $data = [
            'profile' => [
                'name' => $user->name,
                'email' => $user->email,
                'created_at' => $user->created_at->toDateTimeString(),
            ],
            'recipes' => $user->recipes()->with(['media', 'steps', 'ingredients'])->get()->toArray(),
            'comments' => $user->comments()->with('recipe')->get()->toArray(),
            'ratings' => $user->ratings()->with('recipe')->get()->toArray(),
            'favorites' => $user->favorites()->get()->toArray(),
            'collections' => $user->collections()->with('recipes')->get()->toArray(),
            'followers' => $user->followers()->select('id', 'name', 'email')->get()->toArray(),
            'following' => $user->following()->select('id', 'name', 'email')->get()->toArray(),
            'meal_plans' => $user->mealPlans()->with('recipes')->get()->toArray(),
            'shopping_lists' => $user->shoppingLists()->with('items')->get()->toArray(),
            'pantry' => $user->pantryItems()->with('ingredient')->get()->toArray(),
            'consents' => $user->consents()->get()->toArray(),
            'stripe' => $this->getStripeData($user),
        ];

        $filename = 'user_data_' . $user->id . '_' . now()->timestamp . '.json';
        $path = 'gdpr-exports/' . $filename;

        Storage::put($path, json_encode($data, JSON_PRETTY_PRINT));

        return Storage::path($path);
    }

    protected function getStripeData(User $user): array
    {
        if (!$user->hasStripeId()) {
            return [];
        }

        try {
            $stripeData = [
                'customer_id' => $user->stripe_id,
                'subscriptions' => [],
                'invoices' => [],
                'payment_methods' => [],
            ];

            if ($user->subscriptions()->count() > 0) {
                $stripeData['subscriptions'] = $user->subscriptions()->get()->map(function ($sub) {
                    return [
                        'stripe_id' => $sub->stripe_id,
                        'stripe_status' => $sub->stripe_status,
                        'stripe_price' => $sub->stripe_price,
                        'quantity' => $sub->quantity,
                        'trial_ends_at' => $sub->trial_ends_at,
                        'ends_at' => $sub->ends_at,
                        'created_at' => $sub->created_at,
                    ];
                })->toArray();
            }

            $invoices = $user->invoices();
            if ($invoices) {
                $stripeData['invoices'] = collect($invoices)->map(function ($invoice) {
                    return [
                        'id' => $invoice->id,
                        'date' => $invoice->date()->toDateTimeString(),
                        'total' => $invoice->total(),
                        'status' => $invoice->status,
                    ];
                })->toArray();
            }

            $paymentMethods = $user->paymentMethods();
            if ($paymentMethods) {
                $stripeData['payment_methods'] = collect($paymentMethods)->map(function ($pm) {
                    return [
                        'id' => $pm->id,
                        'type' => $pm->type,
                        'last4' => $pm->card->last4 ?? null,
                        'brand' => $pm->card->brand ?? null,
                    ];
                })->toArray();
            }

            return $stripeData;
        } catch (\Exception $e) {
            return ['error' => 'Unable to retrieve Stripe data: ' . $e->getMessage()];
        }
    }

    public function deleteUserData(User $user): void
    {
        $this->createAuditRecord($user);

        $user->recipes()->each(function ($recipe) {
            $recipe->clearMediaCollection('images');
            $recipe->delete();
        });

        $user->cooksnaps()->each(function ($cooksnap) {
            $cooksnap->clearMediaCollection('photos');
            $cooksnap->delete();
        });

        $user->comments()->delete();
        $user->ratings()->delete();
        $user->favorites()->detach();
        $user->collections()->delete();
        $user->followers()->detach();
        $user->following()->detach();
        $user->mealPlans()->delete();
        $user->shoppingLists()->delete();
        $user->pantryItems()->delete();
        $user->reports()->delete();
        $user->badges()->detach();
        $user->eventParticipations()->detach();
        $user->consents()->delete();

        $user->clearMediaCollection();

        $user->delete();
    }

    protected function createAuditRecord(User $user): void
    {
        $totalSpent = 0;
        $subscriptionHistory = [];

        if ($user->hasStripeId()) {
            try {
                $invoices = $user->invoices();
                if ($invoices) {
                    foreach ($invoices as $invoice) {
                        $totalSpent += $invoice->total();
                    }
                }

                $subscriptionHistory = $user->subscriptions()->get()->map(function ($sub) {
                    return [
                        'stripe_price' => $sub->stripe_price,
                        'started_at' => $sub->created_at,
                        'ended_at' => $sub->ends_at,
                        'status' => $sub->stripe_status,
                    ];
                })->toArray();
            } catch (\Exception $e) {
            }
        }

        $retentionDays = app(SettingsService::class)->get('data_retention_days', 3650);

        DeletedUserAudit::create([
            'original_user_id' => $user->id,
            'deletion_date' => now(),
            'stripe_customer_id' => $user->stripe_id,
            'total_spent' => $totalSpent / 100,
            'subscription_history' => $subscriptionHistory,
            'legal_retention_until' => now()->addDays($retentionDays),
            'deleted_by' => auth()->check() ? auth()->user()->name : 'User self-deletion',
        ]);
    }
}

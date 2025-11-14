<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SubscriptionController extends Controller
{
    /**
     * Show the subscription plans page.
     */
    public function index(): Response
    {
        $user = auth()->user();
        $isPremium = $user ? $user->isPremium() : false;
        $subscription = $isPremium ? $user->subscription('default') : null;

        return Inertia::render('Subscription/Index', [
            'plans' => [
                'free' => [
                    'name' => 'free',
                    'price' => 0,
                    'interval' => null,
                    'features' => [
                        'access_recipes',
                        'create_recipes',
                        'basic_pantry',
                        'basic_meal_plans',
                        'with_ads',
                    ],
                ],
                'monthly' => [
                    'name' => 'monthly',
                    'price' => 4.99,
                    'price_id' => config('stripe.price_monthly'),
                    'interval' => 'month',
                    'features' => [
                        'all_free_features',
                        'ai_menu_generator',
                        'advanced_pantry',
                        'expiry_alerts',
                        'recipe_suggestions',
                        'advanced_statistics',
                        'exclusive_content',
                        'no_ads',
                    ],
                ],
                'yearly' => [
                    'name' => 'yearly',
                    'price' => 49.90,
                    'price_id' => config('stripe.price_yearly'),
                    'interval' => 'year',
                    'savings' => '2 mois offerts',
                    'features' => [
                        'all_monthly_features',
                        'best_value',
                    ],
                ],
            ],
            'currentPlan' => $user ? $user->planName() : 'free',
            'isSubscribed' => $isPremium,
            'subscription' => $subscription,
            'stripeKey' => config('stripe.key'),
        ]);
    }

    /**
     * Create a checkout session for a new subscription.
     */
    public function checkout(Request $request)
    {
        $request->validate([
            'plan' => 'required|in:monthly,yearly',
        ]);

        $user = auth()->user();

        $priceId = $request->plan === 'monthly'
            ? config('stripe.price_monthly')
            : config('stripe.price_yearly');

        \Log::info('Subscription checkout attempt', [
            'plan' => $request->plan,
            'price_id' => $priceId,
            'user_id' => $user->id,
        ]);

        if (empty($priceId)) {
            \Log::warning('Stripe price ID not configured', ['plan' => $request->plan]);
            throw \Illuminate\Validation\ValidationException::withMessages([
                'plan' => 'Les abonnements ne sont pas encore configurés. Veuillez contacter le support.'
            ]);
        }

        try {
            if (!$user->stripe_id) {
                $user->createAsStripeCustomer();
            }

            $stripe = new \Stripe\StripeClient(config('stripe.secret'));

            $session = $stripe->checkout->sessions->create([
                'customer' => $user->stripe_id,
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price' => $priceId,
                    'quantity' => 1,
                ]],
                'mode' => 'subscription',
                'success_url' => route('subscription.success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('subscription.index'),
            ]);

            return Inertia::location($session->url);
        } catch (\Exception $e) {
            \Log::error('Subscription checkout error: ' . $e->getMessage(), [
                'exception' => get_class($e),
                'trace' => $e->getTraceAsString(),
            ]);

            throw \Illuminate\Validation\ValidationException::withMessages([
                'plan' => 'Erreur lors de la création de la session de paiement : ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Handle successful subscription.
     */
    public function success(Request $request)
    {
        $sessionId = $request->query('session_id');

        if ($sessionId) {
            $user = auth()->user();
            $stripe = new \Stripe\StripeClient(config('stripe.secret'));

            try {
                $session = $stripe->checkout->sessions->retrieve($sessionId);

                if ($session->payment_status === 'paid' && $session->subscription) {
                    $stripeSubscription = $stripe->subscriptions->retrieve($session->subscription);

                    $subscription = $user->subscriptions()
                        ->where('stripe_id', $session->subscription)
                        ->first();

                    if (!$subscription) {
                        $user->subscriptions()->create([
                            'type' => 'default',
                            'stripe_id' => $stripeSubscription->id,
                            'stripe_status' => $stripeSubscription->status,
                            'stripe_price' => $stripeSubscription->items->data[0]->price->id,
                            'quantity' => 1,
                            'trial_ends_at' => null,
                            'ends_at' => null,
                        ]);
                    }
                }
            } catch (\Exception $e) {
                \Log::error('Error processing subscription success: ' . $e->getMessage());
            }
        }

        return Inertia::render('Subscription/Success');
    }

    /**
     * Show the subscription management page.
     */
    public function manage()
    {
        $user = auth()->user();

        if (! $user->isPremium()) {
            return redirect()->route('subscription.index');
        }

        $subscription = $user->subscription('default');

        return Inertia::render('Subscription/Manage', [
            'subscription' => [
                'plan' => $user->planName(),
                'price_id' => $subscription->stripe_price,
                'status' => $subscription->stripe_status,
                'ends_at' => $subscription->ends_at,
                'trial_ends_at' => $subscription->trial_ends_at,
                'on_grace_period' => $subscription->onGracePeriod(),
                'canceled' => $subscription->canceled(),
            ],
        ]);
    }

    /**
     * Resume a cancelled subscription.
     */
    public function resume(Request $request)
    {
        $user = auth()->user();
        $subscription = $user->subscription('default');

        if ($subscription && $subscription->onGracePeriod()) {
            $subscription->resume();
            return redirect()->route('subscription.manage')->with('success', 'Votre abonnement a été réactivé.');
        }

        return redirect()->route('subscription.manage')->with('error', 'Impossible de réactiver l\'abonnement.');
    }

    /**
     * Cancel the subscription.
     */
    public function cancel(Request $request)
    {
        $user = auth()->user();
        $subscription = $user->subscription('default');

        if ($subscription && ! $subscription->canceled()) {
            $subscription->cancel();
            return redirect()->route('subscription.manage')->with('success', 'Votre abonnement sera annulé à la fin de la période en cours.');
        }

        return redirect()->route('subscription.manage')->with('error', 'Impossible d\'annuler l\'abonnement.');
    }

    /**
     * Update the payment method.
     */
    public function updatePaymentMethod(Request $request)
    {
        $user = auth()->user();

        try {
            return $user->updateDefaultPaymentMethod($request->payment_method);
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la mise à jour du moyen de paiement.');
        }
    }

    /**
     * Show the payment method update page.
     */
    public function paymentMethod()
    {
        $user = auth()->user();

        if (!$user->isPremium()) {
            return redirect()->route('subscription.index');
        }

        try {
            $stripe = new \Stripe\StripeClient(config('stripe.secret'));

            $session = $stripe->billingPortal->sessions->create([
                'customer' => $user->stripe_id,
                'return_url' => route('subscription.manage'),
            ]);

            return Inertia::location($session->url);
        } catch (\Exception $e) {
            \Log::error('Stripe billing portal error: ' . $e->getMessage());
            return redirect()->route('subscription.manage')->with('error', 'Erreur lors de la création de la session de paiement.');
        }
    }
}

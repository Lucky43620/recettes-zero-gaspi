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
            $checkoutResponse = $user->newSubscription('default', $priceId)
                ->checkout([
                    'success_url' => route('subscription.success') . '?session_id={CHECKOUT_SESSION_ID}',
                    'cancel_url' => route('subscription.index'),
                ]);

            $targetUrl = $checkoutResponse->getTargetUrl();
            \Log::info('Stripe checkout session created', ['url' => $targetUrl]);

            return Inertia::location($targetUrl);
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
        return Inertia::render('Subscription/Success');
    }

    /**
     * Show the subscription management page.
     */
    public function manage(): Response
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
                'cancelled' => $subscription->cancelled(),
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

        if ($subscription && ! $subscription->cancelled()) {
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
    public function paymentMethod(): Response
    {
        $user = auth()->user();

        return Inertia::render('Subscription/PaymentMethod', [
            'intent' => $user->createSetupIntent(),
            'stripeKey' => config('stripe.key'),
        ]);
    }
}

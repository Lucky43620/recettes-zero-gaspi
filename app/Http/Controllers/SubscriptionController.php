<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;
use Laravel\Cashier\Cashier;
use Laravel\Cashier\Exceptions\IncompletePayment;

class SubscriptionController extends Controller
{
    public function index(): Response
    {
        $user = auth()->user();

        return Inertia::render('Subscription/Index', [
            'plans' => $this->getPlans(),
            'currentPlan' => $user ? $user->planName() : 'free',
            'subscriptionStatus' => $user ? $user->subscriptionStatus() : 'inactive',
            'isSubscribed' => $user ? $user->isPremium() : false,
            'onTrial' => $user ? $user->isOnTrial() : false,
        ]);
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'plan' => 'required|in:monthly,yearly',
            'promotion_code' => 'nullable|string',
        ]);

        $user = $request->user();

        if ($user->subscribed('default')) {
            return redirect()->route('subscription.manage')
                ->with('error', 'Vous avez déjà un abonnement actif.');
        }

        $priceId = $request->plan === 'monthly'
            ? config('cashier.price_monthly')
            : config('cashier.price_yearly');

        if (empty($priceId)) {
            Log::warning('Stripe price ID not configured', ['plan' => $request->plan]);

            return redirect()->route('subscription.index')
                ->with('error', 'Configuration d\'abonnement non disponible.');
        }

        try {
            $checkoutBuilder = $user->newSubscription('default', $priceId)
                ->allowPromotionCodes();

            if ($request->filled('promotion_code')) {
                $checkoutBuilder->withPromotionCode($request->promotion_code);
            }

            $checkout = $checkoutBuilder->checkout([
                'success_url' => route('subscription.success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('subscription.index'),
            ]);

            Log::info('Subscription checkout created', [
                'user_id' => $user->id,
                'plan' => $request->plan,
                'price_id' => $priceId,
            ]);

            return Inertia::location($checkout->url());
        } catch (IncompletePayment $e) {
            Log::error('Incomplete payment on checkout', [
                'user_id' => $user->id,
                'plan' => $request->plan,
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('cashier.payment', [
                $e->payment->id,
                'redirect' => route('subscription.index'),
            ]);
        } catch (\Exception $e) {
            Log::error('Subscription checkout error', [
                'user_id' => $user->id,
                'plan' => $request->plan,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->route('subscription.index')
                ->with('error', 'Une erreur est survenue lors de la création de l\'abonnement.');
        }
    }

    public function success(Request $request)
    {
        $sessionId = $request->get('session_id');

        if (! $sessionId) {
            return redirect()->route('subscription.index');
        }

        try {
            $session = Cashier::stripe()->checkout->sessions->retrieve($sessionId);

            if ($session->payment_status !== 'paid') {
                return redirect()->route('subscription.index')
                    ->with('error', 'Le paiement n\'a pas été finalisé.');
            }

            $user = $request->user();

            Log::info('Subscription payment successful', [
                'user_id' => $user->id,
                'session_id' => $sessionId,
                'plan' => $user->planName(),
            ]);

            return Inertia::render('Subscription/Success', [
                'plan' => $user->planDisplayName(),
                'price' => $user->planPrice(),
            ]);
        } catch (\Exception $e) {
            Log::error('Subscription success error', [
                'session_id' => $sessionId,
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('subscription.index')
                ->with('error', 'Une erreur est survenue.');
        }
    }

    public function manage()
    {
        $user = auth()->user();

        if (! $user->subscribed('default')) {
            return redirect()->route('subscription.index')
                ->with('info', 'Vous n\'avez pas d\'abonnement actif.');
        }

        $subscription = $user->subscription('default');

        try {
            $stripeSubscription = $subscription->asStripeSubscription();

            return Inertia::render('Subscription/Manage', [
                'subscription' => [
                    'id' => $subscription->id,
                    'plan' => $user->planName(),
                    'plan_display' => $user->planDisplayName(),
                    'price' => $user->planPrice(),
                    'status' => $user->subscriptionStatus(),
                    'current_period_end' => $stripeSubscription->current_period_end,
                    'current_period_end_formatted' => date('d/m/Y', $stripeSubscription->current_period_end),
                    'cancel_at_period_end' => $subscription->onGracePeriod(),
                    'on_grace_period' => $subscription->onGracePeriod(),
                    'ends_at' => $subscription->ends_at?->format('d/m/Y'),
                    'trial_ends_at' => $subscription->trial_ends_at?->format('d/m/Y'),
                    'on_trial' => $subscription->onTrial(),
                    'recurring' => $subscription->recurring(),
                    'past_due' => $subscription->pastDue(),
                    'incomplete' => $subscription->incomplete(),
                ],
                'invoices' => $this->getInvoices($user),
                'payment_method' => $this->getPaymentMethod($user),
                'plans' => [
                    'monthly' => [
                        'price' => 4.99,
                        'display' => 'Premium Mensuel',
                    ],
                    'yearly' => [
                        'price' => 49.90,
                        'display' => 'Premium Annuel',
                        'savings' => 'Économisez 2 mois',
                    ],
                ],
                'canChangePlan' => ! $subscription->onGracePeriod() && $subscription->recurring(),
                'canCancel' => ! $subscription->onGracePeriod() && $subscription->active(),
                'canResume' => $subscription->onGracePeriod(),
            ]);
        } catch (\Exception $e) {
            Log::error('Error loading subscription management', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('subscription.index')
                ->with('error', 'Impossible de charger les informations de l\'abonnement.');
        }
    }

    public function swap(Request $request)
    {
        $request->validate([
            'plan' => 'required|in:monthly,yearly',
        ]);

        $user = $request->user();
        $subscription = $user->subscription('default');

        if (! $subscription || $subscription->onGracePeriod()) {
            return back()->with('error', 'Impossible de changer de plan.');
        }

        $priceId = $request->plan === 'monthly'
            ? config('cashier.price_monthly')
            : config('cashier.price_yearly');

        if ($subscription->stripe_price === $priceId) {
            return back()->with('info', 'Vous êtes déjà sur ce plan.');
        }

        try {
            $subscription->swap($priceId);

            Log::info('Subscription plan changed', [
                'user_id' => $user->id,
                'old_plan' => $user->planName(),
                'new_plan' => $request->plan,
                'price_id' => $priceId,
            ]);

            return back()->with('success', 'Votre abonnement a été modifié avec succès.');
        } catch (IncompletePayment $e) {
            Log::error('Incomplete payment on swap', [
                'user_id' => $user->id,
                'plan' => $request->plan,
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('cashier.payment', [
                $e->payment->id,
                'redirect' => route('subscription.manage'),
            ]);
        } catch (\Exception $e) {
            Log::error('Subscription swap error', [
                'user_id' => $user->id,
                'plan' => $request->plan,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Une erreur est survenue lors du changement d\'abonnement.');
        }
    }

    public function cancel(Request $request)
    {
        $user = $request->user();
        $subscription = $user->subscription('default');

        if (! $subscription || $subscription->onGracePeriod()) {
            return back()->with('error', 'Impossible d\'annuler l\'abonnement.');
        }

        try {
            $subscription->cancel();

            Log::info('Subscription canceled', [
                'user_id' => $user->id,
                'subscription_id' => $subscription->id,
                'ends_at' => $subscription->ends_at,
            ]);

            return back()->with('success', 'Votre abonnement a été annulé. Vous pourrez continuer à utiliser les fonctionnalités premium jusqu\'au ' . $subscription->ends_at->format('d/m/Y') . '.');
        } catch (\Exception $e) {
            Log::error('Subscription cancel error', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Une erreur est survenue lors de l\'annulation.');
        }
    }

    public function cancelNow(Request $request)
    {
        $user = $request->user();
        $subscription = $user->subscription('default');

        if (! $subscription) {
            return back()->with('error', 'Aucun abonnement actif trouvé.');
        }

        try {
            $subscription->cancelNow();

            Log::info('Subscription canceled immediately', [
                'user_id' => $user->id,
                'subscription_id' => $subscription->id,
            ]);

            return redirect()->route('subscription.index')
                ->with('success', 'Votre abonnement a été annulé immédiatement.');
        } catch (\Exception $e) {
            Log::error('Subscription cancel now error', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Une erreur est survenue lors de l\'annulation immédiate.');
        }
    }

    public function resume(Request $request)
    {
        $user = $request->user();
        $subscription = $user->subscription('default');

        if (! $subscription || ! $subscription->onGracePeriod()) {
            return back()->with('error', 'Impossible de réactiver l\'abonnement.');
        }

        try {
            $subscription->resume();

            Log::info('Subscription resumed', [
                'user_id' => $user->id,
                'subscription_id' => $subscription->id,
            ]);

            return back()->with('success', 'Votre abonnement a été réactivé avec succès.');
        } catch (\Exception $e) {
            Log::error('Subscription resume error', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Une erreur est survenue lors de la réactivation.');
        }
    }

    public function billingPortal(Request $request)
    {
        try {
            return $request->user()->redirectToBillingPortal(route('subscription.manage'));
        } catch (\Exception $e) {
            Log::error('Billing portal error', [
                'user_id' => $request->user()->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Une erreur est survenue.');
        }
    }

    public function downloadInvoice(Request $request, string $invoiceId)
    {
        try {
            return $request->user()->downloadInvoice($invoiceId, [
                'vendor' => config('app.name'),
                'product' => 'Abonnement Premium',
                'street' => '',
                'location' => '',
                'phone' => '',
                'email' => config('mail.from.address'),
                'url' => config('app.url'),
            ]);
        } catch (\Exception $e) {
            Log::error('Invoice download error', [
                'user_id' => $request->user()->id,
                'invoice_id' => $invoiceId,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Impossible de télécharger la facture.');
        }
    }

    protected function getPlans(): array
    {
        return [
            [
                'id' => 'free',
                'name' => 'Gratuit',
                'price' => 'Gratuit',
                'price_value' => 0,
                'interval' => null,
                'description' => 'Accès limité aux fonctionnalités de base',
                'features' => [
                    '10 recettes privées maximum',
                    '5 plannings de repas',
                    '3 listes de courses',
                    'Garde-manger basique',
                    'Publicités',
                ],
            ],
            [
                'id' => 'monthly',
                'name' => 'Premium Mensuel',
                'price' => '4,99€',
                'price_value' => 4.99,
                'price_id' => config('cashier.price_monthly'),
                'interval' => 'month',
                'billing' => 'par mois',
                'description' => 'Accès complet à toutes les fonctionnalités',
                'features' => [
                    'Recettes privées illimitées',
                    'Plannings de repas illimités',
                    'Listes de courses illimitées',
                    'Garde-manger avancé avec alertes',
                    'Sans publicité',
                    'Génération automatique de menus',
                    'Statistiques nutritionnelles',
                ],
                'popular' => false,
            ],
            [
                'id' => 'yearly',
                'name' => 'Premium Annuel',
                'price' => '49,90€',
                'price_value' => 49.90,
                'price_id' => config('cashier.price_yearly'),
                'interval' => 'year',
                'billing' => 'par an',
                'description' => 'Économisez 2 mois avec l\'abonnement annuel',
                'features' => [
                    'Recettes privées illimitées',
                    'Plannings de repas illimités',
                    'Listes de courses illimitées',
                    'Garde-manger avancé avec alertes',
                    'Sans publicité',
                    'Génération automatique de menus',
                    'Statistiques nutritionnelles',
                    '🎁 2 mois offerts',
                ],
                'popular' => true,
                'savings' => '16%',
            ],
        ];
    }

    protected function getInvoices(User $user): array
    {
        try {
            return $user->invoices()->map(function ($invoice) {
                return [
                    'id' => $invoice->id,
                    'date' => $invoice->date()->format('d/m/Y'),
                    'total' => $invoice->total(),
                    'status' => $invoice->status,
                    'download_url' => route('subscription.invoice', $invoice->id),
                ];
            })->toArray();
        } catch (\Exception $e) {
            Log::error('Error fetching invoices', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return [];
        }
    }

    protected function getPaymentMethod(User $user): ?array
    {
        try {
            if ($user->hasDefaultPaymentMethod()) {
                $paymentMethod = $user->defaultPaymentMethod();

                return [
                    'type' => $paymentMethod->type,
                    'brand' => $paymentMethod->card->brand ?? null,
                    'last_four' => $paymentMethod->card->last4 ?? null,
                    'exp_month' => $paymentMethod->card->exp_month ?? null,
                    'exp_year' => $paymentMethod->card->exp_year ?? null,
                ];
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Error fetching payment method', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\SubscriptionStatsService;
use App\Services\SettingsService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Laravel\Cashier\Subscription;

class AdminSubscriptionController extends Controller
{
    protected $statsService;
    protected $settings;

    public function __construct(SubscriptionStatsService $statsService, SettingsService $settings)
    {
        $this->statsService = $statsService;
        $this->settings = $settings;
    }

    public function index(Request $request)
    {
        $filter = $request->get('filter', 'all');

        $query = Subscription::with('user');

        switch ($filter) {
            case 'active':
                $query->where('stripe_status', 'active');
                break;
            case 'trialing':
                $query->where('stripe_status', 'trialing');
                break;
            case 'canceled':
                $query->whereNotNull('ends_at');
                break;
            case 'expiring':
                $query->whereNotNull('ends_at')
                    ->where('ends_at', '>=', now())
                    ->where('ends_at', '<=', now()->addDays(7));
                break;
        }

        $subscriptions = $query->latest()->paginate(20);

        $stats = [
            'mrr' => $this->statsService->getMRR(),
            'arr' => $this->statsService->getARR(),
            'active_count' => $this->statsService->getActiveSubscriptions(),
            'trial_count' => $this->statsService->getTrialSubscriptions(),
            'churn_rate' => $this->statsService->getChurnRate(),
            'new_subscribers' => $this->statsService->getNewSubscribers(),
            'ltv' => $this->statsService->getLTV(),
            'conversion_rate' => $this->statsService->getConversionRate(),
            'plan_distribution' => $this->statsService->getPlanDistribution(),
        ];

        $revenueChart = $this->statsService->getRevenueByMonth(12);
        $growthChart = $this->statsService->getSubscriberGrowth(12);

        return Inertia::render('Admin/Subscriptions/Index', [
            'subscriptions' => $subscriptions,
            'stats' => $stats,
            'revenueChart' => $revenueChart,
            'growthChart' => $growthChart,
            'currentFilter' => $filter,
        ]);
    }

    public function show(User $user)
    {
        $user->load(['subscriptions']);

        $subscriptionData = [
            'current_plan' => $user->planName(),
            'is_premium' => $user->isPremium(),
            'subscriptions' => $user->subscriptions()->get()->map(function ($sub) {
                return [
                    'id' => $sub->id,
                    'stripe_id' => $sub->stripe_id,
                    'stripe_status' => $sub->stripe_status,
                    'stripe_price' => $sub->stripe_price,
                    'trial_ends_at' => $sub->trial_ends_at,
                    'ends_at' => $sub->ends_at,
                    'created_at' => $sub->created_at,
                ];
            }),
        ];

        $invoices = [];
        try {
            if ($user->hasStripeId()) {
                $stripeInvoices = $user->invoices();
                if ($stripeInvoices) {
                    foreach ($stripeInvoices as $invoice) {
                        $invoices[] = [
                            'id' => $invoice->id,
                            'date' => $invoice->date()->toDateTimeString(),
                            'total' => $invoice->total() / 100,
                            'status' => $invoice->status,
                        ];
                    }
                }
            }
        } catch (\Exception $e) {
        }

        return Inertia::render('Admin/Subscriptions/Show', [
            'user' => $user,
            'subscription' => $subscriptionData,
            'invoices' => $invoices,
        ]);
    }

    public function cancel(User $user)
    {
        // Récupérer l'abonnement via le modèle Subscription directement
        $subscription = Subscription::where('type', 'default')
            ->where('user_id', $user->id)
            ->whereNull('ends_at')
            ->first();

        if ($subscription) {
            try {
                // Annuler via l'API Stripe directement
                $stripe = new \Stripe\StripeClient($this->settings->get('stripe_secret'));
                $stripe->subscriptions->update($subscription->stripe_id, [
                    'cancel_at_period_end' => true
                ]);

                // Mettre à jour l'abonnement local
                $subscription->cancel();

                \Log::info('Subscription cancelled by admin', [
                    'admin_id' => auth()->id(),
                    'user_id' => $user->id,
                    'subscription_id' => $subscription->id,
                    'stripe_id' => $subscription->stripe_id,
                ]);

                return redirect()->back()->with('success', 'Abonnement annulé avec succès');
            } catch (\Exception $e) {
                \Log::error('Admin cancel subscription error: ' . $e->getMessage());
                return redirect()->back()->with('error', 'Erreur lors de l\'annulation : ' . $e->getMessage());
            }
        }

        return redirect()->back()->with('error', 'Aucun abonnement actif trouvé');
    }

    public function resume(User $user)
    {
        if ($user->subscription('default')) {
            $user->subscription('default')->resume();

            return redirect()->back()->with('success', 'Abonnement réactivé avec succès');
        }

        return redirect()->back()->with('error', 'Aucun abonnement trouvé');
    }
}

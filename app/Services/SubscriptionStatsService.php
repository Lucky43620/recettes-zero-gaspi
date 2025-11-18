<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Laravel\Cashier\Subscription;

class SubscriptionStatsService
{
    public function getMRR(): float
    {
        return Cache::remember('subscription_mrr', 300, function () {
            $monthlyRevenue = Subscription::where('stripe_status', 'active')
                ->where('stripe_price', 'LIKE', '%monthly%')
                ->count() * 4.99;

            $yearlyRevenue = Subscription::where('stripe_status', 'active')
                ->where('stripe_price', 'LIKE', '%yearly%')
                ->count() * (49.90 / 12);

            return round($monthlyRevenue + $yearlyRevenue, 2);
        });
    }

    public function getARR(): float
    {
        return $this->getMRR() * 12;
    }

    public function getActiveSubscriptions(): int
    {
        return Cache::remember('subscription_active_count', 300, function () {
            return Subscription::where('stripe_status', 'active')->count();
        });
    }

    public function getTrialSubscriptions(): int
    {
        return Cache::remember('subscription_trial_count', 300, function () {
            return Subscription::where('stripe_status', 'trialing')->count();
        });
    }

    public function getChurnRate(int $days = 30): float
    {
        $startOfPeriod = now()->subDays($days);

        $startingSubscribers = Subscription::where('created_at', '<', $startOfPeriod)
            ->where(function ($query) use ($startOfPeriod) {
                $query->whereNull('ends_at')
                    ->orWhere('ends_at', '>=', $startOfPeriod);
            })
            ->count();

        if ($startingSubscribers === 0) {
            return 0;
        }

        $canceledInPeriod = Subscription::whereNotNull('ends_at')
            ->where('ends_at', '>=', $startOfPeriod)
            ->where('ends_at', '<=', now())
            ->count();

        return round(($canceledInPeriod / $startingSubscribers) * 100, 2);
    }

    public function getNewSubscribers(int $days = 30): int
    {
        return Subscription::where('created_at', '>=', now()->subDays($days))
            ->count();
    }

    public function getCancellations(int $days = 30): int
    {
        return Subscription::whereNotNull('ends_at')
            ->where('ends_at', '>=', now()->subDays($days))
            ->count();
    }

    public function getLTV(): float
    {
        return Cache::remember('subscription_ltv', 600, function () {
            $totalRevenue = 0;
            $totalCustomers = 0;

            $users = User::whereHas('subscriptions')->with('subscriptions')->get();

            foreach ($users as $user) {
                try {
                    $invoices = $user->invoices();
                    if ($invoices) {
                        foreach ($invoices as $invoice) {
                            $totalRevenue += $invoice->total();
                        }
                        $totalCustomers++;
                    }
                } catch (\Exception $e) {
                    continue;
                }
            }

            if ($totalCustomers === 0) {
                return 0;
            }

            return round(($totalRevenue / 100) / $totalCustomers, 2);
        });
    }

    public function getConversionRate(): float
    {
        $trials = Subscription::where('stripe_status', 'trialing')
            ->orWhere(function ($query) {
                $query->whereNotNull('trial_ends_at')
                    ->where('trial_ends_at', '<', now());
            })
            ->count();

        if ($trials === 0) {
            return 0;
        }

        $converted = Subscription::where('stripe_status', 'active')
            ->whereNotNull('trial_ends_at')
            ->count();

        return round(($converted / $trials) * 100, 2);
    }

    public function getPlanDistribution(): array
    {
        return Cache::remember('subscription_plan_distribution', 300, function () {
            $monthly = Subscription::where('stripe_status', 'active')
                ->where('stripe_price', 'LIKE', '%monthly%')
                ->count();

            $yearly = Subscription::where('stripe_status', 'active')
                ->where('stripe_price', 'LIKE', '%yearly%')
                ->count();

            return [
                'monthly' => $monthly,
                'yearly' => $yearly,
                'total' => $monthly + $yearly,
            ];
        });
    }

    public function getRevenueByMonth(int $months = 12): array
    {
        $data = [];

        for ($i = $months - 1; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthStart = $month->copy()->startOfMonth();
            $monthEnd = $month->copy()->endOfMonth();

            $monthlyRevenue = $this->calculateRevenueForPeriod($monthStart, $monthEnd);

            $data[] = [
                'month' => $month->format('M Y'),
                'revenue' => $monthlyRevenue,
            ];
        }

        return $data;
    }

    protected function calculateRevenueForPeriod($start, $end): float
    {
        $users = User::whereHas('subscriptions', function ($query) use ($start, $end) {
            $query->where('created_at', '<=', $end)
                ->where(function ($q) use ($start) {
                    $q->whereNull('ends_at')
                        ->orWhere('ends_at', '>=', $start);
                });
        })->get();

        $revenue = 0;

        foreach ($users as $user) {
            try {
                $invoices = $user->invoices();
                if ($invoices) {
                    foreach ($invoices as $invoice) {
                        $invoiceDate = $invoice->date();
                        if ($invoiceDate >= $start && $invoiceDate <= $end) {
                            $revenue += $invoice->total();
                        }
                    }
                }
            } catch (\Exception $e) {
                continue;
            }
        }

        return round($revenue / 100, 2);
    }

    public function getSubscriberGrowth(int $months = 12): array
    {
        $data = [];

        for ($i = $months - 1; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthEnd = $month->copy()->endOfMonth();

            $count = Subscription::where('created_at', '<=', $monthEnd)
                ->where(function ($query) use ($monthEnd) {
                    $query->whereNull('ends_at')
                        ->orWhere('ends_at', '>=', $monthEnd);
                })
                ->count();

            $data[] = [
                'month' => $month->format('M Y'),
                'subscribers' => $count,
            ];
        }

        return $data;
    }

    public function clearCache(): void
    {
        Cache::forget('subscription_mrr');
        Cache::forget('subscription_active_count');
        Cache::forget('subscription_trial_count');
        Cache::forget('subscription_ltv');
        Cache::forget('subscription_plan_distribution');
    }
}

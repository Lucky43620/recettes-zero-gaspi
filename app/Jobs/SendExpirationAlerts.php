<?php

namespace App\Jobs;

use App\Models\PantryItem;
use App\Models\User;
use App\Notifications\ExpirationAlertNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendExpirationAlerts implements ShouldQueue
{
    use Queueable;

    public function __construct()
    {
    }

    public function handle(): void
    {
        $expiringItems = PantryItem::whereNotNull('expiry_date')
            ->whereBetween('expiry_date', [now(), now()->addDays(3)])
            ->with('user')
            ->get();

        foreach ($expiringItems as $item) {
            if ($item->user) {
                $item->user->notify(new ExpirationAlertNotification($item));
            }
        }
    }
}

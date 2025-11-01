<?php

namespace App\Jobs;

use App\Models\User;
use App\Notifications\ExpiringItemsNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class SendExpiringItemsNotifications implements ShouldQueue
{
    use Queueable;

    public function handle(): void
    {
        Log::info('Starting expiring items notifications job');

        User::chunk(100, function ($users) {
            foreach ($users as $user) {
                $expiringItems = $user->pantryItems()
                    ->with(['ingredient', 'unit'])
                    ->expiringSoon()
                    ->get();

                if ($expiringItems->isNotEmpty()) {
                    $user->notify(new ExpiringItemsNotification($expiringItems));

                    Log::info("Sent expiring items notification to user {$user->id}", [
                        'user_id' => $user->id,
                        'items_count' => $expiringItems->count(),
                    ]);
                }
            }
        });

        Log::info('Finished expiring items notifications job');
    }
}

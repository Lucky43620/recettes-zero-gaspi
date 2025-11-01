<?php

namespace App\Console\Commands;

use App\Jobs\SendExpiringItemsNotifications;
use Illuminate\Console\Command;

class SendExpiringItemsNotificationsCommand extends Command
{
    protected $signature = 'pantry:notify-expiring-items';

    protected $description = 'Send notifications to users about items expiring soon in their pantry';

    public function handle(): int
    {
        $this->info('Dispatching expiring items notifications job...');

        SendExpiringItemsNotifications::dispatch();

        $this->info('Job dispatched successfully!');

        return Command::SUCCESS;
    }
}

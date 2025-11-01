<?php

use App\Jobs\SendMealPlanReminders;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('pantry:notify-expiring-items')
    ->daily()
    ->at('08:00')
    ->description('Send notifications for items expiring soon');

Schedule::job(new SendMealPlanReminders)
    ->everyThirtyMinutes()
    ->between('6:00', '20:00')
    ->description('Send meal plan reminders');

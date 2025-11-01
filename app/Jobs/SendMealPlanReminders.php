<?php

namespace App\Jobs;

use App\Models\MealPlanRecipe;
use App\Notifications\MealPlanReminderNotification;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendMealPlanReminders implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $today = Carbon::today();
        $mealTimes = [
            'breakfast' => '07:00',
            'lunch' => '11:30',
            'dinner' => '18:00',
            'snack' => '15:00'
        ];

        $mealPlanRecipes = MealPlanRecipe::whereDate('planned_date', $today)
            ->with(['recipe', 'mealPlan.user'])
            ->get();

        foreach ($mealPlanRecipes as $mealPlanRecipe) {
            $mealTime = $mealTimes[$mealPlanRecipe->meal_type] ?? '12:00';
            $reminderTime = Carbon::parse("$today $mealTime")->subHour();

            if (Carbon::now()->between($reminderTime, $reminderTime->copy()->addMinutes(30))) {
                $user = $mealPlanRecipe->mealPlan->user;
                $user->notify(new MealPlanReminderNotification($mealPlanRecipe));

                Log::info('Meal plan reminder sent', [
                    'user_id' => $user->id,
                    'recipe' => $mealPlanRecipe->recipe->title,
                    'meal_type' => $mealPlanRecipe->meal_type
                ]);
            }
        }
    }
}

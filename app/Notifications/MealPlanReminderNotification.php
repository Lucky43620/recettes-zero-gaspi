<?php

namespace App\Notifications;

use App\Models\MealPlanRecipe;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MealPlanReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public MealPlanRecipe $mealPlanRecipe
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $recipe = $this->mealPlanRecipe->recipe;
        $mealTypeLabels = [
            'breakfast' => 'petit-déjeuner',
            'lunch' => 'déjeuner',
            'dinner' => 'dîner',
            'snack' => 'collation'
        ];
        $mealType = $mealTypeLabels[$this->mealPlanRecipe->meal_type] ?? $this->mealPlanRecipe->meal_type;

        return (new MailMessage)
            ->subject('Rappel : ' . $recipe->title)
            ->greeting('Bonjour ' . $notifiable->name . ' !')
            ->line('N\'oubliez pas votre ' . $mealType . ' d\'aujourd\'hui :')
            ->line('🍽️ ' . $recipe->title)
            ->action('Voir la recette', route('recipes.show', $recipe->slug))
            ->line('Bon appétit !');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'meal_plan_reminder',
            'title' => 'Rappel de repas',
            'message' => 'N\'oubliez pas : ' . $this->mealPlanRecipe->recipe->title,
            'meal_plan_recipe_id' => $this->mealPlanRecipe->id,
            'recipe_id' => $this->mealPlanRecipe->recipe_id,
            'recipe_title' => $this->mealPlanRecipe->recipe->title,
            'recipe_slug' => $this->mealPlanRecipe->recipe->slug,
            'meal_type' => $this->mealPlanRecipe->meal_type,
            'planned_date' => $this->mealPlanRecipe->planned_date,
            'action_url' => route('recipes.show', $this->mealPlanRecipe->recipe->slug),
        ];
    }
}

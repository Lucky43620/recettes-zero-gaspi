<?php

namespace Database\Factories;

use App\Models\MealPlan;
use App\Models\ShoppingList;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ShoppingListFactory extends Factory
{
    protected $model = ShoppingList::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'meal_plan_id' => null,
            'name' => fake()->words(3, true),
        ];
    }
}

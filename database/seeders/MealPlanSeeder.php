<?php

namespace Database\Seeders;

use App\Models\MealPlan;
use App\Models\Recipe;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MealPlanSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        MealPlan::truncate();
        DB::table('meal_plan_recipes')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $users = User::all();
        $recipes = Recipe::where('is_public', true)->get();

        foreach ($users->random(15) as $user) {
            $mealPlan = MealPlan::create([
                'user_id' => $user->id,
                'name' => 'Planning de la semaine',
                'week_start_date' => now()->startOfWeek(),
                'created_at' => now()->subDays(rand(1, 30)),
            ]);

            $days = 7;
            $mealTypes = ['breakfast', 'lunch', 'dinner'];
            
            for ($day = 0; $day < $days; $day++) {
                foreach ($mealTypes as $mealType) {
                    if (rand(0, 2) > 0) {
                        $recipe = $recipes->random();
                        $mealPlan->recipes()->attach($recipe->id, [
                            'meal_type' => $mealType,
                            'planned_date' => now()->startOfWeek()->addDays($day),
                            'servings' => rand(2, 6),
                        ]);
                    }
                }
            }
        }
    }
}

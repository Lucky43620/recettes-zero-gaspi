<?php

namespace Database\Seeders;

use App\Models\Rating;
use App\Models\Recipe;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RatingSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Rating::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $users = User::all();
        $recipes = Recipe::where('is_public', true)->get();

        foreach ($recipes as $recipe) {
            $ratingCount = min(rand(3, 15), $users->count());
            $selectedUsers = $users->shuffle()->take($ratingCount);

            foreach ($selectedUsers as $user) {
                Rating::create([
                    'user_id' => $user->id,
                    'recipe_id' => $recipe->id,
                    'rating' => rand(3, 5),
                    'created_at' => now()->subDays(rand(1, 180)),
                ]);
            }
        }
    }
}

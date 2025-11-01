<?php

namespace Database\Seeders;

use App\Models\Recipe;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FavoriteSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('favorites')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $users = User::all();
        $recipes = Recipe::where('is_public', true)->get();

        foreach ($users as $user) {
            $favoriteCount = rand(5, 20);
            $favoriteRecipes = $recipes->random(min($favoriteCount, $recipes->count()));
            
            foreach ($favoriteRecipes as $recipe) {
                DB::table('favorites')->insert([
                    'user_id' => $user->id,
                    'recipe_id' => $recipe->id,
                    'created_at' => now()->subDays(rand(1, 365)),
                    'updated_at' => now()->subDays(rand(1, 365)),
                ]);
            }
        }
    }
}

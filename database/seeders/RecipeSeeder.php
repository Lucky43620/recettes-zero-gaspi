<?php

namespace Database\Seeders;

use App\Enums\Difficulty;
use App\Models\Ingredient;
use App\Models\Recipe;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RecipeSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Recipe::truncate();
        DB::table('recipe_steps')->truncate();
        DB::table('recipe_ingredients')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $users = User::all();
        
        // Generate random recipes
        Recipe::factory(60)->create();
    }
}

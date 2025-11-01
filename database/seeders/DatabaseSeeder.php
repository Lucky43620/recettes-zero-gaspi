<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            // Core data
            UserSeeder::class,
            IngredientSeeder::class,  // Also creates units
            RecipeSeeder::class,

            // Social features
            RatingSeeder::class,
            CommentSeeder::class,
            FollowSeeder::class,
            FavoriteSeeder::class,
            CollectionSeeder::class,

            // Planning features
            PantrySeeder::class,
            MealPlanSeeder::class,
            ShoppingListSeeder::class,
        ]);
    }
}

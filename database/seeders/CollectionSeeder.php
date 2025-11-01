<?php

namespace Database\Seeders;

use App\Models\Collection;
use App\Models\Recipe;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CollectionSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Collection::truncate();
        DB::table('collection_recipe')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $users = User::all();
        $recipes = Recipe::where('is_public', true)->get();

        $collectionNames = [
            'Mes recettes préférées', 'Cuisine rapide', 'Recettes du dimanche',
            'Anti-gaspi', 'Apéritifs', 'Desserts gourmands', 'Plats végétariens',
            'Cuisine du monde', 'Batch cooking', 'Recettes d\'été', 'Plats réconfortants'
        ];

        foreach ($users->random(15) as $user) {
            $collectionCount = rand(2, 4);
            for ($i = 0; $i < $collectionCount; $i++) {
                $collection = Collection::create([
                    'user_id' => $user->id,
                    'name' => $collectionNames[array_rand($collectionNames)],
                    'description' => 'Une sélection de mes meilleures recettes',
                    'is_public' => rand(0, 1) === 1,
                    'created_at' => now()->subDays(rand(1, 365)),
                ]);

                $recipeCount = rand(3, 10);
                $collectionRecipes = $recipes->random(min($recipeCount, $recipes->count()));
                
                foreach ($collectionRecipes as $index => $recipe) {
                    $collection->recipes()->attach($recipe->id, ['position' => $index + 1]);
                }
            }
        }
    }
}

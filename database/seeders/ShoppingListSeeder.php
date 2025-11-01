<?php

namespace Database\Seeders;

use App\Models\Ingredient;
use App\Models\ShoppingList;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ShoppingListSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        ShoppingList::truncate();
        DB::table('shopping_list_items')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $users = User::all();
        $ingredients = Ingredient::all();

        foreach ($users->random(18) as $user) {
            $shoppingList = ShoppingList::create([
                'user_id' => $user->id,
                'name' => 'Courses de la semaine',
                'created_at' => now()->subDays(rand(1, 14)),
            ]);

            $itemCount = rand(8, 25);
            $shoppingIngredients = $ingredients->random(min($itemCount, $ingredients->count()));
            
            foreach ($shoppingIngredients as $ingredient) {
                $shoppingList->items()->create([
                    'ingredient_id' => $ingredient->id,
                    'quantity' => rand(100, 1000),
                    'unit_code' => ['g', 'kg', 'ml', 'l', 'piece'][array_rand(['g', 'kg', 'ml', 'l', 'piece'])],
                    'is_checked' => rand(0, 3) > 0,
                ]);
            }
        }
    }
}

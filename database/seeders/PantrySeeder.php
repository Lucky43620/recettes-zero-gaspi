<?php

namespace Database\Seeders;

use App\Models\Ingredient;
use App\Models\PantryItem;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PantrySeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        PantryItem::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $users = User::all();
        $ingredients = Ingredient::all();

        foreach ($users->random(20) as $user) {
            $itemCount = rand(8, 20);
            $pantryIngredients = $ingredients->random(min($itemCount, $ingredients->count()));
            
            foreach ($pantryIngredients as $ingredient) {
                $daysUntilExpiration = rand(-5, 30);
                
                PantryItem::create([
                    'user_id' => $user->id,
                    'ingredient_id' => $ingredient->id,
                    'quantity' => rand(50, 1000),
                    'unit_code' => ['g', 'kg', 'ml', 'l', 'piece'][array_rand(['g', 'kg', 'ml', 'l', 'piece'])],
                    'expiration_date' => now()->addDays($daysUntilExpiration),
                    'storage_location' => ['Frigo', 'Placard', 'Congélateur', 'Cave'][array_rand(['Frigo', 'Placard', 'Congélateur', 'Cave'])],
                    'opened' => rand(0, 1) === 1,
                    'created_at' => now()->subDays(rand(1, 60)),
                ]);
            }
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\Recipe;
use App\Models\User;
use Illuminate\Database\Seeder;

class RecipeSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

        if ($users->isEmpty()) {
            $users = User::factory(5)->create();
        }

        foreach ($users as $user) {
            Recipe::factory(rand(2, 5))
                ->for($user, 'author')
                ->create()
                ->each(function ($recipe) {
                    for ($i = 1; $i <= rand(3, 8); $i++) {
                        $recipe->steps()->create([
                            'position' => $i,
                            'text' => fake()->paragraph(),
                            'timer_seconds' => rand(0, 1) ? rand(60, 600) : null,
                        ]);
                    }
                });
        }
    }
}

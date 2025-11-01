<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class RecipeFactory extends Factory
{
    public function definition(): array
    {
        $title = fake()->sentence(rand(3, 6));

        return [
            'author_id' => User::factory(),
            'title' => $title,
            'slug' => Str::slug($title) . '-' . Str::random(5),
            'summary' => fake()->paragraph(),
            'servings' => fake()->numberBetween(1, 8),
            'prep_minutes' => fake()->numberBetween(5, 60),
            'cook_minutes' => fake()->numberBetween(10, 120),
            'difficulty' => fake()->randomElement(['easy', 'medium', 'hard']),
            'cuisine' => fake()->randomElement(['Française', 'Italienne', 'Asiatique', 'Méditerranéenne', 'Végétarienne']),
            'is_public' => fake()->boolean(80),
            'calories' => fake()->numberBetween(100, 800),
            'rating_avg' => 0,
            'rating_count' => 0,
        ];
    }
}

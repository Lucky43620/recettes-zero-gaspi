<?php

namespace Database\Factories;

use App\Models\Ingredient;
use App\Models\PantryItem;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PantryItemFactory extends Factory
{
    protected $model = PantryItem::class;

    public function definition(): array
    {
        Unit::firstOrCreate(['code' => 'g'], ['label' => 'gramme']);

        return [
            'user_id' => User::factory(),
            'ingredient_id' => Ingredient::factory(),
            'quantity' => fake()->randomFloat(2, 50, 1000),
            'unit_code' => 'g',
            'expiration_date' => fake()->dateTimeBetween('now', '+30 days'),
            'storage_location' => fake()->randomElement(['Frigo', 'Congélateur', 'Placard', 'Cave']),
            'opened' => fake()->boolean(30),
        ];
    }

    public function expiringSoon(): static
    {
        return $this->state(fn (array $attributes) => [
            'expiration_date' => fake()->dateTimeBetween('now', '+3 days'),
        ]);
    }

    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'expiration_date' => fake()->dateTimeBetween('-7 days', 'now'),
        ]);
    }
}

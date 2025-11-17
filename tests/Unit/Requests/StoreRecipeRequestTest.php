<?php

namespace Tests\Unit\Requests;

use App\Enums\Difficulty;
use App\Http\Requests\StoreRecipeRequest;
use App\Models\Ingredient;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class StoreRecipeRequestTest extends TestCase
{
    use RefreshDatabase;

    private function makeRequest(array $data): \Illuminate\Validation\Validator
    {
        $request = new StoreRecipeRequest();
        return Validator::make($data, $request->rules());
    }

    public function test_valid_recipe_data_passes_validation(): void
    {
        $ingredient = Ingredient::factory()->create();
        $unit = Unit::firstOrCreate(['code' => 'g'], ['label' => 'gramme']);

        $data = [
            'title' => 'Test Recipe',
            'summary' => 'A delicious test recipe',
            'servings' => 4,
            'prep_minutes' => 15,
            'cook_minutes' => 30,
            'difficulty' => Difficulty::MEDIUM->value,
            'is_public' => true,
            'ingredients' => [
                [
                    'ingredient_id' => $ingredient->id,
                    'quantity' => 200,
                    'unit_code' => $unit->code,
                ]
            ],
            'steps' => [
                ['text' => 'First step'],
                ['text' => 'Second step'],
            ],
        ];

        $validator = $this->makeRequest($data);

        $this->assertFalse($validator->fails());
    }

    public function test_title_is_required(): void
    {
        $data = [
            'summary' => 'Test summary',
            'servings' => 4,
        ];

        $validator = $this->makeRequest($data);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('title', $validator->errors()->toArray());
    }

    public function test_title_must_be_string(): void
    {
        $data = [
            'title' => 12345,
            'summary' => 'Test summary',
            'servings' => 4,
        ];

        $validator = $this->makeRequest($data);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('title', $validator->errors()->toArray());
    }

    public function test_title_max_length_is_255(): void
    {
        $data = [
            'title' => str_repeat('a', 256),
            'summary' => 'Test summary',
            'servings' => 4,
        ];

        $validator = $this->makeRequest($data);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('title', $validator->errors()->toArray());
    }

    public function test_servings_is_required(): void
    {
        $data = [
            'title' => 'Test Recipe',
            'summary' => 'Test summary',
        ];

        $validator = $this->makeRequest($data);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('servings', $validator->errors()->toArray());
    }

    public function test_servings_must_be_integer(): void
    {
        $data = [
            'title' => 'Test Recipe',
            'summary' => 'Test summary',
            'servings' => 'not-a-number',
        ];

        $validator = $this->makeRequest($data);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('servings', $validator->errors()->toArray());
    }

    public function test_servings_must_be_at_least_one(): void
    {
        $data = [
            'title' => 'Test Recipe',
            'summary' => 'Test summary',
            'servings' => 0,
        ];

        $validator = $this->makeRequest($data);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('servings', $validator->errors()->toArray());
    }

    public function test_prep_minutes_must_be_integer(): void
    {
        $data = [
            'title' => 'Test Recipe',
            'summary' => 'Test summary',
            'servings' => 4,
            'prep_minutes' => 'not-a-number',
        ];

        $validator = $this->makeRequest($data);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('prep_minutes', $validator->errors()->toArray());
    }

    public function test_prep_minutes_must_be_at_least_zero(): void
    {
        $data = [
            'title' => 'Test Recipe',
            'summary' => 'Test summary',
            'servings' => 4,
            'prep_minutes' => -5,
        ];

        $validator = $this->makeRequest($data);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('prep_minutes', $validator->errors()->toArray());
    }

    public function test_cook_minutes_must_be_integer(): void
    {
        $data = [
            'title' => 'Test Recipe',
            'summary' => 'Test summary',
            'servings' => 4,
            'cook_minutes' => 'not-a-number',
        ];

        $validator = $this->makeRequest($data);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('cook_minutes', $validator->errors()->toArray());
    }

    public function test_difficulty_must_be_valid_enum_value(): void
    {
        $data = [
            'title' => 'Test Recipe',
            'summary' => 'Test summary',
            'servings' => 4,
            'difficulty' => 'invalid-difficulty',
        ];

        $validator = $this->makeRequest($data);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('difficulty', $validator->errors()->toArray());
    }

    public function test_ingredients_must_be_array(): void
    {
        $data = [
            'title' => 'Test Recipe',
            'summary' => 'Test summary',
            'servings' => 4,
            'ingredients' => 'not-an-array',
        ];

        $validator = $this->makeRequest($data);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('ingredients', $validator->errors()->toArray());
    }

    public function test_ingredient_id_must_exist_in_database(): void
    {
        $data = [
            'title' => 'Test Recipe',
            'summary' => 'Test summary',
            'servings' => 4,
            'ingredients' => [
                [
                    'ingredient_id' => 99999, // Non-existent
                    'quantity' => 100,
                    'unit_code' => 'g',
                ]
            ],
        ];

        $validator = $this->makeRequest($data);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('ingredients.0.ingredient_id', $validator->errors()->toArray());
    }

    public function test_ingredient_quantity_must_be_numeric(): void
    {
        $ingredient = Ingredient::factory()->create();

        $data = [
            'title' => 'Test Recipe',
            'summary' => 'Test summary',
            'servings' => 4,
            'ingredients' => [
                [
                    'ingredient_id' => $ingredient->id,
                    'quantity' => 'not-numeric',
                    'unit_code' => 'g',
                ]
            ],
        ];

        $validator = $this->makeRequest($data);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('ingredients.0.quantity', $validator->errors()->toArray());
    }

    public function test_ingredient_quantity_must_be_positive(): void
    {
        $ingredient = Ingredient::factory()->create();

        $data = [
            'title' => 'Test Recipe',
            'summary' => 'Test summary',
            'servings' => 4,
            'ingredients' => [
                [
                    'ingredient_id' => $ingredient->id,
                    'quantity' => -5,
                    'unit_code' => 'g',
                ]
            ],
        ];

        $validator = $this->makeRequest($data);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('ingredients.0.quantity', $validator->errors()->toArray());
    }

    public function test_steps_must_be_array(): void
    {
        $data = [
            'title' => 'Test Recipe',
            'summary' => 'Test summary',
            'servings' => 4,
            'steps' => 'not-an-array',
        ];

        $validator = $this->makeRequest($data);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('steps', $validator->errors()->toArray());
    }

    public function test_step_text_is_required(): void
    {
        $data = [
            'title' => 'Test Recipe',
            'summary' => 'Test summary',
            'servings' => 4,
            'steps' => [
                ['text' => ''],
            ],
        ];

        $validator = $this->makeRequest($data);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('steps.0.text', $validator->errors()->toArray());
    }

    public function test_step_text_must_be_string(): void
    {
        $data = [
            'title' => 'Test Recipe',
            'summary' => 'Test summary',
            'servings' => 4,
            'steps' => [
                ['text' => 12345],
            ],
        ];

        $validator = $this->makeRequest($data);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('steps.0.text', $validator->errors()->toArray());
    }

    public function test_is_public_must_be_boolean(): void
    {
        $data = [
            'title' => 'Test Recipe',
            'summary' => 'Test summary',
            'servings' => 4,
            'is_public' => 'not-boolean',
        ];

        $validator = $this->makeRequest($data);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('is_public', $validator->errors()->toArray());
    }
}

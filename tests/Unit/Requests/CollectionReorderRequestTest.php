<?php

namespace Tests\Unit\Requests;

use App\Http\Requests\CollectionReorderRequest;
use App\Models\Recipe;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class CollectionReorderRequestTest extends TestCase
{
    use RefreshDatabase;

    private function makeRequest(array $data): \Illuminate\Validation\Validator
    {
        $request = new CollectionReorderRequest();
        return Validator::make($data, $request->rules());
    }

    public function test_valid_recipe_ids_passes_validation(): void
    {
        $recipe1 = Recipe::factory()->create();
        $recipe2 = Recipe::factory()->create();

        $data = [
            'recipe_ids' => [$recipe1->id, $recipe2->id],
        ];

        $validator = $this->makeRequest($data);

        $this->assertFalse($validator->fails());
    }

    public function test_recipe_ids_is_required(): void
    {
        $data = [];

        $validator = $this->makeRequest($data);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('recipe_ids', $validator->errors()->toArray());
    }

    public function test_recipe_ids_must_be_array(): void
    {
        $data = [
            'recipe_ids' => 'not-an-array',
        ];

        $validator = $this->makeRequest($data);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('recipe_ids', $validator->errors()->toArray());
    }

    public function test_each_recipe_id_is_required(): void
    {
        $data = [
            'recipe_ids' => [null],
        ];

        $validator = $this->makeRequest($data);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('recipe_ids.0', $validator->errors()->toArray());
    }

    public function test_each_recipe_id_must_exist_in_database(): void
    {
        $recipe = Recipe::factory()->create();

        $data = [
            'recipe_ids' => [$recipe->id, 99999], // 99999 doesn't exist
        ];

        $validator = $this->makeRequest($data);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('recipe_ids.1', $validator->errors()->toArray());
    }

    public function test_get_recipe_ids_returns_array(): void
    {
        $recipe1 = Recipe::factory()->create();
        $recipe2 = Recipe::factory()->create();

        $request = new CollectionReorderRequest();
        $request->merge([
            'recipe_ids' => [$recipe1->id, $recipe2->id],
        ]);

        $result = $request->getRecipeIds();

        $this->assertIsArray($result);
        $this->assertEquals([$recipe1->id, $recipe2->id], $result);
    }

    public function test_get_recipe_ids_returns_empty_array_when_not_set(): void
    {
        $request = new CollectionReorderRequest();

        $result = $request->getRecipeIds();

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function test_authorize_returns_true(): void
    {
        $request = new CollectionReorderRequest();

        $this->assertTrue($request->authorize());
    }
}

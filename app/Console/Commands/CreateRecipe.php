<?php

namespace App\Console\Commands;

use App\Enums\Difficulty;
use App\Models\Ingredient;
use App\Models\Recipe;
use App\Models\RecipeStep;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class CreateRecipe extends Command
{
    protected $signature = 'recipe:create {email} {data}';

    protected $description = 'Créer une nouvelle recette';

    public function handle()
    {
        $email = $this->argument('email');
        $data = json_decode($this->argument('data'), true);

        if (!$data) {
            $this->error('Données JSON invalides');
            return 1;
        }

        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("Utilisateur non trouvé : {$email}");
            return 1;
        }

        DB::beginTransaction();

        try {
            $difficulty = isset($data['difficulty']) ? Difficulty::from($data['difficulty']) : null;

            $recipe = Recipe::create([
                'author_id' => $user->id,
                'title' => $data['title'],
                'slug' => Str::slug($data['title']),
                'summary' => $data['summary'] ?? null,
                'servings' => $data['servings'] ?? 4,
                'prep_minutes' => $data['prep_minutes'] ?? null,
                'cook_minutes' => $data['cook_minutes'] ?? null,
                'difficulty' => $difficulty,
                'cuisine' => $data['cuisine'] ?? null,
                'is_public' => $data['is_public'] ?? true,
                'calories' => $data['calories'] ?? null,
                'nutrients' => $data['nutrients'] ?? null,
            ]);

            if (isset($data['steps'])) {
                foreach ($data['steps'] as $step) {
                    RecipeStep::create([
                        'recipe_id' => $recipe->id,
                        'position' => $step['position'],
                        'text' => $step['text'],
                        'timer_minutes' => $step['timer_minutes'] ?? null,
                    ]);
                }
            }

            if (isset($data['ingredients'])) {
                foreach ($data['ingredients'] as $ingredientData) {
                    $ingredient = Ingredient::firstOrCreate(
                        ['name' => $ingredientData['name']],
                        []
                    );

                    if (isset($ingredientData['unit'])) {
                        Unit::firstOrCreate(
                            ['code' => $ingredientData['unit']],
                            ['label' => ucfirst($ingredientData['unit'])]
                        );
                    }

                    $recipe->ingredients()->attach($ingredient->id, [
                        'quantity' => $ingredientData['quantity'] ?? null,
                        'unit_code' => $ingredientData['unit'] ?? null,
                        'position' => $ingredientData['position'],
                    ]);
                }
            }

            if (isset($data['image_url'])) {
                try {
                    $response = Http::timeout(30)->get($data['image_url']);

                    if ($response->successful()) {
                        $extension = pathinfo(parse_url($data['image_url'], PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';
                        $filename = Str::random(40) . '.' . $extension;

                        $recipe->addMediaFromString($response->body())
                            ->usingFileName($filename)
                            ->toMediaCollection('images');
                    }
                } catch (\Exception $e) {
                }
            }

            DB::commit();

            $this->info("Recette créée avec succès : {$recipe->title} (ID: {$recipe->id})");
            return 0;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Erreur lors de la création : " . $e->getMessage());
            return 1;
        }
    }
}

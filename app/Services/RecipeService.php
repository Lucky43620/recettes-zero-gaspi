<?php

namespace App\Services;

use App\Models\Recipe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RecipeService
{
    public function createRecipe(array $validated): Recipe
    {
        $recipe = Auth::user()->recipes()->create([
            'title' => $validated['title'],
            'summary' => $validated['summary'] ?? null,
            'servings' => $validated['servings'],
            'prep_minutes' => $validated['prep_minutes'] ?? null,
            'cook_minutes' => $validated['cook_minutes'] ?? null,
            'difficulty' => $validated['difficulty'] ?? null,
            'cuisine' => $validated['cuisine'] ?? null,
            'is_public' => $validated['is_public'] ?? true,
            'calories' => $validated['calories'] ?? null,
            'nutrients' => $validated['nutrients'] ?? null,
        ]);

        $this->syncSteps($recipe, $validated['steps']);

        if (!empty($validated['images'])) {
            $this->syncImages($recipe, $validated['images']);
        }

        return $recipe;
    }

    public function updateRecipe(Recipe $recipe, array $validated): Recipe
    {
        $recipe->update([
            'title' => $validated['title'],
            'summary' => $validated['summary'] ?? null,
            'servings' => $validated['servings'],
            'prep_minutes' => $validated['prep_minutes'] ?? null,
            'cook_minutes' => $validated['cook_minutes'] ?? null,
            'difficulty' => $validated['difficulty'] ?? null,
            'cuisine' => $validated['cuisine'] ?? null,
            'is_public' => $validated['is_public'] ?? true,
            'calories' => $validated['calories'] ?? null,
            'nutrients' => $validated['nutrients'] ?? null,
        ]);

        $this->syncSteps($recipe, $validated['steps']);

        if (!empty($validated['images'])) {
            $this->syncImages($recipe, $validated['images']);
        }

        return $recipe;
    }

    public function applyFilters($query, Request $request)
    {
        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                    ->orWhere('summary', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->has('difficulty')) {
            $query->where('difficulty', $request->difficulty);
        }

        if ($request->has('sort')) {
            match ($request->sort) {
                'rating' => $query->orderBy('rating_avg', 'desc'),
                'duration' => $query->orderByRaw('COALESCE(prep_minutes, 0) + COALESCE(cook_minutes, 0) ASC'),
                default => $query->latest(),
            };
        } else {
            $query->latest();
        }

        return $query;
    }

    private function syncSteps(Recipe $recipe, array $steps): void
    {
        $recipe->steps()->delete();

        foreach ($steps as $index => $step) {
            $recipe->steps()->create([
                'position' => $index + 1,
                'text' => $step['text'],
                'timer_minutes' => $step['timer_minutes'] ?? null,
            ]);
        }
    }

    private function syncImages(Recipe $recipe, array $images): void
    {
        $recipe->clearMediaCollection('images');

        foreach ($images as $image) {
            $recipe->addMedia($image)->toMediaCollection('images');
        }
    }
}

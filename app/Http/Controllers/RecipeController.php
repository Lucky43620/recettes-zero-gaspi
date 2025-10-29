<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use App\Models\Unit;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class RecipeController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $query = Recipe::with('author')
            ->where('is_public', true)
            ->latest();

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
        }

        $recipes = $query->paginate(12);

        return Inertia::render('Recipe/Index', [
            'recipes' => $recipes,
            'filters' => $request->only(['search', 'difficulty', 'sort']),
        ]);
    }

    public function create()
    {
        $units = Unit::all();

        return Inertia::render('Recipe/Create', [
            'units' => $units,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'summary' => 'nullable|string',
            'servings' => 'required|integer|min:1',
            'prep_minutes' => 'nullable|integer|min:0',
            'cook_minutes' => 'nullable|integer|min:0',
            'difficulty' => 'nullable|in:easy,medium,hard',
            'cuisine' => 'nullable|string|max:100',
            'is_public' => 'boolean',
            'calories' => 'nullable|integer|min:0',
            'nutrients' => 'nullable|array',
            'steps' => 'required|array|min:1',
            'steps.*.text' => 'required|string',
            'steps.*.timer_seconds' => 'nullable|integer|min:0',
            'images' => 'nullable|array',
            'images.*' => 'image|max:10240',
        ]);

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

        foreach ($validated['steps'] as $index => $step) {
            $recipe->steps()->create([
                'position' => $index + 1,
                'text' => $step['text'],
                'timer_seconds' => $step['timer_seconds'] ?? null,
            ]);
        }

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $recipe->addMedia($image)->toMediaCollection('images');
            }
        }

        return redirect()->route('recipes.show', $recipe->slug)
            ->with('success', 'Recette créée avec succès');
    }

    public function show(Recipe $recipe)
    {
        if (!$recipe->is_public && $recipe->author_id !== Auth::id()) {
            abort(403);
        }

        $recipe->load(['author', 'steps', 'media']);

        return Inertia::render('Recipe/Show', [
            'recipe' => $recipe,
            'canEdit' => Auth::user()?->can('update', $recipe),
            'canDelete' => Auth::user()?->can('delete', $recipe),
        ]);
    }

    public function edit(Recipe $recipe)
    {
        $this->authorize('update', $recipe);

        $recipe->load(['steps', 'media']);
        $units = Unit::all();

        return Inertia::render('Recipe/Edit', [
            'recipe' => $recipe,
            'units' => $units,
        ]);
    }

    public function update(Request $request, Recipe $recipe)
    {
        $this->authorize('update', $recipe);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'summary' => 'nullable|string',
            'servings' => 'required|integer|min:1',
            'prep_minutes' => 'nullable|integer|min:0',
            'cook_minutes' => 'nullable|integer|min:0',
            'difficulty' => 'nullable|in:easy,medium,hard',
            'cuisine' => 'nullable|string|max:100',
            'is_public' => 'boolean',
            'calories' => 'nullable|integer|min:0',
            'nutrients' => 'nullable|array',
            'steps' => 'required|array|min:1',
            'steps.*.text' => 'required|string',
            'steps.*.timer_seconds' => 'nullable|integer|min:0',
            'images' => 'nullable|array',
            'images.*' => 'image|max:10240',
        ]);

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

        $recipe->steps()->delete();
        foreach ($validated['steps'] as $index => $step) {
            $recipe->steps()->create([
                'position' => $index + 1,
                'text' => $step['text'],
                'timer_seconds' => $step['timer_seconds'] ?? null,
            ]);
        }

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $recipe->addMedia($image)->toMediaCollection('images');
            }
        }

        return redirect()->route('recipes.show', $recipe->slug)
            ->with('success', 'Recette mise à jour avec succès');
    }

    public function destroy(Recipe $recipe)
    {
        $this->authorize('delete', $recipe);

        $recipe->delete();

        return redirect()->route('recipes.index')
            ->with('success', 'Recette supprimée avec succès');
    }
}

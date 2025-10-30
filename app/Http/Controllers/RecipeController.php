<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRecipeRequest;
use App\Http\Requests\UpdateRecipeRequest;
use App\Models\Recipe;
use App\Models\Unit;
use App\Services\RecipeService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class RecipeController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private RecipeService $recipeService
    ) {}

    public function index(Request $request)
    {
        $query = Recipe::with(['author', 'media'])
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

    public function store(StoreRecipeRequest $request)
    {
        $recipe = $this->recipeService->createRecipe($request->validated());

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

    public function update(UpdateRecipeRequest $request, Recipe $recipe)
    {
        $this->recipeService->updateRecipe($recipe, $request->validated());

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

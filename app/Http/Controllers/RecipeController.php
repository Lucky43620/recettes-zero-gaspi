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
            ->where('is_public', true);

        $query = $this->recipeService->applyFilters($query, $request);
        $recipes = $query->paginate(12);

        return Inertia::render('Recipe/Index', [
            'recipes' => $recipes,
            'filters' => $request->only(['search', 'difficulty', 'sort']),
        ]);
    }

    public function myRecipes(Request $request)
    {
        $query = Recipe::with(['author', 'media'])
            ->where('author_id', Auth::id());

        if ($request->has('visibility')) {
            $query->where('is_public', $request->visibility === 'public');
        }

        $query = $this->recipeService->applyFilters($query, $request);
        $recipes = $query->paginate(12);

        return Inertia::render('Recipe/MyRecipes', [
            'recipes' => $recipes,
            'filters' => $request->only(['search', 'difficulty', 'sort', 'visibility']),
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

        return redirect()->route('recipes.show', ['recipe' => $recipe->slug, 'from' => 'my'])
            ->with('success', 'Recette créée avec succès');
    }

    public function show(Request $request, Recipe $recipe)
    {
        if (!$recipe->is_public && $recipe->author_id !== Auth::id()) {
            abort(403);
        }

        $recipe->load([
            'author',
            'steps',
            'ingredients.unit',
            'media',
            'ratings.user',
            'comments' => function ($query) {
                $query->with(['user', 'replies.user'])->orderBy('upvotes', 'desc')->orderBy('created_at', 'desc');
            },
        ]);

        $userRating = null;
        $isFavorited = false;
        $commentVotes = [];

        if (Auth::check()) {
            $userRating = $recipe->ratings()->where('user_id', Auth::id())->first();
            $isFavorited = Auth::user()->hasFavorited($recipe);
            $commentVotes = \App\Models\CommentVote::where('user_id', Auth::id())
                ->whereIn('comment_id', $recipe->comments->pluck('id'))
                ->pluck('vote', 'comment_id')
                ->toArray();
        }

        $isOwner = Auth::id() === $recipe->author_id;
        $fromMyRecipes = $request->query('from') === 'my';

        return Inertia::render('Recipe/Show', [
            'recipe' => $recipe,
            'userRating' => $userRating,
            'isFavorited' => $isFavorited,
            'commentVotes' => $commentVotes,
            'canEdit' => Auth::user()?->can('update', $recipe),
            'canDelete' => Auth::user()?->can('delete', $recipe),
            'usePrivateLayout' => $isOwner && $fromMyRecipes,
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

        return redirect()->route('recipes.show', ['recipe' => $recipe->slug, 'from' => 'my'])
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

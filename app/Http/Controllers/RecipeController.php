<?php

namespace App\Http\Controllers;

use App\Http\Requests\RecipeFilterRequest;
use App\Http\Requests\StoreRecipeRequest;
use App\Http\Requests\UpdateRecipeRequest;
use App\Models\Recipe;
use App\Models\Unit;
use App\Services\RecipeService;
use App\Services\UnitService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class RecipeController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private RecipeService $recipeService,
        private UnitService $unitService
    ) {}

    public function index(RecipeFilterRequest $request)
    {
        $query = Recipe::with(['author', 'media'])
            ->where('is_public', true);

        $query = $this->recipeService->applyFilters($query, $request);
        $recipes = $query->paginate(12);

        return Inertia::render('Recipe/Index', [
            'recipes' => $recipes,
            'filters' => $request->filters(),
        ]);
    }

    public function myRecipes(RecipeFilterRequest $request)
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
            'filters' => $request->filters(),
        ]);
    }

    public function create()
    {
        $units = $this->unitService->getAllUnits();

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

        $loadRelations = [
            'author',
            'steps' => function ($query) {
                $query->orderBy('position');
            },
            'ingredients',
            'media',
            'ratings.user',
            'comments' => function ($query) {
                $query->with(['user', 'replies.user'])->orderBy('upvotes', 'desc')->orderBy('created_at', 'desc');
            },
            'cooksnaps.user',
        ];

        if (Auth::check()) {
            $loadRelations['ratings'] = fn($query) => $query->where('user_id', Auth::id());
            $loadRelations['comments.votes'] = fn($query) => $query->where('user_id', Auth::id());
        }

        $recipe->load($loadRelations);

        if (Auth::check()) {
            $recipe->loadExists(['favorites' => fn($query) => $query->where('user_id', Auth::id())]);
        }

        $userRating = null;
        $isFavorited = false;
        $commentVotes = [];

        if (Auth::check()) {
            $userRating = $recipe->ratings->where('user_id', Auth::id())->first();
            $isFavorited = $recipe->favorites_exists ?? false;

            $commentVotes = $recipe->comments->flatMap(function ($comment) {
                return $comment->votes;
            })->pluck('vote_type', 'comment_id')->toArray();
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

    public function cook(Recipe $recipe)
    {
        if (!$recipe->is_public && $recipe->author_id !== Auth::id()) {
            abort(403);
        }

        $recipe->load(['steps' => function ($query) {
            $query->orderBy('position');
        }, 'author']);

        if ($recipe->steps->isEmpty()) {
            return redirect()->route('recipes.show', $recipe->slug)
                ->with('error', 'Cette recette n\'a pas d\'étapes définies.');
        }

        return Inertia::render('Recipe/Cook', [
            'recipe' => [
                'id' => $recipe->id,
                'slug' => $recipe->slug,
                'title' => $recipe->title,
                'author' => [
                    'id' => $recipe->author->id,
                    'name' => $recipe->author->name,
                    'profile_photo_url' => $recipe->author->profile_photo_url,
                ],
                'steps' => $recipe->steps->map(function ($step) {
                    return [
                        'id' => $step->id,
                        'position' => $step->position,
                        'text' => $step->text,
                        'timer_minutes' => $step->timer_minutes,
                    ];
                })->toArray(),
            ],
        ]);
    }

    public function edit(Recipe $recipe)
    {
        $this->authorize('update', $recipe);

        $recipe->load(['steps', 'ingredients', 'media']);
        $units = $this->unitService->getAllUnits();

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

        return redirect()->to('/my-recipes')
            ->with('success', 'Recette supprimée avec succès');
    }
}

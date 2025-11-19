<?php

namespace App\Http\Controllers;

use App\Http\Requests\CollectionReorderRequest;
use App\Http\Requests\StoreCollectionRequest;
use App\Models\Collection;
use App\Models\Recipe;
use App\Services\CollectionService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class CollectionController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private CollectionService $collectionService
    ) {}

    public function index()
    {
        $collections = $this->collectionService->getUserCollections(Auth::user(), 100);

        return Inertia::render('Collection/Index', [
            'collections' => $collections,
        ]);
    }

    public function public()
    {
        $collections = Collection::with(['user', 'recipes'])
            ->where('is_public', true)
            ->withCount('recipes')
            ->latest()
            ->paginate(12);

        return Inertia::render('Collection/Public', [
            'collections' => $collections,
        ]);
    }

    public function show(Collection $collection)
    {
        $this->authorize('view', $collection);

        $collection = $this->collectionService->getCollectionWithRecipes($collection);

        // Charger toutes les recettes publiques pour le modal
        $userRecipes = Recipe::where('is_public', true)
            ->with(['media', 'author'])
            ->latest()
            ->get();

        return Inertia::render('Collection/Show', [
            'collection' => $collection,
            'canEdit' => Auth::user()?->can('update', $collection),
            'userRecipes' => $userRecipes,
        ]);
    }

    public function store(StoreCollectionRequest $request)
    {
        $collection = $this->collectionService->createCollection(Auth::user(), $request->validated());

        return redirect()->route('collections.show', $collection)
            ->with('success', 'Collection créée');
    }

    public function update(StoreCollectionRequest $request, Collection $collection)
    {
        $this->authorize('update', $collection);

        $this->collectionService->updateCollection($collection, $request->validated());

        return back()->with('success', 'Collection mise à jour');
    }

    public function destroy(Collection $collection)
    {
        $this->authorize('delete', $collection);

        $this->collectionService->deleteCollection($collection);

        return redirect()->route('collections.index')
            ->with('success', 'Collection supprimée');
    }

    public function addRecipe(Collection $collection, Recipe $recipe)
    {
        $this->authorize('update', $collection);

        try {
            $this->collectionService->addRecipe($collection, $recipe);
            return back()->with('success', 'Recette ajoutée à la collection');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function removeRecipe(Collection $collection, Recipe $recipe)
    {
        $this->authorize('update', $collection);

        $this->collectionService->removeRecipe($collection, $recipe);

        return back()->with('success', 'Recette retirée de la collection');
    }

    public function addMultipleRecipes(Request $request, Collection $collection)
    {
        $this->authorize('update', $collection);

        $validated = $request->validate([
            'recipe_ids' => 'required|array|min:1',
            'recipe_ids.*' => 'required|integer|exists:recipes,id',
        ]);

        $addedCount = 0;
        foreach ($validated['recipe_ids'] as $recipeId) {
            try {
                $recipe = Recipe::findOrFail($recipeId);
                $this->collectionService->addRecipe($collection, $recipe);
                $addedCount++;
            } catch (\Exception $e) {
                // Skip duplicates or errors, continue with the next recipe
                continue;
            }
        }

        $message = $addedCount === 1
            ? 'Recette ajoutée à la collection'
            : "{$addedCount} recettes ajoutées à la collection";

        return back()->with('success', $message);
    }

    public function reorder(CollectionReorderRequest $request, Collection $collection)
    {
        $this->authorize('update', $collection);

        $this->collectionService->reorderRecipes($collection, $request->getRecipeIds());

        return back()->with('success', 'Ordre mis à jour');
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\CollectionReorderRequest;
use App\Http\Requests\StoreCollectionRequest;
use App\Models\Collection;
use App\Models\Recipe;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class CollectionController extends Controller
{
    use AuthorizesRequests;
    public function index()
    {
        $collections = Auth::user()->collections()
            ->withCount('recipes')
            ->latest()
            ->get();

        return Inertia::render('Collection/Index', [
            'collections' => $collections,
        ]);
    }

    public function show(Collection $collection)
    {
        $this->authorize('view', $collection);

        $collection->load(['recipes.author', 'recipes.media', 'user']);

        return Inertia::render('Collection/Show', [
            'collection' => $collection,
            'canEdit' => Auth::user()?->can('update', $collection),
        ]);
    }

    public function store(StoreCollectionRequest $request)
    {
        $collection = Auth::user()->collections()->create($request->validated());

        return redirect()->route('collections.show', $collection)
            ->with('success', 'Collection créée');
    }

    public function update(StoreCollectionRequest $request, Collection $collection)
    {
        $this->authorize('update', $collection);

        $collection->update($request->validated());

        return back()->with('success', 'Collection mise à jour');
    }

    public function destroy(Collection $collection)
    {
        $this->authorize('delete', $collection);

        $collection->delete();

        return redirect()->route('collections.index')
            ->with('success', 'Collection supprimée');
    }

    public function addRecipe(Collection $collection, Recipe $recipe)
    {
        $this->authorize('update', $collection);

        if ($collection->recipes()->where('recipe_id', $recipe->id)->exists()) {
            return back()->withErrors(['error' => 'Cette recette est déjà dans la collection']);
        }

        $maxPosition = $collection->recipes()->max('collection_recipe.position') ?? -1;

        $collection->recipes()->attach($recipe->id, [
            'position' => $maxPosition + 1,
        ]);

        return back()->with('success', 'Recette ajoutée à la collection');
    }

    public function removeRecipe(Collection $collection, Recipe $recipe)
    {
        $this->authorize('update', $collection);

        $collection->recipes()->detach($recipe->id);

        return back()->with('success', 'Recette retirée de la collection');
    }

    public function reorder(CollectionReorderRequest $request, Collection $collection)
    {
        $this->authorize('update', $collection);

        $recipeIds = $request->getRecipeIds();

        foreach ($recipeIds as $position => $recipeId) {
            $collection->recipes()->updateExistingPivot($recipeId, [
                'position' => $position + 1,
            ]);
        }

        return back()->with('success', 'Ordre mis à jour');
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCollectionRequest;
use App\Models\Collection;
use App\Models\Recipe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class CollectionController extends Controller
{
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
        if (!$collection->is_public && $collection->user_id !== Auth::id()) {
            abort(403);
        }

        $collection->load(['recipes.author', 'recipes.media', 'user']);

        return Inertia::render('Collection/Show', [
            'collection' => $collection,
            'canEdit' => Auth::id() === $collection->user_id,
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
        if ($collection->user_id !== Auth::id()) {
            abort(403);
        }

        $collection->update($request->validated());

        return back()->with('success', 'Collection mise à jour');
    }

    public function destroy(Collection $collection)
    {
        if ($collection->user_id !== Auth::id()) {
            abort(403);
        }

        $collection->delete();

        return redirect()->route('collections.index')
            ->with('success', 'Collection supprimée');
    }

    public function addRecipe(Collection $collection, Recipe $recipe)
    {
        if ($collection->user_id !== Auth::id()) {
            abort(403);
        }

        if ($collection->recipes()->where('recipe_id', $recipe->id)->exists()) {
            return back()->withErrors(['error' => 'Cette recette est déjà dans la collection']);
        }

        $maxPosition = $collection->recipes()->max('position') ?? -1;

        $collection->recipes()->attach($recipe->id, [
            'position' => $maxPosition + 1,
        ]);

        return back()->with('success', 'Recette ajoutée à la collection');
    }

    public function removeRecipe(Collection $collection, Recipe $recipe)
    {
        if ($collection->user_id !== Auth::id()) {
            abort(403);
        }

        $collection->recipes()->detach($recipe->id);

        return back()->with('success', 'Recette retirée de la collection');
    }

    public function reorder(Collection $collection, Request $request)
    {
        if ($collection->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'recipes' => 'required|array',
            'recipes.*.id' => 'required|exists:recipes,id',
            'recipes.*.position' => 'required|integer',
        ]);

        foreach ($validated['recipes'] as $recipe) {
            $collection->recipes()->updateExistingPivot($recipe['id'], [
                'position' => $recipe['position'],
            ]);
        }

        return back()->with('success', 'Ordre mis à jour');
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\CollectionReorderRequest;
use App\Http\Requests\StoreCollectionRequest;
use App\Models\Collection;
use App\Models\Recipe;
use App\Services\CollectionService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
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

    public function show(Collection $collection)
    {
        $this->authorize('view', $collection);

        $collection = $this->collectionService->getCollectionWithRecipes($collection);

        return Inertia::render('Collection/Show', [
            'collection' => $collection,
            'canEdit' => Auth::user()?->can('update', $collection),
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

    public function reorder(CollectionReorderRequest $request, Collection $collection)
    {
        $this->authorize('update', $collection);

        $this->collectionService->reorderRecipes($collection, $request->getRecipeIds());

        return back()->with('success', 'Ordre mis à jour');
    }
}

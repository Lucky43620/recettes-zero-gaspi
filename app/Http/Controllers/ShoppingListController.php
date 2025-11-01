<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddShoppingListItemRequest;
use App\Http\Requests\StoreShoppingListRequest;
use App\Http\Requests\UpdateShoppingListItemRequest;
use App\Models\MealPlan;
use App\Models\ShoppingList;
use App\Models\ShoppingListItem;
use App\Services\ShoppingListService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ShoppingListController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private ShoppingListService $shoppingListService
    ) {}

    public function index()
    {
        $shoppingLists = Auth::user()->shoppingLists()
            ->with('items.unit')
            ->latest()
            ->get();

        return Inertia::render('ShoppingList/Index', [
            'shoppingLists' => $shoppingLists,
        ]);
    }

    public function show(ShoppingList $shoppingList)
    {
        $this->authorize('view', $shoppingList);

        $shoppingList->load('items.unit');

        return Inertia::render('ShoppingList/Show', [
            'shoppingList' => $shoppingList,
        ]);
    }

    public function store(StoreShoppingListRequest $request)
    {
        $validated = $request->validated();

        $shoppingList = Auth::user()->shoppingLists()->create($validated);

        return redirect()->route('shopping-lists.show', $shoppingList)
            ->with('success', 'Liste de courses créée avec succès');
    }

    public function generateFromMealPlan(Request $request, MealPlan $mealPlan)
    {
        $this->authorize('view', $mealPlan);

        $shoppingList = $this->shoppingListService->generateFromMealPlan($mealPlan, Auth::id());

        return redirect()->route('shopping-lists.show', $shoppingList)
            ->with('success', 'Liste de courses générée avec succès');
    }

    public function destroy(ShoppingList $shoppingList)
    {
        $this->authorize('delete', $shoppingList);

        $shoppingList->delete();

        return redirect()->route('shopping-lists.index')
            ->with('success', 'Liste supprimée avec succès');
    }

    public function addItem(AddShoppingListItemRequest $request, ShoppingList $shoppingList)
    {
        $this->authorize('update', $shoppingList);

        $validated = $request->validated();

        $shoppingList->items()->create($validated);

        return back();
    }

    public function updateItem(UpdateShoppingListItemRequest $request, ShoppingListItem $item)
    {
        $this->authorize('update', $item->shoppingList);

        $validated = $request->validated();

        $item->update($validated);

        return back();
    }

    public function removeItem(ShoppingListItem $item)
    {
        $this->authorize('update', $item->shoppingList);

        $item->delete();

        return back();
    }
}

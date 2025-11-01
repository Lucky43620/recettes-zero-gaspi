<?php

namespace App\Http\Controllers;

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

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'meal_plan_id' => 'nullable|exists:meal_plans,id',
        ]);

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

    public function addItem(Request $request, ShoppingList $shoppingList)
    {
        $this->authorize('update', $shoppingList);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'quantity' => 'nullable|numeric|min:0',
            'unit_code' => 'nullable|exists:units,code',
        ]);

        $shoppingList->items()->create($validated);

        return back();
    }

    public function updateItem(Request $request, ShoppingListItem $item)
    {
        $this->authorize('update', $item->shoppingList);

        $validated = $request->validate([
            'is_checked' => 'required|boolean',
        ]);

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

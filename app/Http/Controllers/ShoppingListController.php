<?php

namespace App\Http\Controllers;

use App\Models\MealPlan;
use App\Models\ShoppingList;
use App\Models\ShoppingListItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ShoppingListController extends Controller
{
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
        // Authorization check
        if ($shoppingList->user_id !== Auth::id()) {
            abort(403, 'Vous n\'êtes pas autorisé à voir cette liste.');
        }

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
        // Authorization check
        if ($mealPlan->user_id !== Auth::id()) {
            abort(403, 'Vous n\'êtes pas autorisé à générer une liste depuis ce planning.');
        }

        $mealPlan->load('mealPlanRecipes.recipe.ingredients.unit');

        $ingredients = collect();

        foreach ($mealPlan->mealPlanRecipes as $mealPlanRecipe) {
            foreach ($mealPlanRecipe->recipe->ingredients as $ingredient) {
                $key = $ingredient->name . '_' . $ingredient->unit_code;

                if ($ingredients->has($key)) {
                    $existing = $ingredients->get($key);
                    $existing['quantity'] += $ingredient->quantity * ($mealPlanRecipe->servings / $mealPlanRecipe->recipe->servings);
                    $ingredients->put($key, $existing);
                } else {
                    $ingredients->put($key, [
                        'name' => $ingredient->name,
                        'quantity' => $ingredient->quantity * ($mealPlanRecipe->servings / $mealPlanRecipe->recipe->servings),
                        'unit_code' => $ingredient->unit_code,
                    ]);
                }
            }
        }

        $weekStart = \Carbon\Carbon::parse($mealPlan->week_start)->format('d/m/Y');
        $shoppingList = Auth::user()->shoppingLists()->create([
            'name' => "Liste - Semaine du {$weekStart}",
            'meal_plan_id' => $mealPlan->id,
        ]);

        foreach ($ingredients as $ingredient) {
            $shoppingList->items()->create($ingredient);
        }

        return redirect()->route('shopping-lists.show', $shoppingList)
            ->with('success', 'Liste de courses générée avec succès');
    }

    public function destroy(ShoppingList $shoppingList)
    {
        $shoppingList->delete();

        return redirect()->route('shopping-lists.index')
            ->with('success', 'Liste supprimée avec succès');
    }

    public function addItem(Request $request, ShoppingList $shoppingList)
    {
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
        $validated = $request->validate([
            'is_checked' => 'required|boolean',
        ]);

        $item->update($validated);

        return back();
    }

    public function removeItem(ShoppingListItem $item)
    {
        $item->delete();

        return back();
    }
}

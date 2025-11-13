<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMealPlanRequest;
use App\Http\Requests\UpdateMealPlanRecipeRequest;
use App\Models\MealPlan;
use App\Models\MealPlanRecipe;
use App\Models\Recipe;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class MealPlanController extends Controller
{
    use AuthorizesRequests;
    public function index(Request $request)
    {
        $weekStart = $request->has('week')
            ? Carbon::parse($request->week)
            : Carbon::now()->startOfWeek();

        $mealPlan = Auth::user()->mealPlans()
            ->where('week_start_date', $weekStart->format('Y-m-d'))
            ->with(['mealPlanRecipes.recipe.media'])
            ->first();

        if (!$mealPlan) {
            $mealPlan = Auth::user()->mealPlans()->create([
                'week_start_date' => $weekStart->format('Y-m-d'),
            ]);
        }

        $userRecipes = Auth::user()->recipes()
            ->where('is_public', true)
            ->with('media')
            ->get();

        $favoriteRecipes = Auth::user()->favorites()
            ->where('is_public', true)
            ->with('media')
            ->get();

        return Inertia::render('MealPlan/Index', [
            'mealPlan' => $mealPlan,
            'weekStart' => $weekStart->format('Y-m-d'),
            'userRecipes' => $userRecipes,
            'favoriteRecipes' => $favoriteRecipes,
            'isPremium' => Auth::user()->isPremium(),
            'recipeLimit' => Auth::user()->isPremium() ? null : 3,
        ]);
    }

    public function addRecipe(StoreMealPlanRequest $request, MealPlan $mealPlan)
    {
        $this->authorize('update', $mealPlan);

        $user = $request->user();

        // Limit for free users: 3 recipes per week max
        if (! $user->isPremium() && $mealPlan->mealPlanRecipes()->count() >= 3) {
            return redirect()->back()->with('error', 'Limite atteinte. Passez à Premium pour des plans de repas illimités.');
        }

        $validated = $request->validated();

        $mealPlan->mealPlanRecipes()->create($validated);

        return back();
    }

    public function removeRecipe(MealPlanRecipe $mealPlanRecipe)
    {
        $this->authorize('update', $mealPlanRecipe->mealPlan);

        $mealPlanRecipe->delete();

        return back();
    }

    public function updateRecipe(UpdateMealPlanRecipeRequest $request, MealPlanRecipe $mealPlanRecipe)
    {
        $this->authorize('update', $mealPlanRecipe->mealPlan);

        $validated = $request->validated();

        $mealPlanRecipe->update($validated);

        return back();
    }

    public function duplicate(Request $request, MealPlan $mealPlan)
    {
        $this->authorize('view', $mealPlan);

        $validated = $request->validate([
            'week_start_date' => 'required|date',
        ]);

        $newWeekStart = Carbon::parse($validated['week_start_date']);

        $existingPlan = Auth::user()->mealPlans()
            ->where('week_start_date', $newWeekStart->format('Y-m-d'))
            ->first();

        if ($existingPlan) {
            $existingPlan->mealPlanRecipes()->delete();
            $newPlan = $existingPlan;
        } else {
            $newPlan = Auth::user()->mealPlans()->create([
                'week_start_date' => $newWeekStart->format('Y-m-d'),
                'name' => $mealPlan->name,
            ]);
        }

        foreach ($mealPlan->mealPlanRecipes as $mealPlanRecipe) {
            $newPlan->mealPlanRecipes()->create([
                'recipe_id' => $mealPlanRecipe->recipe_id,
                'planned_date' => $mealPlanRecipe->planned_date,
                'meal_type' => $mealPlanRecipe->meal_type,
                'servings' => $mealPlanRecipe->servings,
                'notes' => $mealPlanRecipe->notes,
            ]);
        }

        return redirect()->route('meal-plans.index', ['week' => $newWeekStart->format('Y-m-d')])
            ->with('success', 'Semaine dupliquée avec succès');
    }
}

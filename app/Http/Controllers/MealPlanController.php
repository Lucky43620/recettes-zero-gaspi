<?php

namespace App\Http\Controllers;

use App\Models\MealPlan;
use App\Models\MealPlanRecipe;
use App\Models\Recipe;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class MealPlanController extends Controller
{
    public function index(Request $request)
    {
        $weekStart = $request->has('week')
            ? Carbon::parse($request->week)
            : Carbon::now()->startOfWeek();

        $mealPlan = Auth::user()->mealPlans()
            ->where('week_start', $weekStart->format('Y-m-d'))
            ->with(['mealPlanRecipes.recipe.media'])
            ->first();

        if (!$mealPlan) {
            $mealPlan = Auth::user()->mealPlans()->create([
                'week_start' => $weekStart->format('Y-m-d'),
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
        ]);
    }

    public function addRecipe(Request $request, MealPlan $mealPlan)
    {
        // Authorization check
        if ($mealPlan->user_id !== Auth::id()) {
            abort(403, 'Vous n\'êtes pas autorisé à modifier ce planning.');
        }

        $validated = $request->validate([
            'recipe_id' => 'required|exists:recipes,id',
            'day_of_week' => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'meal_type' => 'required|in:breakfast,lunch,dinner,snack',
            'servings' => 'nullable|integer|min:1',
            'notes' => 'nullable|string',
        ]);

        $mealPlan->mealPlanRecipes()->create($validated);

        return back();
    }

    public function removeRecipe(MealPlanRecipe $mealPlanRecipe)
    {
        // Authorization check
        if ($mealPlanRecipe->mealPlan->user_id !== Auth::id()) {
            abort(403, 'Vous n\'êtes pas autorisé à modifier ce planning.');
        }

        $mealPlanRecipe->delete();

        return back();
    }

    public function updateRecipe(Request $request, MealPlanRecipe $mealPlanRecipe)
    {
        // Authorization check
        if ($mealPlanRecipe->mealPlan->user_id !== Auth::id()) {
            abort(403, 'Vous n\'êtes pas autorisé à modifier ce planning.');
        }

        $validated = $request->validate([
            'servings' => 'nullable|integer|min:1',
            'notes' => 'nullable|string',
        ]);

        $mealPlanRecipe->update($validated);

        return back();
    }

    public function duplicate(Request $request, MealPlan $mealPlan)
    {
        // Authorization check
        if ($mealPlan->user_id !== Auth::id()) {
            abort(403, 'Vous n\'êtes pas autorisé à dupliquer ce planning.');
        }

        $validated = $request->validate([
            'week_start' => 'required|date',
        ]);

        $newWeekStart = Carbon::parse($validated['week_start']);

        $existingPlan = Auth::user()->mealPlans()
            ->where('week_start', $newWeekStart->format('Y-m-d'))
            ->first();

        if ($existingPlan) {
            $existingPlan->mealPlanRecipes()->delete();
            $newPlan = $existingPlan;
        } else {
            $newPlan = Auth::user()->mealPlans()->create([
                'week_start' => $newWeekStart->format('Y-m-d'),
                'name' => $mealPlan->name,
            ]);
        }

        foreach ($mealPlan->mealPlanRecipes as $mealPlanRecipe) {
            $newPlan->mealPlanRecipes()->create([
                'recipe_id' => $mealPlanRecipe->recipe_id,
                'day_of_week' => $mealPlanRecipe->day_of_week,
                'meal_type' => $mealPlanRecipe->meal_type,
                'servings' => $mealPlanRecipe->servings,
                'notes' => $mealPlanRecipe->notes,
            ]);
        }

        return redirect()->route('meal-plans.index', ['week' => $newWeekStart->format('Y-m-d')])
            ->with('success', 'Semaine dupliquée avec succès');
    }
}

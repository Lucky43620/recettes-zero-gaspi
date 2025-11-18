<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMealPlanRequest;
use App\Http\Requests\UpdateMealPlanRecipeRequest;
use App\Models\MealPlan;
use App\Models\MealPlanRecipe;
use App\Models\Recipe;
use App\Services\FeatureLimitService;
use App\Services\MealPlanService;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class MealPlanController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private FeatureLimitService $limitService
    ) {}

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
            $mealPlan->load('mealPlanRecipes.recipe.media');
        }

        $userRecipes = Auth::user()->recipes()
            ->where('is_public', true)
            ->with('media')
            ->get();

        $favoriteRecipes = Auth::user()->favorites()
            ->where('is_public', true)
            ->with('media')
            ->get();

        $isPremium = Auth::user()->isPremium();

        return Inertia::render('MealPlan/Index', [
            'mealPlan' => $mealPlan,
            'weekStart' => $weekStart->format('Y-m-d'),
            'userRecipes' => $userRecipes,
            'favoriteRecipes' => $favoriteRecipes,
            'isPremium' => $isPremium,
            'recipeLimit' => $isPremium ? null : $this->limitService->getLimit($request->user(), 'meal_plan_recipes'),
        ]);
    }

    public function addRecipe(StoreMealPlanRequest $request, MealPlan $mealPlan, FeatureLimitService $limitService)
    {
        $this->authorize('update', $mealPlan);

        $user = $request->user();
        $currentCount = $mealPlan->mealPlanRecipes()->count();

        if (! $limitService->canAdd($user, 'meal_plan_recipes', $currentCount)) {
            return redirect()->back()->with('error', $limitService->getLimitMessage('meal_plan_recipes'));
        }

        $validated = $request->validated();

        $dayOfWeek = $validated['day_of_week'];
        $weekStart = Carbon::parse($mealPlan->week_start_date);

        $daysMap = [
            'monday' => 0,
            'tuesday' => 1,
            'wednesday' => 2,
            'thursday' => 3,
            'friday' => 4,
            'saturday' => 5,
            'sunday' => 6,
        ];

        $plannedDate = $weekStart->copy()->addDays($daysMap[$dayOfWeek]);

        $mealPlan->mealPlanRecipes()->create([
            'recipe_id' => $validated['recipe_id'],
            'planned_date' => $plannedDate->format('Y-m-d'),
            'meal_type' => $validated['meal_type'],
            'servings' => $validated['servings'] ?? 1,
            'notes' => $validated['notes'] ?? null,
        ]);

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

    public function moveRecipe(Request $request, MealPlanRecipe $mealPlanRecipe)
    {
        $this->authorize('update', $mealPlanRecipe->mealPlan);

        $validated = $request->validate([
            'day_of_week' => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'meal_type' => 'required|in:breakfast,lunch,dinner,snack',
        ]);

        $mealPlan = $mealPlanRecipe->mealPlan;
        $weekStart = Carbon::parse($mealPlan->week_start_date);

        $daysMap = [
            'monday' => 0,
            'tuesday' => 1,
            'wednesday' => 2,
            'thursday' => 3,
            'friday' => 4,
            'saturday' => 5,
            'sunday' => 6,
        ];

        $plannedDate = $weekStart->copy()->addDays($daysMap[$validated['day_of_week']]);

        $mealPlanRecipe->update([
            'planned_date' => $plannedDate->format('Y-m-d'),
            'meal_type' => $validated['meal_type'],
        ]);

        return back();
    }

    public function duplicate(Request $request, MealPlan $mealPlan, MealPlanService $mealPlanService)
    {
        $this->authorize('view', $mealPlan);

        $validated = $request->validate([
            'week_start_date' => 'required|date',
        ]);

        $newPlan = $mealPlanService->duplicateMealPlan(
            Auth::user(),
            $mealPlan,
            $validated['week_start_date']
        );

        return redirect()->route('meal-plans.index', ['week' => $validated['week_start_date']])
            ->with('success', 'Semaine dupliquée avec succès');
    }
}

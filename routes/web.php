<?php

use App\Http\Controllers\CollectionController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\FeedController;
use App\Http\Controllers\FollowController;
use App\Http\Controllers\IngredientController;
use App\Http\Controllers\MealPlanController;
use App\Http\Controllers\PantryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\RecipeController;
use App\Http\Controllers\ShoppingListController;
use App\Models\MealPlanRecipe;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::bind('mealPlanRecipe', function (string $value) {
    return MealPlanRecipe::findOrFail($value);
});

Route::get('/', function () {
    $featuredRecipes = \App\Models\Recipe::where('is_public', true)
        ->with(['author', 'media'])
        ->orderBy('rating_avg', 'desc')
        ->orderBy('rating_count', 'desc')
        ->limit(6)
        ->get();

    return Inertia::render('Home', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'featuredRecipes' => $featuredRecipes,
    ]);
})->name('home');

// Public recipe routes - order matters! Specific routes before parameterized routes
Route::get('/recipes', [RecipeController::class, 'index'])->name('recipes.index');
Route::get('/recipes/create', [RecipeController::class, 'create'])->name('recipes.create')->middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified']);
Route::get('/recipes/{recipe:slug}', [RecipeController::class, 'show'])->name('recipes.show');

Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/ingredients/{ingredient}', [IngredientController::class, 'show'])->name('ingredients.show');

Route::get('/profile/{user}', [ProfileController::class, 'show'])->name('profile.show');
Route::get('/profile/{user}/followers', [ProfileController::class, 'followers'])->name('profile.followers');
Route::get('/profile/{user}/following', [ProfileController::class, 'following'])->name('profile.following');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    Route::get('/dashboard', function () {
        $user = Auth::user();

        $stats = [
            'totalRecipes' => $user->recipes()->count(),
            'publicRecipes' => $user->recipes()->where('is_public', true)->count(),
            'privateRecipes' => $user->recipes()->where('is_public', false)->count(),
            'totalRatings' => $user->recipes()->withCount('ratings')->get()->sum('ratings_count'),
            'averageRating' => $user->recipes()->whereNotNull('rating_avg')->avg('rating_avg'),
            'totalComments' => $user->recipes()->withCount('comments')->get()->sum('comments_count'),
            'recentRecipes' => $user->recipes()->with(['media'])->latest()->limit(3)->get(),
        ];

        return Inertia::render('Dashboard', [
            'stats' => $stats
        ]);
    })->name('dashboard');

    Route::get('/my-recipes', [RecipeController::class, 'myRecipes'])->name('recipes.my');
    Route::post('/recipes', [RecipeController::class, 'store'])->name('recipes.store');
    Route::get('/recipes/{recipe:slug}/edit', [RecipeController::class, 'edit'])->name('recipes.edit');
    Route::put('/recipes/{recipe:slug}', [RecipeController::class, 'update'])->name('recipes.update');
    Route::delete('/recipes/{recipe:slug}', [RecipeController::class, 'destroy'])->name('recipes.destroy');

    Route::get('/feed', [FeedController::class, 'index'])->name('feed.index');

    Route::post('/users/{user}/follow', [FollowController::class, 'follow'])->name('users.follow');
    Route::delete('/users/{user}/unfollow', [FollowController::class, 'unfollow'])->name('users.unfollow');

    Route::post('/recipes/{recipe:slug}/ratings', [RatingController::class, 'store'])->name('ratings.store');
    Route::delete('/recipes/{recipe:slug}/ratings', [RatingController::class, 'destroy'])->name('ratings.destroy');

    Route::post('/recipes/{recipe:slug}/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');
    Route::post('/comments/{comment}/vote/{type}', [CommentController::class, 'vote'])->name('comments.vote');

    Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites.index');
    Route::post('/recipes/{recipe:slug}/favorite', [FavoriteController::class, 'toggle'])->name('favorites.toggle');

    Route::get('/collections', [CollectionController::class, 'index'])->name('collections.index');
    Route::post('/collections', [CollectionController::class, 'store'])->name('collections.store');
    Route::get('/collections/{collection}', [CollectionController::class, 'show'])->name('collections.show');
    Route::put('/collections/{collection}', [CollectionController::class, 'update'])->name('collections.update');
    Route::delete('/collections/{collection}', [CollectionController::class, 'destroy'])->name('collections.destroy');
    Route::post('/collections/{collection}/recipes/{recipe}', [CollectionController::class, 'addRecipe'])->name('collections.recipes.add')->where('recipe', '[0-9]+');
    Route::delete('/collections/{collection}/recipes/{recipe:slug}', [CollectionController::class, 'removeRecipe'])->name('collections.recipes.remove');
    Route::post('/collections/{collection}/reorder', [CollectionController::class, 'reorder'])->name('collections.reorder');

    Route::get('/meal-plans', [MealPlanController::class, 'index'])->name('meal-plans.index');
    Route::post('/meal-plans/{mealPlan}/recipes', [MealPlanController::class, 'addRecipe'])->name('meal-plans.recipes.add');
    Route::delete('/meal-plan-recipes/{mealPlanRecipe}', [MealPlanController::class, 'removeRecipe'])->name('meal-plans.recipes.remove');
    Route::put('/meal-plan-recipes/{mealPlanRecipe}', [MealPlanController::class, 'updateRecipe'])->name('meal-plans.recipes.update');
    Route::post('/meal-plans/{mealPlan}/duplicate', [MealPlanController::class, 'duplicate'])->name('meal-plans.duplicate');

    Route::get('/shopping-lists', [ShoppingListController::class, 'index'])->name('shopping-lists.index');
    Route::post('/shopping-lists', [ShoppingListController::class, 'store'])->name('shopping-lists.store');
    Route::get('/shopping-lists/{shoppingList}', [ShoppingListController::class, 'show'])->name('shopping-lists.show');
    Route::delete('/shopping-lists/{shoppingList}', [ShoppingListController::class, 'destroy'])->name('shopping-lists.destroy');
    Route::post('/meal-plans/{mealPlan}/generate-shopping-list', [ShoppingListController::class, 'generateFromMealPlan'])->name('shopping-lists.generate');
    Route::post('/shopping-lists/{shoppingList}/items', [ShoppingListController::class, 'addItem'])->name('shopping-lists.items.add');
    Route::put('/shopping-list-items/{item}', [ShoppingListController::class, 'updateItem'])->name('shopping-lists.items.update');
    Route::delete('/shopping-list-items/{item}', [ShoppingListController::class, 'removeItem'])->name('shopping-lists.items.remove');

    Route::get('/pantry', [PantryController::class, 'index'])->name('pantry.index');
    Route::post('/pantry', [PantryController::class, 'store'])->name('pantry.store');
    Route::put('/pantry/{pantryItem}', [PantryController::class, 'update'])->name('pantry.update');
    Route::delete('/pantry/{pantryItem}', [PantryController::class, 'destroy'])->name('pantry.destroy');

    Route::get('/anti-waste', function () {
        return Inertia::render('AntiWaste/Index');
    })->name('anti-waste.index');
});
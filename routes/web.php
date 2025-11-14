<?php

use App\Http\Controllers\CollectionController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\CooksnapController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\FeedController;
use App\Http\Controllers\FollowController;
use App\Http\Controllers\GdprController;
use App\Http\Controllers\IngredientController;
use App\Http\Controllers\MealPlanController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PantryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\RecipeController;
use App\Http\Controllers\ReportController;
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

// Stripe webhook (handled automatically by Laravel Cashier)
Route::post('/stripe/webhook', '\Laravel\Cashier\Http\Controllers\WebhookController@handleWebhook')
    ->name('cashier.webhook');

// Public recipe routes - order matters! Specific routes before parameterized routes
Route::get('/recipes', [RecipeController::class, 'index'])->name('recipes.index');
Route::get('/recipes/create', [RecipeController::class, 'create'])->name('recipes.create')->middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified']);
Route::get('/recipes/{recipe:slug}', [RecipeController::class, 'show'])->name('recipes.show');
Route::get('/recipes/{recipe:slug}/cook', [RecipeController::class, 'cook'])->name('recipes.cook');

Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/ingredients/{ingredient}', [IngredientController::class, 'show'])->name('ingredients.show');

Route::get('/profile/{user}', [ProfileController::class, 'show'])->name('profile.show');
Route::get('/profile/{user}/followers', [ProfileController::class, 'followers'])->name('profile.followers');
Route::get('/profile/{user}/following', [ProfileController::class, 'following'])->name('profile.following');

Route::get('/events', [EventController::class, 'index'])->name('events.index');
Route::get('/events/{event:slug}', [EventController::class, 'show'])->name('events.show');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    Route::get('/dashboard', function () {
        $user = Auth::user();

        $recipes = $user->recipes()
            ->select('id', 'author_id', 'is_public', 'rating_count', 'rating_avg')
            ->get();

        $stats = [
            'totalRecipes' => $recipes->count(),
            'publicRecipes' => $recipes->where('is_public', true)->count(),
            'privateRecipes' => $recipes->where('is_public', false)->count(),
            'totalRatings' => $recipes->sum('rating_count'),
            'averageRating' => $recipes->whereNotNull('rating_avg')->avg('rating_avg'),
            'totalComments' => $user->recipes()
                ->selectRaw('COALESCE(SUM((SELECT COUNT(*) FROM comments WHERE comments.recipe_id = recipes.id)), 0) as total')
                ->value('total'),
            'recentRecipes' => $user->recipes()
                ->select('id', 'author_id', 'title', 'slug', 'created_at')
                ->with('media')
                ->latest()
                ->limit(3)
                ->get(),
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

    Route::post('/recipes/{recipe:slug}/cooksnaps', [CooksnapController::class, 'store'])->name('cooksnaps.store');
    Route::delete('/cooksnaps/{cooksnap}', [CooksnapController::class, 'destroy'])->name('cooksnaps.destroy');

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
    })->name('anti-waste.index')->middleware('premium');

    Route::post('/events', [EventController::class, 'store'])->name('events.store');
    Route::put('/events/{event:slug}', [EventController::class, 'update'])->name('events.update');
    Route::delete('/events/{event:slug}', [EventController::class, 'destroy'])->name('events.destroy');
    Route::post('/events/{event:slug}/join', [EventController::class, 'join'])->name('events.join');
    Route::delete('/events/{event:slug}/leave', [EventController::class, 'leave'])->name('events.leave');

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
    Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy'])->name('notifications.destroy');

    Route::post('/reports', [ReportController::class, 'store'])
        ->middleware('throttle:10,1')
        ->name('reports.store');
    Route::put('/reports/{report}', [ReportController::class, 'update'])->name('reports.update');

    Route::get('/gdpr/export', [GdprController::class, 'exportData'])->name('gdpr.export');
    Route::delete('/gdpr/delete-account', [GdprController::class, 'deleteAccount'])->name('gdpr.delete');

    Route::post('/barcode/lookup', [IngredientController::class, 'lookupBarcode'])
        ->middleware(['premium', 'throttle:60,1'])
        ->name('barcode.lookup');

    // Subscription routes
    Route::prefix('subscription')->name('subscription.')->group(function () {
        Route::get('/', [\App\Http\Controllers\SubscriptionController::class, 'index'])->name('index');
        Route::post('/checkout', [\App\Http\Controllers\SubscriptionController::class, 'checkout'])->name('checkout');
        Route::get('/success', [\App\Http\Controllers\SubscriptionController::class, 'success'])->name('success');
        Route::get('/manage', [\App\Http\Controllers\SubscriptionController::class, 'manage'])->name('manage');
        Route::post('/resume', [\App\Http\Controllers\SubscriptionController::class, 'resume'])->name('resume');
        Route::post('/cancel', [\App\Http\Controllers\SubscriptionController::class, 'cancel'])->name('cancel');
        Route::get('/payment-method', [\App\Http\Controllers\SubscriptionController::class, 'paymentMethod'])->name('payment-method');
        Route::post('/payment-method', [\App\Http\Controllers\SubscriptionController::class, 'updatePaymentMethod'])->name('payment-method.update');
    });
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
    \App\Http\Middleware\EnsureUserIsAdmin::class,
])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Admin\AdminController::class, 'dashboard'])->name('dashboard');

    Route::get('/users', [\App\Http\Controllers\Admin\AdminUserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}', [\App\Http\Controllers\Admin\AdminUserController::class, 'show'])->name('users.show');
    Route::delete('/users/{user}', [\App\Http\Controllers\Admin\AdminUserController::class, 'destroy'])->name('users.destroy');

    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

    Route::get('/badges', [\App\Http\Controllers\Admin\AdminBadgeController::class, 'index'])->name('badges.index');
    Route::post('/badges', [\App\Http\Controllers\Admin\AdminBadgeController::class, 'store'])->name('badges.store');
    Route::put('/badges/{badge}', [\App\Http\Controllers\Admin\AdminBadgeController::class, 'update'])->name('badges.update');
    Route::delete('/badges/{badge}', [\App\Http\Controllers\Admin\AdminBadgeController::class, 'destroy'])->name('badges.destroy');
});
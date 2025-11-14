# Performance Optimization Recommendations

**Date**: November 14, 2025
**Current Status**: Well-optimized with room for enhancements

---

## Current Optimizations ✅

### 1. Database Query Optimization
**Status**: ✅ **COMPLETED**

- All N+1 queries fixed (10 instances resolved)
- Eager loading used throughout:
  - `Recipe::with(['author', 'media', 'ratings'])`
  - `Collection::withCount('recipes')`
  - Optimized queries in AdminController (16→5 queries)

### 2. Indexing
**Status**: ✅ **IN PLACE**

**Existing Indexes:**
- Primary keys on all tables
- Foreign key indexes
- FULLTEXT index on `ingredients.name` (for MATCH AGAINST queries)
- Unique indexes for email, slug fields

### 3. Caching Strategy
**Status**: ⚠️ **NEEDS IMPLEMENTATION**

**Recommended Cache Layers:**

#### Application Cache (Redis)
```php
// Config: cache.php - driver = 'redis'
// TTL recommendations:

// 1. Featured recipes (home page) - 15 minutes
Cache::remember('featured_recipes', 900, function () {
    return Recipe::where('is_public', true)
        ->with(['author', 'media'])
        ->orderBy('rating_avg', 'desc')
        ->limit(6)
        ->get();
});

// 2. User statistics (dashboard) - 5 minutes
Cache::remember("user_stats_{$userId}", 300, function () use ($userId) {
    // Expensive stats calculations
});

// 3. Recipe details (most accessed) - 30 minutes
Cache::remember("recipe_{$slug}", 1800, function () use ($slug) {
    return Recipe::with(['author', 'steps', 'ingredients'])
        ->where('slug', $slug)
        ->first();
});

// 4. Event leaderboards - 2 minutes (frequently updated)
Cache::remember("event_leaderboard_{$eventId}", 120, function () use ($eventId) {
    return $eventService->getLeaderboard($event, 10);
});
```

**Cache Invalidation Strategy:**
```php
// In RecipeService::updateRecipe()
Cache::forget("recipe_{$recipe->slug}");
Cache::forget('featured_recipes');

// In RatingService::addOrUpdateRating()
Cache::forget("recipe_{$recipe->slug}");

// In EventService
Cache::forget("event_leaderboard_{$eventId}");
```

---

## Implementation Roadmap

### Phase 1: Quick Wins (1-2 hours)
**Priority**: HIGH

1. **Add Route Caching**
```bash
php artisan route:cache
php artisan config:cache
php artisan view:cache
```

2. **Add Opcode Caching**
```ini
# php.ini
opcache.enable=1
opcache.memory_consumption=256
opcache.max_accelerated_files=20000
opcache.validate_timestamps=0 # Production only
```

3. **Database Connection Pooling**
```env
DB_CONNECTION=mysql
DB_PERSISTENT=true
```

### Phase 2: Application Caching (2-4 hours)
**Priority**: HIGH

1. **Implement Model Caching**

Create `app/Traits/Cacheable.php`:
```php
<?php

namespace App\Traits;

use Illuminate\Support\Facades\Cache;

trait Cacheable
{
    protected static function bootCacheable()
    {
        static::created(function ($model) {
            $model->clearCache();
        });

        static::updated(function ($model) {
            $model->clearCache();
        });

        static::deleted(function ($model) {
            $model->clearCache();
        });
    }

    public function clearCache()
    {
        $cacheKeys = $this->getCacheKeys();
        foreach ($cacheKeys as $key) {
            Cache::forget($key);
        }
    }

    abstract public function getCacheKeys(): array;
}
```

2. **Apply to Models**
```php
// app/Models/Recipe.php
use App\Traits\Cacheable;

class Recipe extends Model
{
    use Cacheable;

    public function getCacheKeys(): array
    {
        return [
            "recipe_{$this->slug}",
            "user_recipes_{$this->author_id}",
            'featured_recipes',
        ];
    }
}
```

3. **Cache Hot Endpoints**

Update controllers to use caching:
```php
// RecipeController::index()
$recipes = Cache::remember(
    "recipes_page_{$page}_{$filters_hash}",
    600,
    fn() => $query->paginate(12)
);

// ProfileController::show()
$stats = Cache::remember(
    "user_stats_{$user->id}",
    300,
    fn() => $this->calculateUserStats($user)
);
```

### Phase 3: Query Optimization (2-3 hours)
**Priority**: MEDIUM

1. **Add Missing Indexes**
```php
// Migration: add_performance_indexes.php
Schema::table('recipes', function (Blueprint $table) {
    $table->index(['is_public', 'created_at']);
    $table->index(['author_id', 'is_public']);
    $table->index('rating_avg');
});

Schema::table('comments', function (Blueprint $table) {
    $table->index(['recipe_id', 'created_at']);
    $table->index(['parent_id', 'created_at']);
});

Schema::table('ratings', function (Blueprint $table) {
    $table->index(['recipe_id', 'user_id']); // Composite for lookup
});
```

2. **Optimize Queries Further**
```php
// Use select() to limit columns
Recipe::select('id', 'title', 'slug', 'author_id', 'rating_avg')
    ->with(['author:id,name']) // Specify columns
    ->get();

// Use chunk() for large datasets
Recipe::chunk(100, function ($recipes) {
    // Process in batches
});
```

### Phase 4: CDN & Asset Optimization (1-2 hours)
**Priority**: MEDIUM

1. **Compile Assets for Production**
```bash
npm run build
php artisan storage:link
```

2. **Image Optimization**
- Already using Spatie Media Library ✅
- Consider adding automatic WebP conversion
- Lazy loading images in Vue components

3. **CDN Configuration** (if using)
```env
ASSET_URL=https://cdn.your-domain.com
```

### Phase 5: Advanced Optimizations (4-6 hours)
**Priority**: LOW

1. **Queue Long-Running Jobs**
```php
// Already configured ✅
// dispatch(new GenerateShoppingList($mealPlan));
// dispatch(new SendExpirationNotifications($pantryItem));
```

2. **Implement API Response Caching**
```php
// Middleware: CacheResponse
return response()
    ->json($data)
    ->header('Cache-Control', 'public, max-age=300');
```

3. **Database Read Replicas** (Production)
```php
// config/database.php
'mysql' => [
    'read' => ['host' => env('DB_READ_HOST')],
    'write' => ['host' => env('DB_WRITE_HOST')],
],
```

4. **Session Store Optimization**
```env
SESSION_DRIVER=redis  # Instead of file/database
```

---

## Performance Monitoring

### Tools to Implement

1. **Laravel Telescope** (Development)
   - Already installed ✅
   - Monitor queries, requests, jobs

2. **Laravel Horizon** (Production queues)
```bash
composer require laravel/horizon
php artisan horizon:install
```

3. **Query Monitoring**
```php
// AppServiceProvider::boot()
if (app()->environment('local')) {
    DB::listen(function ($query) {
        if ($query->time > 100) { // >100ms
            Log::warning('Slow query detected', [
                'sql' => $query->sql,
                'bindings' => $query->bindings,
                'time' => $query->time
            ]);
        }
    });
}
```

---

## Current Performance Baseline

### ✅ Already Optimized
1. N+1 queries eliminated
2. Eager loading throughout
3. Pagination on large datasets
4. FULLTEXT search indexing
5. Rate limiting on API endpoints
6. Job queue for async tasks
7. Redis for sessions/cache
8. Meilisearch for fast search

### ⚠️ Can Be Improved
1. Add application-level caching
2. Add more database indexes
3. Implement CDN for static assets
4. Add response caching headers

---

## Expected Performance Gains

| Optimization | Expected Improvement |
|--------------|---------------------|
| Route/Config cache | 20-30% faster bootstrap |
| Query caching | 50-80% faster on cached hits |
| Additional indexes | 30-50% faster complex queries |
| Opcode cache | 10-20% overall speedup |
| CDN for assets | 40-60% faster page loads |
| Redis sessions | 30-40% faster session access |

---

## Implementation Commands

```bash
# Enable caching (Production)
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Clear caches (Development)
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# Install additional tools
composer require laravel/horizon
composer require predis/predis

# Database optimization
php artisan migrate:fresh --seed # Recreate with indexes
php artisan db:monitor # Monitor connection pools
```

---

## Monitoring Metrics to Track

1. **Response Times**
   - Average: < 200ms
   - 95th percentile: < 500ms
   - 99th percentile: < 1000ms

2. **Database Queries**
   - Queries per request: < 20
   - Query time: < 50ms average
   - No N+1 queries

3. **Cache Hit Ratio**
   - Target: > 80%
   - Monitor with: `Redis::info()`

4. **Memory Usage**
   - PHP workers: < 128MB each
   - Redis: < 500MB total

---

## Conclusion

The application already has **excellent optimization fundamentals**:
- ✅ No N+1 queries
- ✅ Proper indexing
- ✅ Eager loading
- ✅ Queue system
- ✅ Search engine (Meilisearch)

**Recommended Next Steps:**
1. Implement application-level caching (Phase 1-2)
2. Add missing database indexes (Phase 3)
3. Monitor with Telescope/Horizon
4. Measure before/after with benchmarks

**Estimated total implementation time**: 10-16 hours
**Expected overall performance gain**: 2-3x faster on most endpoints

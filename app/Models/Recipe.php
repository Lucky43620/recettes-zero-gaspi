<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Recipe extends Model implements HasMedia
{
    use HasFactory, Sluggable, InteractsWithMedia;

    protected $fillable = [
        'author_id',
        'title',
        'slug',
        'summary',
        'servings',
        'prep_minutes',
        'cook_minutes',
        'difficulty',
        'cuisine',
        'is_public',
        'calories',
        'nutrients',
        'rating_avg',
        'rating_count',
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'nutrients' => 'array',
        'servings' => 'integer',
        'prep_minutes' => 'integer',
        'cook_minutes' => 'integer',
        'calories' => 'integer',
        'rating_count' => 'integer',
        'rating_avg' => 'decimal:2',
        'difficulty' => \App\Enums\Difficulty::class,
    ];

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'title'
            ]
        ];
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function steps(): HasMany
    {
        return $this->hasMany(RecipeStep::class)->orderBy('position');
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class)->whereNull('parent_id');
    }

    public function favorites()
    {
        return $this->belongsToMany(User::class, 'favorites')->withTimestamps();
    }

    public function collections()
    {
        return $this->belongsToMany(Collection::class)
            ->withPivot('position', 'added_at');
    }

    public function ingredients()
    {
        return $this->belongsToMany(Ingredient::class, 'recipe_ingredients')
            ->withPivot('quantity', 'unit_code', 'position')
            ->withTimestamps()
            ->orderBy('recipe_ingredients.position');
    }

    public function cooksnaps()
    {
        return $this->hasMany(Cooksnap::class)->latest();
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('images')
            ->useFallbackUrl('/images/recipe-placeholder.jpg');
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->fit(Fit::Crop, 300, 300)
            ->format('webp')
            ->quality(85)
            ->performOnCollections('images');

        $this->addMediaConversion('medium')
            ->fit(Fit::Max, 800, 800)
            ->format('webp')
            ->quality(85)
            ->performOnCollections('images');

        $this->addMediaConversion('large')
            ->fit(Fit::Max, 1200, 1200)
            ->format('webp')
            ->quality(90)
            ->performOnCollections('images');
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopeWithMetadata($query)
    {
        return $query->with(['author', 'media']);
    }

    public function scopeWithFullData($query)
    {
        return $query->with(['author', 'media', 'ratings', 'comments']);
    }

    public function scopeByDifficulty($query, $difficulty)
    {
        return $query->where('difficulty', $difficulty);
    }

    public function scopePopular($query)
    {
        return $query->where('rating_count', '>', 0)->orderByDesc('rating_avg');
    }

    public function scopeRecent($query)
    {
        return $query->latest();
    }
}

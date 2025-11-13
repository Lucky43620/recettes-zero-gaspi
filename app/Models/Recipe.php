<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Scout\Searchable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Recipe extends Model implements HasMedia
{
    use HasFactory, Sluggable, Searchable, InteractsWithMedia;

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

    public function toSearchableArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'summary' => $this->summary,
            'difficulty' => $this->difficulty,
            'cuisine' => $this->cuisine,
            'author_name' => $this->author->name,
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

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

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('images')
            ->useFallbackUrl('/images/recipe-placeholder.jpg');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Cooksnap extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'user_id',
        'recipe_id',
        'comment',
    ];

    protected $with = ['media'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function recipe(): BelongsTo
    {
        return $this->belongsTo(Recipe::class);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('photos')
            ->useDisk('public');
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->fit(Fit::Crop, 300, 300)
            ->format('webp')
            ->quality(85)
            ->performOnCollections('photos');

        $this->addMediaConversion('medium')
            ->fit(Fit::Max, 800, 800)
            ->format('webp')
            ->quality(85)
            ->performOnCollections('photos');

        $this->addMediaConversion('large')
            ->fit(Fit::Max, 1200, 1200)
            ->format('webp')
            ->quality(90)
            ->performOnCollections('photos');
    }
}

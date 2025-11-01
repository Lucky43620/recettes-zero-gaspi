<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Rating extends Model
{
    public $incrementing = false;
    protected $primaryKey = null;

    protected $fillable = [
        'user_id',
        'recipe_id',
        'rating',
        'review',
    ];

    protected $casts = [
        'rating' => 'integer',
    ];

    protected static function booted()
    {
        static::created(function ($rating) {
            $rating->updateRecipeRating();
        });

        static::updated(function ($rating) {
            $rating->updateRecipeRating();
        });

        static::deleting(function ($rating) {
            $rating->updateRecipeRating();
        });
    }

    protected function setKeysForSaveQuery($query)
    {
        return $query->where('user_id', $this->getAttribute('user_id'))
                     ->where('recipe_id', $this->getAttribute('recipe_id'));
    }

    protected function setKeysForSelectQuery($query)
    {
        return $query->where('user_id', $this->getAttribute('user_id'))
                     ->where('recipe_id', $this->getAttribute('recipe_id'));
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function recipe()
    {
        return $this->belongsTo(Recipe::class);
    }

    protected function updateRecipeRating()
    {
        $recipe = Recipe::find($this->recipe_id);

        if (!$recipe) {
            return;
        }

        $stats = Rating::where('recipe_id', $this->recipe_id)
            ->select(DB::raw('AVG(rating) as avg, COUNT(*) as count'))
            ->first();

        $recipe->update([
            'rating_avg' => $stats->avg ?? 0,
            'rating_count' => $stats->count ?? 0,
        ]);
    }
}

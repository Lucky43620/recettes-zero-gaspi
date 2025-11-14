<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Cashier\Billable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens;
    use Billable;
    use HasFactory;
    use HasProfilePhoto;
    use HasRoles;
    use Notifiable;
    use TwoFactorAuthenticatable;

    public function recipes()
    {
        return $this->hasMany(Recipe::class, 'author_id');
    }

    public function followers()
    {
        return $this->belongsToMany(User::class, 'followers', 'following_id', 'follower_id');
    }

    public function following()
    {
        return $this->belongsToMany(User::class, 'followers', 'follower_id', 'following_id');
    }

    public function isFollowing(User $user)
    {
        return $this->following()->where('following_id', $user->id)->exists();
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function favorites()
    {
        return $this->belongsToMany(Recipe::class, 'favorites')->withTimestamps();
    }

    public function hasFavorited(Recipe $recipe)
    {
        return $this->favorites()->where('recipe_id', $recipe->id)->exists();
    }

    public function collections()
    {
        return $this->hasMany(Collection::class);
    }

    public function mealPlans()
    {
        return $this->hasMany(MealPlan::class);
    }

    public function shoppingLists()
    {
        return $this->hasMany(ShoppingList::class);
    }

    public function pantryItems()
    {
        return $this->hasMany(PantryItem::class);
    }

    public function isPremium(): bool
    {
        return $this->subscribed('default');
    }

    public function isFree(): bool
    {
        return ! $this->isPremium();
    }

    public function planName(): string
    {
        if ($this->isFree()) {
            return 'free';
        }

        $subscription = $this->subscription('default');

        if (! $subscription) {
            return 'free';
        }

        if ($subscription->stripe_price === env('STRIPE_PRICE_MONTHLY')) {
            return 'monthly';
        }

        if ($subscription->stripe_price === env('STRIPE_PRICE_YEARLY')) {
            return 'yearly';
        }

        return 'premium';
    }

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    protected $appends = [
        'profile_photo_url',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}

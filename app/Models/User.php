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

class User extends Authenticatable
{
    use HasApiTokens;
    use Billable;

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;

    use HasProfilePhoto;
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

    /**
     * Check if the user has an active premium subscription.
     */
    public function isPremium(): bool
    {
        return $this->subscribed('default');
    }

    /**
     * Check if the user is on a free plan.
     */
    public function isFree(): bool
    {
        return ! $this->isPremium();
    }

    /**
     * Get the user's subscription plan name.
     */
    public function planName(): string
    {
        if ($this->isFree()) {
            return 'free';
        }

        $subscription = $this->subscription('default');

        if (! $subscription) {
            return 'free';
        }

        // Check if it's the monthly or yearly price
        if ($subscription->stripe_price === env('STRIPE_PRICE_MONTHLY')) {
            return 'monthly';
        }

        if ($subscription->stripe_price === env('STRIPE_PRICE_YEARLY')) {
            return 'yearly';
        }

        return 'premium';
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}

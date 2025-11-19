<?php

namespace App\Models;

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

    public function isOnTrial(): bool
    {
        return $this->onTrial('default');
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

        if ($subscription->stripe_price === config('cashier.price_monthly')) {
            return 'monthly';
        }

        if ($subscription->stripe_price === config('cashier.price_yearly')) {
            return 'yearly';
        }

        return 'premium';
    }

    public function planDisplayName(): string
    {
        return match ($this->planName()) {
            'monthly' => SystemSetting::getValue('monthly_plan_name', 'Premium Mensuel'),
            'yearly' => SystemSetting::getValue('yearly_plan_name', 'Premium Annuel'),
            'free' => 'Gratuit',
            default => 'Premium',
        };
    }

    public function planPrice(): ?string
    {
        $monthlyPrice = SystemSetting::getValue('monthly_price', 4.99);
        $yearlyPrice = SystemSetting::getValue('yearly_price', 49.90);

        return match ($this->planName()) {
            'monthly' => number_format($monthlyPrice, 2, ',', ' ') . '€/mois',
            'yearly' => number_format($yearlyPrice, 2, ',', ' ') . '€/an',
            'free' => 'Gratuit',
            default => null,
        };
    }

    public function subscriptionStatus(): string
    {
        if ($this->isFree()) {
            return 'inactive';
        }

        $subscription = $this->subscription('default');

        if (! $subscription) {
            return 'inactive';
        }

        if ($subscription->onGracePeriod()) {
            return 'canceled';
        }

        if ($subscription->onTrial()) {
            return 'trialing';
        }

        if ($subscription->pastDue()) {
            return 'past_due';
        }

        if ($subscription->incomplete()) {
            return 'incomplete';
        }

        return 'active';
    }

    public function subscriptionEndsAt(): ?string
    {
        $subscription = $this->subscription('default');

        if (! $subscription || ! $subscription->ends_at) {
            return null;
        }

        return $subscription->ends_at->format('d/m/Y');
    }

    public function canAccessPremiumFeatures(): bool
    {
        return $this->subscribed('default') || $this->onTrial('default');
    }

    public function stripeName(): ?string
    {
        return $this->name;
    }

    public function stripeEmail(): ?string
    {
        return $this->email;
    }

    public function exportPersonalData(): array
    {
        return [
            'user' => [
                'name' => $this->name,
                'email' => $this->email,
                'created_at' => $this->created_at->toIso8601String(),
            ],
            'subscription' => [
                'is_premium' => $this->isPremium(),
                'plan' => $this->planName(),
                'status' => $this->subscriptionStatus(),
                'ends_at' => $this->subscriptionEndsAt(),
            ],
            'recipes' => $this->recipes()->count(),
            'favorites' => $this->favorites()->count(),
            'comments' => $this->comments()->count(),
        ];
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

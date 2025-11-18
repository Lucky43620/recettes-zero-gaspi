<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Consent extends Model
{
    protected $fillable = [
        'user_id',
        'consent_type',
        'version',
        'consented_at',
        'ip_address',
        'user_agent',
        'revoked_at',
    ];

    protected $casts = [
        'consented_at' => 'datetime',
        'revoked_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeActive($query)
    {
        return $query->whereNull('revoked_at');
    }

    public function scopeRevoked($query)
    {
        return $query->whereNotNull('revoked_at');
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('consent_type', $type);
    }

    public function scopeForUser($query, User $user)
    {
        return $query->where('user_id', $user->id);
    }

    public function isActive(): bool
    {
        return is_null($this->revoked_at);
    }

    public function revoke(): bool
    {
        $this->revoked_at = now();
        return $this->save();
    }
}

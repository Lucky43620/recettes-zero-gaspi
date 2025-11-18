<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeletedUserAudit extends Model
{
    protected $table = 'deleted_users_audit';

    protected $fillable = [
        'original_user_id',
        'deletion_date',
        'stripe_customer_id',
        'total_spent',
        'subscription_history',
        'legal_retention_until',
        'deleted_by',
    ];

    protected $casts = [
        'deletion_date' => 'datetime',
        'legal_retention_until' => 'date',
        'total_spent' => 'decimal:2',
        'subscription_history' => 'array',
    ];

    public function scopePendingCleanup($query)
    {
        return $query->where('legal_retention_until', '<', now());
    }

    public function scopeRetentionValid($query)
    {
        return $query->where('legal_retention_until', '>=', now());
    }

    public function isEligibleForCleanup(): bool
    {
        return $this->legal_retention_until->isPast();
    }
}

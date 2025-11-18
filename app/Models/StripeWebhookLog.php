<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StripeWebhookLog extends Model
{
    protected $fillable = [
        'event_id',
        'event_type',
        'payload',
        'processed_at',
        'status',
        'error_message',
    ];

    protected $casts = [
        'payload' => 'array',
        'processed_at' => 'datetime',
    ];

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeSuccess($query)
    {
        return $query->where('status', 'success');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('event_type', $type);
    }

    public function markAsProcessed(): bool
    {
        $this->status = 'success';
        $this->processed_at = now();
        return $this->save();
    }

    public function markAsFailed(string $errorMessage): bool
    {
        $this->status = 'failed';
        $this->error_message = $errorMessage;
        $this->processed_at = now();
        return $this->save();
    }
}

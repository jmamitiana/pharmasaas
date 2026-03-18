<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subscription extends Model
{
    protected $fillable = [
        'tenant_id',
        'plan_name',
        'price',
        'duration_days',
        'start_date',
        'end_date',
        'status',
        'payment_method',
        'transaction_id',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'price' => 'decimal:2',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active' 
            && $this->end_date 
            && $this->end_date->gte(now()->toDateString());
    }

    public function daysRemaining(): int
    {
        if (!$this->end_date) {
            return 0;
        }
        return max(0, now()->diffInDays($this->end_date));
    }
}

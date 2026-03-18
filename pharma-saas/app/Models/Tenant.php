<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tenant extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'domain',
        'database',
        'logo',
        'address',
        'phone',
        'email',
        'tax_number',
        'license_number',
        'status',
        'subscription_start',
        'subscription_end',
        'subscription_plan',
    ];

    protected $casts = [
        'subscription_start' => 'date',
        'subscription_end' => 'date',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function settings(): HasMany
    {
        return $this->hasMany(Setting::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function isSubscriptionActive(): bool
    {
        return $this->status === 'active' 
            && $this->subscription_end 
            && $this->subscription_end->gte(now());
    }
}

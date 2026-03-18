<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesHistory extends Model
{
    protected $fillable = [
        'tenant_id',
        'product_id',
        'date',
        'quantity_sold',
        'revenue',
        'avg_daily_sales',
        'days_until_stockout',
        'risk_level',
    ];

    protected $casts = [
        'date' => 'date',
        'quantity_sold' => 'decimal:2',
        'revenue' => 'decimal:2',
        'avg_daily_sales' => 'decimal:2',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}

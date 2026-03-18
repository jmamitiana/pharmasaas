<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'key',
        'value',
        'type',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public static function getValue(string $key, $default = null, ?int $tenantId = null)
    {
        $setting = self::where('key', $key)
            ->where('tenant_id', $tenantId)
            ->first();

        if (!$setting) {
            return $default;
        }

        return match ($setting->type) {
            'boolean' => $setting->value === '1' || $setting->value === 'true',
            'integer' => (int) $setting->value,
            'float' => (float) $setting->value,
            'json' => json_decode($setting->value, true),
            default => $setting->value,
        };
    }

    public static function setValue(string $key, $value, ?int $tenantId = null, string $type = 'string'): void
    {
        $value = match ($type) {
            'boolean' => $value ? 'true' : 'false',
            'json' => json_encode($value),
            default => (string) $value,
        };

        self::updateOrCreate(
            ['key' => $key, 'tenant_id' => $tenantId],
            ['value' => $value, 'type' => $type]
        );
    }
}

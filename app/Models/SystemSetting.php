<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class SystemSetting extends Model
{
    use HasFactory, Auditable;

    protected $fillable = [
        'key',
        'value',
        'description',
    ];

    public static function getValue(string $key, ?string $default = null): ?string
    {
        return static::where('key', $key)->value('value') ?? $default;
    }

    public static function getInt(string $key, int $default): int
    {
        return (int) (static::getValue($key, (string) $default) ?? $default);
    }
}

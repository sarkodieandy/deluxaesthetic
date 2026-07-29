<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = [
        'group',
        'key',
        'value',
        'type',
        'is_public',
    ];

    protected function casts(): array
    {
        return [
            'is_public' => 'boolean',
        ];
    }

    public static function getValue(string $key, mixed $default = null): mixed
    {
        $payload = Cache::remember("setting.{$key}", 3600, function () use ($key) {
            $setting = static::query()->where('key', $key)->first();

            if (! $setting) {
                return null;
            }

            return [
                'value' => $setting->value,
                'type' => $setting->type,
            ];
        });

        if (! is_array($payload)) {
            return $default;
        }

        return static::castValue($payload['value'] ?? null, $payload['type'] ?? null) ?? $default;
    }

    public static function setValue(string $key, mixed $value, string $group = 'general', string $type = 'string'): self
    {
        $setting = static::query()->updateOrCreate(
            ['key' => $key],
            [
                'group' => $group,
                'type' => $type,
                'value' => is_array($value) || is_object($value)
                    ? json_encode($value)
                    : (string) $value,
            ],
        );

        Cache::forget("setting.{$key}");

        return $setting;
    }

    protected static function castValue(?string $value, ?string $type): mixed
    {
        return match ($type) {
            'boolean', 'bool' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'integer', 'int' => (int) $value,
            'float', 'decimal' => (float) $value,
            'json', 'array' => json_decode($value ?? 'null', true),
            default => $value,
        };
    }
}

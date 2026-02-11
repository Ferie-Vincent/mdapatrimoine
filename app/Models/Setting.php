<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $primaryKey = 'key';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = ['key', 'value'];

    /**
     * In-memory cache to avoid repeated DB queries within a single request.
     */
    private static array $cache = [];

    /**
     * Get a setting value by key with an optional default.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        if (array_key_exists($key, static::$cache)) {
            return static::$cache[$key] ?? $default;
        }

        $setting = static::find($key);

        $value = $setting?->value;
        static::$cache[$key] = $value;

        return $value !== null && $value !== '' ? $value : $default;
    }

    /**
     * Set a setting value by key.
     */
    public static function set(string $key, mixed $value): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => $value],
        );

        static::$cache[$key] = $value;
    }

    /**
     * Get all settings as a key-value array.
     */
    public static function allAsArray(): array
    {
        $settings = static::all()->pluck('value', 'key')->toArray();
        static::$cache = $settings;

        return $settings;
    }

    /**
     * Clear the in-memory cache.
     */
    public static function clearCache(): void
    {
        static::$cache = [];
    }
}

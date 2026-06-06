<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    /** @var array<string, mixed> */
    protected static array $cache = [];

    protected $fillable = [
        'key',
        'value',
    ];

    public static function get(string $key, mixed $default = null): mixed
    {
        if (array_key_exists($key, static::$cache)) {
            return static::$cache[$key];
        }

        $setting = static::query()->where('key', $key)->first();
        $value = $setting?->value ?? $default;
        static::$cache[$key] = $value;

        return $value;
    }

    public static function set(string $key, mixed $value): void
    {
        static::query()->updateOrCreate(
            ['key' => $key],
            ['value' => $value === null ? null : (string) $value],
        );

        static::$cache[$key] = $value === null ? null : (string) $value;
    }

    /**
     * @param  array<int, string>  $keys
     * @param  array<string, mixed>  $defaults
     * @return array<string, mixed>
     */
    public static function getMany(array $keys, array $defaults = []): array
    {
        $result = [];

        foreach ($keys as $key) {
            $result[$key] = static::get($key, $defaults[$key] ?? null);
        }

        return $result;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function setMany(array $data): void
    {
        foreach ($data as $key => $value) {
            static::set($key, $value);
        }
    }

    public static function flushCache(): void
    {
        static::$cache = [];
    }

    public static function isTruthy(string $key): bool
    {
        return filter_var(static::get($key), FILTER_VALIDATE_BOOLEAN);
    }
}

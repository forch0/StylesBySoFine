<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SiteSetting extends Model
{
    protected $fillable = ['key', 'value', 'group'];

    public static function get(string $key, $default = null)
    {
        return Cache::rememberForever("setting.{$key}", function () use ($key, $default) {
            return static::where('key', $key)->value('value') ?? $default;
        });
    }

    public static function set(string $key, $value, string $group = 'general'): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value, 'group' => $group]);
        Cache::forget("setting.{$key}");
    }

    public static function getMany(array $keys): array
    {
        $result = [];
        foreach ($keys as $key => $default) {
            $k = is_int($key) ? $default : $key;
            $result[$k] = static::get($k, is_int($key) ? null : $default);
        }

        return $result;
    }

    protected static function booted(): void
    {
        static::saved(fn (SiteSetting $s) => Cache::forget("setting.{$s->key}"));
    }
}

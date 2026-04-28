<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class SettingCache
{
    /** Cache TTL in seconds (5 minutes). */
    private const TTL = 300;

    private const CACHE_KEY = 'app_settings';

    /**
     * Get one setting value by key, with a fallback default.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        return static::all()->get($key, $default);
    }

    /**
     * Return all settings as a key→value Collection (cached for TTL seconds).
     */
    public static function all(): \Illuminate\Support\Collection
    {
        return Cache::remember(static::CACHE_KEY, static::TTL, function () {
            return Setting::pluck('value', 'key');
        });
    }

    /**
     * Invalidate the settings cache (call after any setting is updated).
     */
    public static function flush(): void
    {
        Cache::forget(static::CACHE_KEY);
    }
}

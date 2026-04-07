<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value', 'group'];

    // ── Get a setting value by key (with default) ─────────────
    public static function get(string $key, $default = null): mixed
    {
        return Cache::remember("setting_{$key}", 3600, function () use ($key, $default) {
            $setting = static::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        });
    }

    // ── Set / upsert a setting ─────────────────────────────────
    public static function set(string $key, $value, string $group = 'general'): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'group' => $group]
        );
        Cache::forget("setting_{$key}");
    }

    // ── Get all settings as group → [key => value] map ────────
    public static function allGrouped(): array
    {
        return static::all()->groupBy('group')->map(fn($g) => $g->pluck('value', 'key'))->toArray();
    }

    // ── Helper: get all as flat [key => value] ─────────────────
    public static function allFlat(): array
    {
        return static::pluck('value', 'key')->toArray();
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

class HomepagePreset extends Model
{
    public const KEYS = [
        'editorial' => 'Editorial / Luxury',
        'boutique' => 'Modern Boutique',
        'social' => 'Social / Trend-led',
    ];

    protected $fillable = ['key', 'name', 'description', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function sections(): HasMany
    {
        return $this->hasMany(HomepageSection::class)->orderBy('sort_order');
    }

    protected static function booted(): void
    {
        $flush = fn () => Cache::forget('home.preset');
        static::saved($flush);
        static::deleted($flush);
    }

    public static function active(): ?self
    {
        return static::where('is_active', true)->first() ?? static::where('key', 'editorial')->first();
    }
}

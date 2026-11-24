<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

class NavigationItem extends Model
{
    protected $fillable = ['label', 'url', 'parent_id', 'sort_order', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    protected static function booted(): void
    {
        $flush = fn () => Cache::forget('nav.items');
        static::saved($flush);
        static::deleted($flush);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(NavigationItem::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(NavigationItem::class, 'parent_id')->orderBy('sort_order');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }

    public function resolvedUrl(): string
    {
        $url = $this->url;

        if (str_starts_with($url, 'route:')) {
            $route = substr($url, 6);
            return app('router')->has($route) ? route($route) : '#';
        }

        return $url;
    }
}

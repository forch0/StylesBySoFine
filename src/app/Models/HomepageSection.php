<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;

class HomepageSection extends Model
{
    protected $fillable = [
        'homepage_preset_id', 'key', 'name', 'sort_order', 'is_enabled', 'content',
    ];

    protected static function booted(): void
    {
        $flush = fn () => Cache::forget('home.preset');
        static::saved($flush);
        static::deleted($flush);
    }

    protected $casts = [
        'is_enabled' => 'boolean',
        'content' => 'array',
    ];

    public function preset(): BelongsTo
    {
        return $this->belongsTo(HomepagePreset::class, 'homepage_preset_id');
    }

    public function get(string $field, $default = null)
    {
        return data_get($this->content, $field, $default);
    }
}

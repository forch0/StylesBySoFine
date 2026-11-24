<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductMedia extends Model
{
    protected $table = 'product_media';

    protected $fillable = [
        'product_id', 'type', 'source', 'path', 'poster', 'alt', 'sort_order', 'is_primary',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function isVideo(): bool
    {
        return $this->type === 'video';
    }

    public function url(): string
    {
        if ($this->source === 'url' || Str::startsWith($this->path, ['http://', 'https://'])) {
            return $this->path;
        }

        return Storage::url($this->path);
    }

    public function posterUrl(): ?string
    {
        if (! $this->poster) {
            return null;
        }

        return Str::startsWith($this->poster, ['http://', 'https://'])
            ? $this->poster
            : Storage::url($this->poster);
    }

    /** YouTube/Vimeo embed URL, or null for direct video files. */
    public function embedUrl(): ?string
    {
        if (! $this->isVideo() || $this->source !== 'url') {
            return null;
        }

        if (preg_match('{(?:youtube\.com/(?:watch\?v=|shorts/)|youtu\.be/)([\w-]+)}', $this->path, $m)) {
            return 'https://www.youtube.com/embed/'.$m[1];
        }

        if (preg_match('{vimeo\.com/(\d+)}', $this->path, $m)) {
            return 'https://player.vimeo.com/video/'.$m[1];
        }

        return null; // direct mp4/webm link → use <video> tag
    }
}

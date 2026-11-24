<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class ProductVariant extends Model
{
    protected $fillable = [
        'product_id', 'options', 'sku', 'price', 'stock_quantity',
        'low_stock_threshold', 'is_active', 'image',
    ];

    protected static function booted(): void
    {
        static::creating(function (ProductVariant $variant) {
            $variant->uuid ??= (string) Str::uuid();
        });
    }

    protected $casts = [
        'options' => 'array',
        'is_active' => 'boolean',
        'price' => 'integer',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function movements(): HasMany
    {
        return $this->hasMany(InventoryMovement::class);
    }

    public function effectivePrice(): int
    {
        return $this->price ?? $this->product->price;
    }

    public function summary(): string
    {
        if (empty($this->options)) {
            return 'Default';
        }

        return collect($this->options)->map(fn ($v, $k) => "{$k}: {$v}")->implode(' / ');
    }

    public function inStock(): bool
    {
        return $this->is_active && $this->stock_quantity > 0;
    }

    public function isLowStock(): bool
    {
        $threshold = $this->low_stock_threshold ?? 3;

        return $this->is_active && $this->stock_quantity > 0 && $this->stock_quantity <= $threshold;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Product extends Model
{
    protected $fillable = [
        'category_id', 'name', 'slug', 'short_description', 'description',
        'price', 'compare_at_price', 'sku', 'status',
        'is_featured', 'is_bestseller', 'is_new_arrival',
        'allow_preorder', 'preorder_note',
        'meta_title', 'meta_description', 'care_instructions', 'material', 'weight',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'is_bestseller' => 'boolean',
        'is_new_arrival' => 'boolean',
        'allow_preorder' => 'boolean',
        'price' => 'integer',
        'compare_at_price' => 'integer',
    ];

    protected static function booted(): void
    {
        static::saving(function (Product $product) {
            $product->uuid ??= (string) Str::uuid();
            if (blank($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function collections(): BelongsToMany
    {
        return $this->belongsToMany(Collection::class, 'collection_product');
    }

    public function media(): HasMany
    {
        return $this->hasMany(ProductMedia::class)->orderBy('sort_order');
    }

    public function images(): HasMany
    {
        return $this->media()->where('type', 'image');
    }

    public function videos(): HasMany
    {
        return $this->media()->where('type', 'video');
    }

    public function primaryMedia()
    {
        return $this->hasOne(ProductMedia::class)->where('is_primary', true);
    }

    public function options(): HasMany
    {
        return $this->hasMany(ProductOption::class)->orderBy('position');
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function hasVariants(): bool
    {
        return $this->variants()->exists();
    }

    public function displayPrice(): int
    {
        return $this->price;
    }

    public function onSale(): bool
    {
        return $this->compare_at_price !== null && $this->compare_at_price > $this->price;
    }

    public function inStock(): bool
    {
        return $this->variants()->where('is_active', true)->where('stock_quantity', '>', 0)->exists();
    }

    public function totalStock(): int
    {
        return (int) $this->variants()->where('is_active', true)->sum('stock_quantity');
    }
}

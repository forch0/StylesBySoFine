<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Coupon extends Model
{
    protected $fillable = [
        'code', 'type', 'value', 'min_order_value', 'usage_limit',
        'per_customer_limit', 'starts_at', 'ends_at', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::saving(fn (Coupon $coupon) => $coupon->code = Str::upper(trim($coupon->code)));
    }

    public function redemptions(): HasMany
    {
        return $this->hasMany(CouponRedemption::class);
    }

    public function isCurrentlyValid(): bool
    {
        if (! $this->is_active) {
            return false;
        }
        if ($this->starts_at && $this->starts_at->isFuture()) {
            return false;
        }
        if ($this->ends_at && $this->ends_at->isPast()) {
            return false;
        }
        if ($this->usage_limit && $this->redemptions()->count() >= $this->usage_limit) {
            return false;
        }

        return true;
    }

    /** Discount in kobo for a given subtotal (kobo). */
    public function discountFor(int $subtotal): int
    {
        return match ($this->type) {
            'percentage' => (int) round($subtotal * min($this->value, 100) / 100),
            'fixed' => min($this->value, $subtotal),
            default => 0,
        };
    }
}

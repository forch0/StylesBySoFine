<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Order extends Model
{
    public const PAYMENT_PENDING = 'pending';
    public const PAYMENT_PAID = 'paid';
    public const PAYMENT_FAILED = 'failed';
    public const PAYMENT_ABANDONED = 'abandoned';
    public const PAYMENT_REFUNDED = 'refunded';

    public const FULFIL_UNFULFILLED = 'unfulfilled';
    public const FULFIL_PROCESSING = 'processing';
    public const FULFIL_READY = 'ready';
    public const FULFIL_SHIPPED = 'shipped';
    public const FULFIL_DELIVERED = 'delivered';
    public const FULFIL_CANCELLED = 'cancelled';

    public const CHANNEL_ONLINE = 'online';
    public const CHANNEL_POS = 'pos';

    public const METHODS = [
        'paystack' => 'Paystack',
        'cash' => 'Cash',
        'transfer' => 'Bank Transfer',
        'pos_terminal' => 'POS Terminal',
        'other' => 'Other',
    ];

    protected $fillable = [
        'order_number', 'user_id', 'sold_by_user_id', 'channel', 'payment_method',
        'customer_name', 'customer_email', 'customer_phone',
        'delivery_address', 'city', 'state', 'delivery_instructions', 'order_notes',
        'subtotal', 'delivery_fee', 'discount', 'total', 'currency',
        'payment_status', 'fulfilment_status', 'payment_reference',
        'coupon_id', 'delivery_zone_id', 'tracking_token',
    ];

    protected static function booted(): void
    {
        static::creating(function (Order $order) {
            $order->order_number ??= 'SBF-'.strtoupper(Str::random(8));
            $order->tracking_token ??= Str::random(48);
        });
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function latestPayment(): HasOne
    {
        return $this->hasOne(Payment::class)->latestOfMany();
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    public function deliveryZone(): BelongsTo
    {
        return $this->belongsTo(DeliveryZone::class);
    }

    public function soldBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sold_by_user_id');
    }

    public function isPos(): bool
    {
        return $this->channel === self::CHANNEL_POS;
    }

    public function customerDisplayName(): string
    {
        return $this->customer_name ?: 'Walk-in Customer';
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class);
    }

    public function isPaid(): bool
    {
        return $this->payment_status === self::PAYMENT_PAID;
    }

    public function recordStatusChange(string $field, ?string $from, string $to, ?int $userId = null, ?string $note = null): void
    {
        $this->statusHistories()->create([
            'field' => $field,
            'from' => $from,
            'to' => $to,
            'user_id' => $userId,
            'note' => $note,
        ]);
    }
}

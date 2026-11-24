<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cart extends Model
{
    protected $fillable = ['session_id', 'user_id'];

    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subtotal(): int
    {
        return (int) $this->items->sum(fn (CartItem $item) => $item->unit_price * $item->quantity);
    }

    public function itemCount(): int
    {
        return (int) $this->items->sum('quantity');
    }
}

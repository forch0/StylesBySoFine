<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Wishlist extends Model
{
    protected $fillable = ['session_id', 'user_id'];

    public function items(): HasMany
    {
        return $this->hasMany(WishlistItem::class);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'wishlist_items');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DeliveryZone extends Model
{
    protected $fillable = ['name', 'states', 'fee', 'estimated_delivery', 'is_active', 'sort_order'];

    protected $casts = ['is_active' => 'boolean', 'fee' => 'integer'];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }

    public function stateList(): array
    {
        if (! $this->states) {
            return [];
        }

        return array_values(array_filter(array_map('trim', explode(',', $this->states))));
    }
}

<?php

namespace App\Services;

use App\Models\ProductVariant;

class InventoryService
{
    /**
     * Adjust variant stock and record the movement.
     * Callers must run this inside a transaction with the variant row locked.
     */
    public function adjust(ProductVariant $variant, int $change, string $reason, ?int $orderId = null, ?int $userId = null): void
    {
        $variant->stock_quantity += $change;
        $variant->save();

        $variant->movements()->create([
            'change' => $change,
            'quantity_after' => $variant->stock_quantity,
            'reason' => $reason,
            'order_id' => $orderId,
            'user_id' => $userId,
        ]);
    }
}

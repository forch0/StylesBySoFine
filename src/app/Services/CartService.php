<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductVariant;

class CartService
{
    public function currentCart(): Cart
    {
        $sessionId = session()->getId();

        return Cart::firstOrCreate(['session_id' => $sessionId]);
    }

    public function add(Product $product, ?ProductVariant $variant, int $quantity = 1): CartItem
    {
        $cart = $this->currentCart();

        // Products with variants require a variant selection
        if ($product->hasVariants() && ! $variant) {
            throw new \InvalidArgumentException('Please select an option first.');
        }

        $variant ??= $product->variants()->where('is_active', true)->first();

        if (! $variant || ! $variant->is_active) {
            throw new \InvalidArgumentException('This option is unavailable.');
        }

        $existing = $cart->items()
            ->where('product_id', $product->id)
            ->where('product_variant_id', $variant->id)
            ->first();

        $newQty = ($existing?->quantity ?? 0) + $quantity;

        if ($newQty > $variant->stock_quantity && ! $product->allow_preorder) {
            throw new \InvalidArgumentException("Only {$variant->stock_quantity} left in stock.");
        }

        if ($existing) {
            $existing->update(['quantity' => $newQty, 'unit_price' => $variant->effectivePrice()]);

            return $existing;
        }

        return $cart->items()->create([
            'product_id' => $product->id,
            'product_variant_id' => $variant->id,
            'quantity' => $quantity,
            'unit_price' => $variant->effectivePrice(),
        ]);
    }

    public function updateQuantity(CartItem $item, int $quantity): void
    {
        if ($quantity < 1) {
            $item->delete();

            return;
        }

        $variant = $item->variant;
        if ($variant && $quantity > $variant->stock_quantity && ! $item->product?->allow_preorder) {
            throw new \InvalidArgumentException("Only {$variant->stock_quantity} left in stock.");
        }

        $item->update(['quantity' => $quantity]);
    }

    public function remove(CartItem $item): void
    {
        $item->delete();
    }

    public function clear(): void
    {
        $this->currentCart()->items()->delete();
    }

    /** Re-validate every line against live stock/pricing. Returns problems found. */
    public function validate(): array
    {
        $problems = [];
        $cart = $this->currentCart()->load('items.product', 'items.variant');

        foreach ($cart->items as $item) {
            if (! $item->product || $item->product->status !== 'active') {
                $problems[] = "{$item->product?->name} is no longer available.";
                continue;
            }
            if ($item->variant && (! $item->variant->is_active
                || ($item->variant->stock_quantity < $item->quantity && ! $item->product->allow_preorder))) {
                $problems[] = "{$item->product->name} ({$item->variant->summary()}) has insufficient stock.";
            }
        }

        return $problems;
    }
}

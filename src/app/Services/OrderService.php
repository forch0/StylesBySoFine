<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Coupon;
use App\Models\DeliveryZone;
use App\Models\Order;
use App\Models\Payment;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class OrderService
{
    public function __construct(protected InventoryService $inventory) {}

    /**
     * Create a pending online order from the cart. Totals are computed
     * server-side; stock is re-checked but not yet deducted.
     */
    public function createFromCart(Cart $cart, array $customer, ?DeliveryZone $zone, ?Coupon $coupon): Order
    {
        $cart->loadMissing('items.product', 'items.variant');

        if ($cart->items->isEmpty()) {
            throw new RuntimeException('Your cart is empty.');
        }

        return DB::transaction(function () use ($cart, $customer, $zone, $coupon) {
            // Lock all variant rows and re-validate stock
            $subtotal = 0;
            $lines = [];

            foreach ($cart->items as $item) {
                $variant = ProductVariant::where('id', $item->product_variant_id)->lockForUpdate()->first();

                if (! $variant || ! $variant->is_active
                    || ($variant->stock_quantity < $item->quantity && ! $item->product->allow_preorder)) {
                    throw new RuntimeException("{$item->product->name} has insufficient stock.");
                }

                $unitPrice = $variant->effectivePrice();
                $lines[] = [
                    'product_id' => $item->product_id,
                    'product_variant_id' => $variant->id,
                    'product_name' => $item->product->name,
                    'variant_summary' => $variant->summary() !== 'Default' ? $variant->summary() : null,
                    'unit_price' => $unitPrice,
                    'quantity' => $item->quantity,
                    'total' => $unitPrice * $item->quantity,
                    'is_preorder' => $item->product->allow_preorder && $variant->stock_quantity < $item->quantity,
                ];
                $subtotal += $unitPrice * $item->quantity;
            }

            $discount = 0;
            if ($coupon) {
                if (! $coupon->isCurrentlyValid()) {
                    throw new RuntimeException('That coupon is no longer valid.');
                }
                if ($coupon->min_order_value && $subtotal < $coupon->min_order_value) {
                    throw new RuntimeException('Order does not meet the coupon minimum.');
                }
                $discount = $coupon->discountFor($subtotal);
            }

            // Delivery is charged separately: the zone is recorded for the
            // quote, but the fee is collected offline (rider or transfer).
            $deliveryFee = 0;

            $order = Order::create([
                'channel' => Order::CHANNEL_ONLINE,
                'customer_name' => $customer['name'],
                'customer_email' => strtolower($customer['email']),
                'customer_phone' => $customer['phone'],
                'delivery_address' => $customer['address'],
                'city' => $customer['city'],
                'state' => $customer['state'],
                'delivery_instructions' => $customer['delivery_instructions'] ?? null,
                'order_notes' => $customer['notes'] ?? null,
                'subtotal' => $subtotal,
                'delivery_fee' => $deliveryFee,
                'discount' => $discount,
                'total' => $subtotal - $discount + $deliveryFee,
                'currency' => 'NGN',
                'payment_status' => Order::PAYMENT_PENDING,
                'fulfilment_status' => Order::FULFIL_UNFULFILLED,
                'coupon_id' => $coupon?->id,
                'delivery_zone_id' => $zone?->id,
            ]);

            $order->items()->createMany($lines);

            if ($coupon) {
                $coupon->redemptions()->create([
                    'order_id' => $order->id,
                    'customer_email' => $customer['email'],
                ]);
            }

            return $order;
        });
    }

    /**
     * Record a completed in-person (POS) sale. Deducts stock immediately
     * and marks the order paid via the given offline method.
     */
    public function createPosSale(array $lines, array $sale, int $staffUserId): Order
    {
        return DB::transaction(function () use ($lines, $sale, $staffUserId) {
            $subtotal = 0;
            $orderLines = [];

            foreach ($lines as $line) {
                $variant = ProductVariant::where('id', $line['variant_id'])->lockForUpdate()->firstOrFail();
                $product = $variant->product;

                if (! $variant->is_active || $variant->stock_quantity < $line['quantity']) {
                    throw new RuntimeException("{$product->name} ({$variant->summary()}) has insufficient stock.");
                }

                $unitPrice = $variant->effectivePrice();
                $orderLines[] = [
                    'product_id' => $product->id,
                    'product_variant_id' => $variant->id,
                    'product_name' => $product->name,
                    'variant_summary' => $variant->summary() !== 'Default' ? $variant->summary() : null,
                    'unit_price' => $unitPrice,
                    'quantity' => $line['quantity'],
                    'total' => $unitPrice * $line['quantity'],
                    'variant' => $variant,
                ];
                $subtotal += $unitPrice * $line['quantity'];
            }

            $discount = max(0, (int) ($sale['discount'] ?? 0));
            $discount = min($discount, $subtotal);

            $order = Order::create([
                'channel' => Order::CHANNEL_POS,
                'payment_method' => $sale['payment_method'],
                'sold_by_user_id' => $staffUserId,
                'customer_name' => $sale['customer_name'] ?: 'Walk-in Customer',
                'customer_email' => $sale['customer_email'] ?? null,
                'customer_phone' => $sale['customer_phone'] ?? null,
                'order_notes' => $sale['notes'] ?? null,
                'subtotal' => $subtotal,
                'delivery_fee' => 0,
                'discount' => $discount,
                'total' => $subtotal - $discount,
                'currency' => 'NGN',
                'payment_status' => Order::PAYMENT_PAID,
                'fulfilment_status' => Order::FULFIL_DELIVERED,
            ]);

            foreach ($orderLines as $line) {
                $variant = $line['variant'];
                unset($line['variant']);
                $order->items()->create($line);
                $this->inventory->adjust($variant, -$line['quantity'], 'POS sale '.$order->order_number, $order->id, $staffUserId);
            }

            $order->recordStatusChange('payment_status', null, Order::PAYMENT_PAID, $staffUserId, 'POS sale ('.$sale['payment_method'].')');

            return $order;
        });
    }

    /**
     * Mark an order paid after verified payment. Deducts stock once —
     * safe to call multiple times (idempotent). Returns true only when
     * this call actually transitioned the order to paid.
     */
    public function markPaid(Order $order, Payment $payment, string $method = 'paystack'): bool
    {
        $marked = false;

        DB::transaction(function () use ($order, $payment, $method, &$marked) {
            $order = Order::where('id', $order->id)->lockForUpdate()->firstOrFail();

            if ($order->payment_status === Order::PAYMENT_PAID) {
                return; // already processed (duplicate webhook/callback)
            }

            $from = $order->payment_status;
            $order->update([
                'payment_status' => Order::PAYMENT_PAID,
                'payment_method' => $method,
                'payment_reference' => $payment->reference,
            ]);
            $order->recordStatusChange('payment_status', $from, Order::PAYMENT_PAID, null, 'Payment verified ('.$payment->reference.')');

            foreach ($order->items as $item) {
                if (! $item->product_variant_id) {
                    continue;
                }
                $variant = ProductVariant::where('id', $item->product_variant_id)->lockForUpdate()->first();
                if ($variant) {
                    $this->inventory->adjust($variant, -$item->quantity, 'Order '.$order->order_number, $order->id);
                }
            }

            $marked = true;
        });

        return $marked;
    }

    public function markPaymentFailed(Order $order, string $status = Order::PAYMENT_FAILED): void
    {
        if ($order->payment_status === Order::PAYMENT_PAID) {
            return;
        }

        $from = $order->payment_status;
        $order->update(['payment_status' => $status]);
        $order->recordStatusChange('payment_status', $from, $status);
    }

    public function generateReference(): string
    {
        return 'SBF-'.now()->format('Ymd').'-'.strtoupper(Str::random(10));
    }
}

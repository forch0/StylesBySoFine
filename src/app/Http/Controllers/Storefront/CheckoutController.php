<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\DeliveryZone;
use App\Models\Payment;
use App\Services\CartService;
use App\Services\OrderService;
use App\Services\PaystackService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function __construct(
        protected CartService $cart,
        protected OrderService $orders,
        protected PaystackService $paystack,
    ) {}

    public function index()
    {
        $cart = $this->cart->currentCart()->load('items.product.media', 'items.variant');

        if ($cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your bag is empty.');
        }

        $problems = $this->cart->validate();

        return view('storefront.checkout', [
            'cart' => $cart,
            'zones' => DeliveryZone::active()->get(),
            'problems' => $problems,
            'coupon' => session('coupon_code') ? Coupon::where('code', session('coupon_code'))->first() : null,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:120',
            'email' => 'required|email|max:160',
            'phone' => 'required|string|max:30',
            'address' => 'required|string|max:500',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'delivery_zone_id' => 'required|exists:delivery_zones,id',
            'delivery_instructions' => 'nullable|string|max:500',
            'notes' => 'nullable|string|max:500',
            'coupon_code' => 'nullable|string|max:50',
        ]);

        $cart = $this->cart->currentCart()->load('items');

        if (! $this->paystack->isConfigured()) {
            return back()->withInput()
                ->with('error', 'Online payments are not configured yet. Please contact the store to place your order.');
        }

        $zone = DeliveryZone::active()->findOrFail($data['delivery_zone_id']);

        $coupon = null;
        if ($code = Str::upper(trim($data['coupon_code'] ?? ''))) {
            $coupon = Coupon::where('code', $code)->first();
            if (! $coupon || ! $coupon->isCurrentlyValid()) {
                return back()->withInput()->with('error', 'That coupon code is not valid.');
            }
        }

        try {
            $order = $this->orders->createFromCart($cart, $data, $zone, $coupon);
        } catch (\RuntimeException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        $reference = $this->orders->generateReference();

        Payment::create([
            'order_id' => $order->id,
            'reference' => $reference,
            'amount' => $order->total,
            'currency' => 'NGN',
            'status' => 'pending',
        ]);

        try {
            $init = $this->paystack->initialize(
                email: $order->customer_email,
                amountKobo: $order->total,
                reference: $reference,
                callbackUrl: route('payment.callback'),
                metadata: ['order_id' => $order->id, 'order_number' => $order->order_number],
            );
        } catch (\RuntimeException $e) {
            return back()->withInput()->with('error', 'Payment could not be started. Please try again.');
        }

        $order->update(['payment_reference' => $reference]);

        return redirect()->away($init['authorization_url']);
    }
}

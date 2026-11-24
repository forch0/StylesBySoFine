<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /** Confirmation page — requires the order's tracking token or an email match. */
    public function confirmation(Request $request, string $orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)->with('items')->firstOrFail();

        // Confirmation is only shown right after checkout redirect or with the token
        $allowed = session('last_order_id') === $order->id
            || $request->query('t') === $order->tracking_token;

        abort_unless($allowed, 403);

        return view('storefront.order-confirmation', compact('order'));
    }

    public function trackForm()
    {
        return view('storefront.track');
    }

    public function track(Request $request)
    {
        $data = $request->validate([
            'order_number' => 'required|string',
            'email' => 'required|email',
        ]);

        $order = Order::where('order_number', strtoupper(trim($data['order_number'])))
            ->where('customer_email', strtolower(trim($data['email'])))
            ->with('items', 'statusHistories')
            ->first();

        if (! $order) {
            return back()->withInput()->with('error', 'No order found for those details.');
        }

        return view('storefront.track', compact('order'));
    }
}

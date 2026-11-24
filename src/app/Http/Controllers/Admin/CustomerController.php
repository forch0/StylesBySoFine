<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;
use App\Models\Order;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        // Group every order that has an email — the email is the customer identity.
        $orders = Order::whereNotNull('customer_email')
            ->orderByDesc('created_at')
            ->get()
            ->groupBy(fn ($o) => strtolower($o->customer_email));

        $subscriberEmails = NewsletterSubscriber::where('is_active', true)
            ->pluck('email')->map(fn ($e) => strtolower($e))->flip();

        $customers = $orders->map(function ($group, $email) use ($subscriberEmails) {
            $latest = $group->first();
            $paid = $group->where('payment_status', Order::PAYMENT_PAID);

            return (object) [
                'email' => $email,
                'name' => $latest->customer_name,
                'phone' => $group->firstWhere('customer_phone', '!=', null)?->customer_phone,
                'orders_count' => $group->count(),
                'total_spent' => $paid->sum('total'),
                'last_order_at' => $latest->created_at,
                'subscriber' => $subscriberEmails->has($email),
            ];
        })
        ->when($request->q, fn ($c) => $c->filter(fn ($cust) =>
            str_contains(strtolower($cust->email.' '.$cust->name.' '.$cust->phone), strtolower($request->q))))
        ->sortByDesc('last_order_at')
        ->values();

        return view('admin.customers.index', compact('customers'));
    }

    public function show(string $email)
    {
        $email = strtolower($email);
        $orders = Order::whereRaw('LOWER(customer_email) = ?', [$email])
            ->orderByDesc('created_at')->get();

        abort_if($orders->isEmpty(), 404);

        $customer = (object) [
            'email' => $email,
            'name' => $orders->first()->customer_name,
            'phone' => $orders->firstWhere('customer_phone', '!=', null)?->customer_phone,
            'orders_count' => $orders->count(),
            'total_spent' => $orders->where('payment_status', Order::PAYMENT_PAID)->sum('total'),
            'first_order_at' => $orders->last()->created_at,
            'last_order_at' => $orders->first()->created_at,
            'subscriber' => NewsletterSubscriber::where('email', $email)->where('is_active', true)->exists(),
            'addresses' => $orders->whereNotNull('delivery_address')
                ->map(fn ($o) => implode(', ', array_filter([$o->delivery_address, $o->city, $o->state])))
                ->unique()->values(),
        ];

        return view('admin.customers.show', compact('customer', 'orders'));
    }
}

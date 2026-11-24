@extends('layouts.storefront')

@section('title', 'Order '.$order->order_number.' — '.($settings['store_name'] ?? ''))
@section('robots', 'noindex, nofollow')

@section('content')
<div class="section-pad py-14 max-w-3xl mx-auto text-center">
    @if($order->isPaid())
        <div class="w-16 h-16 mx-auto mb-6 rounded-full bg-wine-700 flex items-center justify-center">
            <svg class="w-8 h-8 text-cream" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
        </div>
        <h1 class="heading-display mb-3">Thank you, {{ Str::before($order->customer_name ?? 'friend', ' ') }}.</h1>
        <p class="text-neutral-600 mb-8">Your order <span class="font-semibold text-ink">{{ $order->order_number }}</span> is confirmed and being prepared. We'll WhatsApp you the delivery cost for your area shortly.</p>
    @else
        <h1 class="heading-display mb-3">Order {{ $order->order_number }}</h1>
        <p class="text-neutral-600 mb-8">Payment status: <span class="font-semibold capitalize">{{ $order->payment_status }}</span></p>
    @endif

    <div class="card p-6 text-left">
        <div class="space-y-3 mb-5">
            @foreach($order->items as $item)
                <div class="flex justify-between text-sm">
                    <span>{{ $item->product_name }} × {{ $item->quantity }}
                        @if($item->variant_summary)<span class="block text-xs text-neutral-500">{{ $item->variant_summary }}</span>@endif
                        @if($item->is_preorder)<span class="block text-[10px] font-semibold text-wine-700 uppercase tracking-wider">Pre-order</span>@endif
                    </span>
                    <span>{{ money($item->total) }}</span>
                </div>
            @endforeach
        </div>
        <div class="border-t border-neutral-200 pt-3 space-y-1.5 text-sm">
            <div class="flex justify-between"><span>Subtotal</span><span>{{ money($order->subtotal) }}</span></div>
            @if($order->discount > 0)
                <div class="flex justify-between text-wine-700"><span>Discount</span><span>−{{ money($order->discount) }}</span></div>
            @endif
            <div class="flex justify-between"><span>Delivery</span><span class="text-neutral-500">{{ $order->delivery_fee ? money($order->delivery_fee) : 'Quoted separately' }}</span></div>
            <div class="flex justify-between font-semibold text-base pt-2 border-t border-neutral-200">
                <span>Total</span><span>{{ money($order->total) }}</span>
            </div>
        </div>
    </div>

    <div class="mt-8 flex flex-wrap justify-center gap-4">
        <a href="{{ route('shop') }}" class="btn-primary">Continue shopping</a>
        <a href="{{ route('track') }}" class="btn-outline">Track order</a>
    </div>
</div>
@endsection

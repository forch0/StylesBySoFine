@extends('layouts.storefront')

@section('title', 'Track My Order — '.($settings['store_name'] ?? ''))
@section('robots', 'noindex, nofollow')

@section('content')
<div class="section-pad py-14 max-w-2xl mx-auto">
    <h1 class="heading-display mb-3">Track my order</h1>
    <p class="text-neutral-600 mb-8">Enter your order number and the email you used at checkout.</p>

    <form action="{{ route('track.submit') }}" method="POST" class="card p-6 space-y-4 mb-10">
        @csrf
        <div>
            <label class="label" for="order_number">Order number</label>
            <input class="input" id="order_number" name="order_number" value="{{ old('order_number') }}" placeholder="SBF-XXXXXXXX" required>
        </div>
        <div>
            <label class="label" for="email">Email</label>
            <input class="input" id="email" type="email" name="email" value="{{ old('email') }}" required>
        </div>
        <button class="btn-primary w-full">Find my order</button>
    </form>

    @isset($order)
        <div class="card p-6">
            <div class="flex flex-wrap justify-between gap-4 mb-6">
                <div>
                    <p class="eyebrow">Order</p>
                    <p class="font-display text-2xl">{{ $order->order_number }}</p>
                </div>
                <div class="text-right">
                    <p class="eyebrow">Placed</p>
                    <p class="text-sm">{{ $order->created_at->format('j M Y, g:ia') }}</p>
                </div>
            </div>

            <div class="grid sm:grid-cols-2 gap-4 mb-6">
                <div class="bg-neutral-50 p-4">
                    <p class="label">Payment</p>
                    <p class="font-semibold capitalize">{{ $order->payment_status }}</p>
                </div>
                <div class="bg-neutral-50 p-4">
                    <p class="label">Fulfilment</p>
                    <p class="font-semibold capitalize">{{ $order->fulfilment_status }}</p>
                </div>
            </div>

            <div class="space-y-2 mb-6">
                @foreach($order->items as $item)
                    <div class="flex justify-between text-sm">
                        <span>{{ $item->product_name }} × {{ $item->quantity }}
                            @if($item->variant_summary)<span class="text-xs text-neutral-500">({{ $item->variant_summary }})</span>@endif
                        </span>
                        <span>{{ money($item->total) }}</span>
                    </div>
                @endforeach
                <div class="flex justify-between font-semibold pt-2 border-t border-neutral-200">
                    <span>Total</span><span>{{ money($order->total) }}</span>
                </div>
            </div>

            @if($order->statusHistories->isNotEmpty())
                <p class="label mb-3">History</p>
                <ol class="relative border-l border-neutral-200 ml-2 space-y-4">
                    @foreach($order->statusHistories->sortByDesc('created_at') as $h)
                        <li class="ml-4">
                            <span class="absolute -left-1.5 w-3 h-3 rounded-full bg-wine-700"></span>
                            <p class="text-sm"><span class="capitalize font-medium">{{ str_replace('_', ' ', $h->to) }}</span>
                                <span class="text-xs text-neutral-500">— {{ $h->created_at->format('j M Y, g:ia') }}</span></p>
                            @if($h->note)<p class="text-xs text-neutral-500">{{ $h->note }}</p>@endif
                        </li>
                    @endforeach
                </ol>
            @endif
        </div>
    @endisset
</div>
@endsection

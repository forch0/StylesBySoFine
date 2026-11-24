@extends('layouts.admin')

@section('title', 'Order '.$order->order_number)

@section('content')
<div class="grid lg:grid-cols-[1fr_340px] gap-6 items-start">
    <div class="space-y-6">
        {{-- Items --}}
        <div class="card p-5">
            <h2 class="font-display text-lg mb-4">Items</h2>
            @if($order->items->contains('is_preorder', true))
                <div class="rounded-lg bg-wine-50 border border-wine-200 p-3 mb-4 text-xs text-wine-900">
                    This order contains <span class="font-semibold">pre-order items</span> — not yet in stock. Contact the customer with an expected date.
                </div>
            @endif
            <div class="divide-y divide-neutral-100">
                @foreach($order->items as $item)
                    <div class="py-3 flex justify-between text-sm">
                        <span>{{ $item->product_name }} × {{ $item->quantity }}
                            @if($item->variant_summary)<span class="block text-xs text-neutral-500">{{ $item->variant_summary }}</span>@endif
                            @if($item->is_preorder)<span class="block text-[10px] font-semibold text-wine-700 uppercase tracking-wider">Pre-order</span>@endif
                        </span>
                        <span>{{ money($item->total) }}</span>
                    </div>
                @endforeach
            </div>
            <div class="border-t border-neutral-200 mt-4 pt-4 space-y-1.5 text-sm">
                <div class="flex justify-between"><span>Subtotal</span><span>{{ money($order->subtotal) }}</span></div>
                @if($order->discount > 0)<div class="flex justify-between text-wine-700"><span>Discount</span><span>−{{ money($order->discount) }}</span></div>@endif
                <div class="flex justify-between"><span>Delivery</span><span>{{ $order->delivery_fee ? money($order->delivery_fee) : ($order->channel === 'online' ? 'Charged separately' : 'Free') }}</span></div>
                <div class="flex justify-between font-semibold text-base pt-2 border-t border-neutral-200"><span>Total</span><span>{{ money($order->total) }}</span></div>
            </div>
        </div>

        {{-- Customer --}}
        <div class="card p-5">
            <h2 class="font-display text-lg mb-4">Customer</h2>
            <div class="text-sm space-y-1.5">
                <p><span class="text-neutral-500">Name:</span> {{ $order->customerDisplayName() }}</p>
                @if($order->customer_email)<p><span class="text-neutral-500">Email:</span> {{ $order->customer_email }}</p>@endif
                @if($order->customer_phone)<p><span class="text-neutral-500">Phone:</span> {{ $order->customer_phone }}</p>@endif
                @if($order->delivery_address)
                    <p class="pt-2"><span class="text-neutral-500">Address:</span><br>{{ $order->delivery_address }}<br>{{ $order->city }}, {{ $order->state }}</p>
                @endif
                @if($order->delivery_instructions)<p><span class="text-neutral-500">Instructions:</span> {{ $order->delivery_instructions }}</p>@endif
                @if($order->order_notes)<p><span class="text-neutral-500">Notes:</span> {{ $order->order_notes }}</p>@endif
            </div>
        </div>

        {{-- History --}}
        <div class="card p-5">
            <h2 class="font-display text-lg mb-4">History</h2>
            @if($order->statusHistories->isEmpty())
                <p class="text-sm text-neutral-500">No events yet.</p>
            @else
                <ol class="relative border-l border-neutral-200 ml-2 space-y-4">
                    @foreach($order->statusHistories->sortByDesc('created_at') as $h)
                        <li class="ml-4">
                            <span class="absolute -left-1.5 w-3 h-3 rounded-full bg-wine-700"></span>
                            <p class="text-sm"><span class="capitalize font-medium">{{ str_replace('_', ' ', $h->to) }}</span>
                                <span class="text-xs text-neutral-500">— {{ $h->created_at->format('j M, g:ia') }}{{ $h->user ? ' · '.$h->user->name : '' }}</span></p>
                            @if($h->note)<p class="text-xs text-neutral-500">{{ $h->note }}</p>@endif
                        </li>
                    @endforeach
                </ol>
            @endif
        </div>
    </div>

    {{-- Sidebar --}}
    <div class="space-y-6">
        <div class="card p-5 space-y-2 text-sm">
            <p><span class="text-neutral-500">Channel:</span> {{ $order->isPos() ? 'In person (POS)' : 'Online' }}</p>
            <p><span class="text-neutral-500">Payment:</span> <span class="capitalize font-medium">{{ $order->payment_status }}</span>
                @if($order->payment_method)<span class="text-neutral-500">via {{ str_replace('_', ' ', $order->payment_method) }}</span>@endif</p>
            @if($order->payment_reference)<p class="text-xs text-neutral-500">Ref: {{ $order->payment_reference }}</p>@endif
            @if($order->soldBy)<p><span class="text-neutral-500">Sold by:</span> {{ $order->soldBy->name }}</p>@endif
            @if($order->deliveryZone)<p><span class="text-neutral-500">Zone:</span> {{ $order->deliveryZone->name }}</p>@endif
        </div>

        <div class="card p-5">
            <h2 class="font-display text-lg mb-3">Fulfilment</h2>
            <form action="{{ route('admin.orders.fulfilment', $order) }}" method="POST" class="space-y-3">
                @csrf @method('PATCH')
                <select name="fulfilment_status" class="input !py-2">
                    @foreach(['unfulfilled','processing','ready','shipped','delivered','cancelled'] as $s)
                        <option value="{{ $s }}" {{ $order->fulfilment_status === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
                <input name="note" placeholder="Note (optional)" class="input !py-2">
                <button class="btn-primary w-full !py-2 text-sm">Update status</button>
            </form>
        </div>

        <div class="card p-5">
            <h2 class="font-display text-lg mb-3">Internal note</h2>
            <form action="{{ route('admin.orders.notes', $order) }}" method="POST" class="space-y-3">
                @csrf
                <textarea name="note" rows="2" class="input !py-2" required></textarea>
                <button class="btn-outline w-full !py-2 text-sm">Add note</button>
            </form>
        </div>

        @if($order->fulfilment_status !== 'cancelled' && $order->fulfilment_status !== 'delivered')
            <form action="{{ route('admin.orders.cancel', $order) }}" method="POST" onsubmit="return confirm('Cancel this order?{{ $order->isPaid() ? ' Stock will be restored.' : '' }}')" class="card p-5">
                @csrf
                <button class="w-full text-sm text-red-700 underline">Cancel order</button>
            </form>
        @endif
    </div>
</div>
@endsection

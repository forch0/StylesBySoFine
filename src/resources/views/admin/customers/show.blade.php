@extends('layouts.admin')

@section('title', $customer->name ?: $customer->email)

@section('content')
<div class="max-w-4xl">
    <a href="{{ route('admin.customers.index') }}" class="text-sm text-neutral-500 hover:text-wine-700">&larr; All customers</a>

    {{-- Header --}}
    <div class="card p-6 mt-4 flex flex-wrap items-center gap-5">
        <span class="w-14 h-14 rounded-full bg-wine-700 text-cream flex items-center justify-center font-display text-xl shrink-0">{{ strtoupper(substr($customer->name ?: $customer->email, 0, 1)) }}</span>
        <div class="flex-1 min-w-0">
            <h2 class="font-display text-2xl truncate">{{ $customer->name ?: 'Unnamed customer' }}</h2>
            <p class="text-sm text-neutral-500">{{ $customer->email }}{{ $customer->phone ? ' · '.$customer->phone : '' }}</p>
        </div>
        @if($customer->subscriber)
            <span class="text-xs font-semibold text-wine-700 bg-wine-50 border border-wine-200 rounded-full px-3 py-1">Newsletter subscriber</span>
        @endif
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-4">
        <div class="card p-4"><p class="text-xs uppercase tracking-wider text-neutral-500">Orders</p><p class="font-display text-2xl mt-1">{{ $customer->orders_count }}</p></div>
        <div class="card p-4"><p class="text-xs uppercase tracking-wider text-neutral-500">Total spent</p><p class="font-display text-2xl mt-1">{{ money($customer->total_spent) }}</p></div>
        <div class="card p-4"><p class="text-xs uppercase tracking-wider text-neutral-500">First order</p><p class="font-display text-2xl mt-1">{{ $customer->first_order_at->format('j M y') }}</p></div>
        <div class="card p-4"><p class="text-xs uppercase tracking-wider text-neutral-500">Last order</p><p class="font-display text-2xl mt-1">{{ $customer->last_order_at->format('j M y') }}</p></div>
    </div>

    <div class="grid lg:grid-cols-[1fr_300px] gap-4 mt-4 items-start">
        {{-- Orders --}}
        <div class="card overflow-x-auto">
            <h3 class="font-display text-lg p-4 pb-2">Order history</h3>
            <table class="w-full text-sm">
                <thead><tr class="text-left text-xs uppercase tracking-wider text-neutral-500 border-b border-neutral-200">
                    <th class="p-3">Order</th><th class="p-3">Channel</th><th class="p-3">Payment</th><th class="p-3">Total</th><th class="p-3">Date</th>
                </tr></thead>
                <tbody class="divide-y divide-neutral-100">
                    @foreach($orders as $order)
                        <tr>
                            <td class="p-3"><a href="{{ route('admin.orders.show', $order) }}" class="font-medium underline hover:text-wine-700">{{ $order->order_number }}</a></td>
                            <td class="p-3 capitalize text-neutral-600">{{ $order->channel === 'pos' ? 'In person' : 'Online' }}</td>
                            <td class="p-3"><span class="text-xs px-2 py-1 {{ $order->payment_status === 'paid' ? 'bg-green-100 text-green-800' : ($order->payment_status === 'pending' ? 'bg-amber-100 text-amber-800' : 'bg-neutral-200 text-neutral-600') }}">{{ ucfirst($order->payment_status) }}</span></td>
                            <td class="p-3 font-medium">{{ money($order->total) }}</td>
                            <td class="p-3 text-neutral-500 text-xs">{{ $order->created_at->format('j M Y') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Addresses --}}
        <div class="card p-5">
            <h3 class="font-display text-lg mb-3">Addresses used</h3>
            @forelse($customer->addresses as $addr)
                <p class="text-sm text-neutral-600 border-b border-neutral-100 last:border-0 py-2">{{ $addr }}</p>
            @empty
                <p class="text-sm text-neutral-400">No delivery addresses (in-person orders only).</p>
            @endforelse
        </div>
    </div>
</div>
@endsection

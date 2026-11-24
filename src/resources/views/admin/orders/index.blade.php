@extends('layouts.admin')

@section('title', 'Orders')

@section('content')
<form method="GET" class="flex flex-wrap gap-2 mb-6">
    <input name="q" value="{{ request('q') }}" placeholder="Order #, customer, email…" class="input !py-2 !w-64">
    <select name="payment_status" class="input !py-2 !w-40">
        <option value="">Payment: all</option>
        @foreach(['pending','paid','failed','abandoned','refunded'] as $s)
            <option value="{{ $s }}" {{ request('payment_status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
        @endforeach
    </select>
    <select name="fulfilment_status" class="input !py-2 !w-44">
        <option value="">Fulfilment: all</option>
        @foreach(['unfulfilled','processing','ready','shipped','delivered','cancelled'] as $s)
            <option value="{{ $s }}" {{ request('fulfilment_status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
        @endforeach
    </select>
    <select name="channel" class="input !py-2 !w-32">
        <option value="">Channel: all</option>
        <option value="online" {{ request('channel') === 'online' ? 'selected' : '' }}>Online</option>
        <option value="pos" {{ request('channel') === 'pos' ? 'selected' : '' }}>In person</option>
    </select>
    <button class="btn-primary !py-2 text-sm">Filter</button>
</form>

<div class="card overflow-x-auto">
    <table class="w-full text-sm">
        <thead><tr class="text-left text-xs uppercase tracking-wider text-neutral-500 border-b border-neutral-200">
            <th class="p-3">Order</th><th class="p-3">Customer</th><th class="p-3">Channel</th><th class="p-3">Payment</th><th class="p-3">Fulfilment</th><th class="p-3">Total</th><th class="p-3">Placed</th>
        </tr></thead>
        <tbody class="divide-y divide-neutral-100">
            @forelse($orders as $order)
                <tr>
                    <td class="p-3"><a href="{{ route('admin.orders.show', $order) }}" class="font-medium underline hover:text-wine-700">{{ $order->order_number }}</a></td>
                    <td class="p-3">{{ $order->customerDisplayName() }}</td>
                    <td class="p-3">{{ $order->isPos() ? 'In person' : 'Online' }}</td>
                    <td class="p-3"><span class="text-xs px-2 py-1 {{ $order->payment_status === 'paid' ? 'bg-green-100 text-green-800' : ($order->payment_status === 'pending' ? 'bg-amber-100 text-amber-800' : 'bg-neutral-200 text-neutral-600') }}">{{ ucfirst($order->payment_status) }}</span></td>
                    <td class="p-3 capitalize text-neutral-600">{{ $order->fulfilment_status }}</td>
                    <td class="p-3 font-medium">{{ money($order->total) }}</td>
                    <td class="p-3 text-neutral-500">{{ $order->created_at->format('j M, g:ia') }}</td>
                </tr>
            @empty
                <tr><td colspan="7" class="p-8 text-center text-neutral-500">No orders found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $orders->links() }}</div>
@endsection

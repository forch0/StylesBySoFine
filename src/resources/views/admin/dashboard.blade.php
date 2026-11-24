@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <div class="card p-5">
        <p class="label">Orders today</p>
        <p class="font-display text-3xl">{{ $ordersToday }}</p>
    </div>
    <div class="card p-5">
        <p class="label">Awaiting processing</p>
        <p class="font-display text-3xl">{{ $awaitingProcessing }}</p>
    </div>
    <div class="card p-5">
        <p class="label">Revenue today</p>
        <p class="font-display text-3xl">{{ money($revenueToday) }}</p>
    </div>
    <div class="card p-5">
        <p class="label">Revenue this month</p>
        <p class="font-display text-3xl">{{ money($revenueMonth) }}</p>
    </div>
</div>

<div class="grid lg:grid-cols-3 gap-6">
    {{-- Sales trend --}}
    <div class="card p-5 lg:col-span-1">
        <h2 class="font-display text-lg mb-4">Sales — last 14 days</h2>
        @if($salesTrend->isEmpty())
            <p class="text-sm text-neutral-500">No paid sales yet.</p>
        @else
            @php $max = $salesTrend->max('total') ?: 1; @endphp
            <div class="flex items-end gap-1.5 h-32">
                @foreach($salesTrend as $day)
                    <div class="flex-1 bg-wine-700/80 hover:bg-wine-700 min-h-[2px]" style="height: {{ max(2, round($day->total / $max * 100)) }}%"
                         title="{{ $day->day }} — {{ money($day->total) }}"></div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Recent orders --}}
    <div class="card p-5 lg:col-span-2">
        <div class="flex justify-between items-center mb-4">
            <h2 class="font-display text-lg">Recent orders</h2>
            <a href="{{ route('admin.orders.index') }}" class="text-sm underline">View all</a>
        </div>
        @if($recentOrders->isEmpty())
            <p class="text-sm text-neutral-500">No orders yet.</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead><tr class="text-left text-xs uppercase tracking-wider text-neutral-500 border-b border-neutral-200">
                        <th class="pb-2">Order</th><th class="pb-2">Customer</th><th class="pb-2">Payment</th><th class="pb-2">Total</th>
                    </tr></thead>
                    <tbody class="divide-y divide-neutral-100">
                        @foreach($recentOrders as $order)
                            <tr>
                                <td class="py-2.5"><a href="{{ route('admin.orders.show', $order) }}" class="underline hover:text-wine-700">{{ $order->order_number }}</a>
                                    @if($order->isPos())<span class="text-[10px] bg-neutral-200 px-1.5 py-0.5 ml-1">POS</span>@endif</td>
                                <td class="py-2.5">{{ $order->customerDisplayName() }}</td>
                                <td class="py-2.5"><span class="capitalize {{ $order->payment_status === 'paid' ? 'text-green-700' : 'text-neutral-500' }}">{{ $order->payment_status }}</span></td>
                                <td class="py-2.5 font-medium">{{ money($order->total) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

<div class="grid lg:grid-cols-2 gap-6 mt-6">
    {{-- Low stock --}}
    <div class="card p-5">
        <div class="flex justify-between items-center mb-4">
            <h2 class="font-display text-lg">Low stock</h2>
            <a href="{{ route('admin.inventory.index', ['filter' => 'low']) }}" class="text-sm underline">Manage</a>
        </div>
        @if($lowStock->isEmpty())
            <p class="text-sm text-neutral-500">All variants are well stocked.</p>
        @else
            <ul class="divide-y divide-neutral-100 text-sm">
                @foreach($lowStock as $variant)
                    <li class="py-2.5 flex justify-between">
                        <span>{{ $variant->product->name }} <span class="text-neutral-500">({{ $variant->summary() }})</span></span>
                        <span class="font-semibold {{ $variant->stock_quantity === 0 ? 'text-red-700' : 'text-amber-700' }}">{{ $variant->stock_quantity }} left</span>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>

    {{-- Bestsellers --}}
    <div class="card p-5">
        <h2 class="font-display text-lg mb-4">Bestsellers</h2>
        @if($bestsellers->isEmpty())
            <p class="text-sm text-neutral-500">No sales data yet.</p>
        @else
            <ul class="divide-y divide-neutral-100 text-sm">
                @foreach($bestsellers as $item)
                    <li class="py-2.5 flex justify-between"><span>{{ $item->product_name }}</span><span class="font-semibold">{{ $item->sold }} sold</span></li>
                @endforeach
            </ul>
        @endif
    </div>
</div>

@if($unreadMessages > 0)
    <div class="mt-6 card p-5 bg-wine-50 border-wine-200">
        <a href="{{ route('admin.messages.index') }}" class="text-sm text-wine-800 underline">{{ $unreadMessages }} unread contact {{ Str::plural('message', $unreadMessages) }} →</a>
    </div>
@endif
@endsection

@extends('layouts.admin')

@section('title', 'Stock history — '.$variant->product->name)

@section('content')
<p class="mb-6 text-sm text-neutral-600">
    <a href="{{ route('admin.inventory.index') }}" class="underline">← Inventory</a> ·
    {{ $variant->product->name }} ({{ $variant->summary() }}) — current stock: <span class="font-semibold">{{ $variant->stock_quantity }}</span>
</p>

<div class="card overflow-x-auto">
    <table class="w-full text-sm">
        <thead><tr class="text-left text-xs uppercase tracking-wider text-neutral-500 border-b border-neutral-200">
            <th class="p-3">When</th><th class="p-3">Change</th><th class="p-3">After</th><th class="p-3">Reason</th><th class="p-3">By</th><th class="p-3">Order</th>
        </tr></thead>
        <tbody class="divide-y divide-neutral-100">
            @forelse($variant->movements->sortByDesc('created_at') as $m)
                <tr>
                    <td class="p-3 text-neutral-500">{{ $m->created_at->format('j M Y, g:ia') }}</td>
                    <td class="p-3 font-semibold {{ $m->change > 0 ? 'text-green-700' : 'text-red-700' }}">{{ $m->change > 0 ? '+' : '' }}{{ $m->change }}</td>
                    <td class="p-3">{{ $m->quantity_after }}</td>
                    <td class="p-3">{{ $m->reason }}</td>
                    <td class="p-3 text-neutral-500">{{ $m->user?->name ?? 'System' }}</td>
                    <td class="p-3">@if($m->order)<a href="{{ route('admin.orders.show', $m->order) }}" class="underline">{{ $m->order->order_number }}</a>@else—@endif</td>
                </tr>
            @empty
                <tr><td colspan="6" class="p-8 text-center text-neutral-500">No movements recorded.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection

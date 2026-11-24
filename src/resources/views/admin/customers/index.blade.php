@extends('layouts.admin')

@section('title', 'Customers')

@section('content')
<div class="max-w-5xl">
    <div class="flex items-center justify-between gap-4 mb-5">
        <p class="text-sm text-neutral-500">{{ $customers->count() }} {{ Str::plural('customer', $customers->count()) }} · identified by email</p>
        <form method="GET" class="relative">
            <svg class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-neutral-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/></svg>
            <input name="q" value="{{ request('q') }}" placeholder="Search name, email, phone…" class="input !py-2.5 !pl-10 w-64 max-w-full">
        </form>
    </div>

    <div class="card overflow-x-auto">
        <table class="w-full text-sm">
            <thead><tr class="text-left text-xs uppercase tracking-wider text-neutral-500 border-b border-neutral-200">
                <th class="p-3">Customer</th><th class="p-3">Email</th><th class="p-3">Phone</th><th class="p-3">Orders</th><th class="p-3">Total spent</th><th class="p-3">Last order</th><th class="p-3"></th>
            </tr></thead>
            <tbody class="divide-y divide-neutral-100">
                @forelse($customers as $c)
                    <tr class="hover:bg-neutral-50">
                        <td class="p-3">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-full bg-wine-100 text-wine-800 flex items-center justify-center font-display text-xs shrink-0">{{ strtoupper(substr($c->name ?: $c->email, 0, 1)) }}</span>
                                <div>
                                    <p class="font-medium">{{ $c->name ?: '—' }}</p>
                                    @if($c->subscriber)
                                        <span class="text-[10px] font-semibold text-wine-700 bg-wine-50 border border-wine-200 rounded-full px-1.5 py-0.5">Newsletter</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="p-3 text-neutral-600">{{ $c->email }}</td>
                        <td class="p-3 text-neutral-500">{{ $c->phone ?? '—' }}</td>
                        <td class="p-3">{{ $c->orders_count }}</td>
                        <td class="p-3 font-medium">{{ money($c->total_spent) }}</td>
                        <td class="p-3 text-neutral-500 text-xs">{{ $c->last_order_at->format('j M Y') }}</td>
                        <td class="p-3 text-right"><a href="{{ route('admin.customers.show', $c->email) }}" class="underline text-wine-700">View</a></td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="p-10 text-center text-neutral-500">No customers yet — they'll appear here after their first order.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

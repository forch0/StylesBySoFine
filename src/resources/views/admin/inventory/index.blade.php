@extends('layouts.admin')

@section('title', 'Inventory')

@section('content')
<form method="GET" class="flex flex-wrap gap-2 mb-6">
    <input name="q" value="{{ request('q') }}" placeholder="Search products…" class="input !py-2 !w-64">
    <select name="filter" class="input !py-2 !w-44" onchange="this.form.submit()">
        <option value="">All stock</option>
        <option value="low" {{ request('filter') === 'low' ? 'selected' : '' }}>Low stock</option>
        <option value="out" {{ request('filter') === 'out' ? 'selected' : '' }}>Out of stock</option>
    </select>
    <button class="btn-primary !py-2 text-sm">Search</button>
</form>

<div class="card overflow-x-auto">
    <table class="w-full text-sm">
        <thead><tr class="text-left text-xs uppercase tracking-wider text-neutral-500 border-b border-neutral-200">
            <th class="p-3">Product</th><th class="p-3">Variant</th><th class="p-3">SKU</th><th class="p-3">Stock</th><th class="p-3">Adjust</th><th class="p-3"></th>
        </tr></thead>
        <tbody class="divide-y divide-neutral-100">
            @forelse($variants as $variant)
                <tr>
                    <td class="p-3 font-medium">{{ $variant->product->name }}</td>
                    <td class="p-3 text-neutral-600">{{ $variant->summary() }}</td>
                    <td class="p-3 text-neutral-500">{{ $variant->sku ?: '—' }}</td>
                    <td class="p-3">
                        <span class="font-semibold {{ $variant->stock_quantity <= 0 ? 'text-red-700' : ($variant->isLowStock() ? 'text-amber-700' : '') }}">{{ $variant->stock_quantity }}</span>
                    </td>
                    <td class="p-3">
                        <form action="{{ route('admin.inventory.adjust', $variant) }}" method="POST" class="flex gap-1.5">
                            @csrf
                            <input name="change" type="number" placeholder="+/−" class="input !py-1 !w-20 text-xs" required>
                            <input name="reason" placeholder="Reason" class="input !py-1 !w-36 text-xs" required>
                            <button class="btn-primary !py-1 !px-3 text-xs">Apply</button>
                        </form>
                    </td>
                    <td class="p-3"><a href="{{ route('admin.inventory.history', $variant) }}" class="underline text-xs">History</a></td>
                </tr>
            @empty
                <tr><td colspan="6" class="p-8 text-center text-neutral-500">No variants found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $variants->links() }}</div>
@endsection

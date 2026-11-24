@extends('layouts.admin')

@section('title', 'Products')

@section('content')
<div class="flex flex-wrap gap-3 items-center justify-between mb-6">
    <form method="GET" class="flex gap-2">
        <input name="q" value="{{ request('q') }}" placeholder="Search products…" class="input !py-2 !w-56">
        <select name="status" onchange="this.form.submit()" class="input !py-2 !w-36">
            <option value="">All statuses</option>
            @foreach(['draft','active','archived'] as $s)
                <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
            @endforeach
        </select>
    </form>
    <a href="{{ route('admin.products.create') }}" class="btn-primary">+ New product</a>
</div>

<div class="card overflow-x-auto">
    <table class="w-full text-sm">
        <thead><tr class="text-left text-xs uppercase tracking-wider text-neutral-500 border-b border-neutral-200">
            <th class="p-3">Product</th><th class="p-3">Category</th><th class="p-3">Price</th><th class="p-3">Stock</th><th class="p-3">Status</th><th class="p-3"></th>
        </tr></thead>
        <tbody class="divide-y divide-neutral-100">
            @forelse($products as $product)
                <tr>
                    <td class="p-3">
                        <div class="flex items-center gap-3">
                            @php $m = $product->media->firstWhere('is_primary', true) ?? $product->media->first(); @endphp
                            <div class="w-10 h-12 bg-neutral-100 overflow-hidden flex-shrink-0">
                                @if($m)<img src="{{ $m->url() }}" class="w-full h-full object-cover" alt="">@endif
                            </div>
                            <a href="{{ route('admin.products.edit', $product) }}" class="font-medium hover:text-wine-700">{{ $product->name }}</a>
                            @if($product->is_featured)<span class="text-[10px] bg-wine-100 text-wine-800 px-1.5 py-0.5">Featured</span>@endif
                        </div>
                    </td>
                    <td class="p-3 text-neutral-500">{{ $product->category?->name ?? '—' }}</td>
                    <td class="p-3">{{ money($product->price) }}</td>
                    <td class="p-3">{{ $product->variants->sum('stock_quantity') }}</td>
                    <td class="p-3"><span class="text-xs px-2 py-1 {{ $product->status === 'active' ? 'bg-green-100 text-green-800' : ($product->status === 'draft' ? 'bg-amber-100 text-amber-800' : 'bg-neutral-200 text-neutral-600') }}">{{ ucfirst($product->status) }}</span></td>
                    <td class="p-3 text-right whitespace-nowrap">
                        <a href="{{ route('admin.products.edit', $product) }}" class="underline mr-3">Edit</a>
                        <a href="{{ route('product.show', $product->slug) }}" target="_blank" class="underline text-neutral-500">View</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="p-8 text-center text-neutral-500">No products yet. <a href="{{ route('admin.products.create') }}" class="underline">Create the first one</a>.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $products->links() }}</div>
@endsection

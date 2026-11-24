@extends('layouts.storefront')

@section('title', ($category->name ?? 'Shop All').' — '.($settings['store_name'] ?? ''))

@section('content')
<div class="section-pad py-10 lg:py-14">
    {{-- Heading --}}
    <div class="mb-8">
        <p class="eyebrow mb-2">{{ $category ? 'Category' : 'The boutique' }}</p>
        <h1 class="heading-display">{{ $category->name ?? ($search ? "“{$search}”" : 'Shop All') }}</h1>
        @if($category?->description)
            <p class="mt-3 text-neutral-600 max-w-2xl">{{ $category->description }}</p>
        @endif
    </div>

    <div class="lg:grid lg:grid-cols-[240px_1fr] lg:gap-10" x-data="{ filtersOpen: false }">
        {{-- Filters --}}
        <aside>
            <button class="lg:hidden btn-outline w-full mb-5" @click="filtersOpen = !filtersOpen">
                Filters
                <svg class="w-4 h-4" :class="filtersOpen && 'rotate-180'" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/></svg>
            </button>
            <form method="GET" class="space-y-7 mb-8 lg:mb-0" :class="filtersOpen ? 'block' : 'hidden lg:block'">
                @if($search)
                    <input type="hidden" name="q" value="{{ $search }}">
                @endif

                <div>
                    <p class="label">Category</p>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('shop') }}" class="{{ !$category ? 'font-semibold text-wine-700' : 'hover:text-wine-700' }}">All</a></li>
                        @foreach($categories as $cat)
                            <li><a href="{{ route('shop.category', $cat->slug) }}" class="{{ $category?->id === $cat->id ? 'font-semibold text-wine-700' : 'hover:text-wine-700' }}">{{ $cat->name }}</a></li>
                        @endforeach
                    </ul>
                </div>

                <div>
                    <p class="label">Size</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach($sizes as $size)
                            <label class="cursor-pointer">
                                <input type="radio" name="size" value="{{ $size }}" class="peer sr-only" {{ request('size') === $size ? 'checked' : '' }}>
                                <span class="inline-block px-3 py-1.5 border border-neutral-300 text-xs peer-checked:bg-wine-700 peer-checked:text-cream peer-checked:border-wine-700">{{ $size }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                @if($colours->isNotEmpty())
                <div>
                    <p class="label">Colour</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach($colours as $colour)
                            <label class="cursor-pointer">
                                <input type="radio" name="colour" value="{{ $colour }}" class="peer sr-only" {{ request('colour') === $colour ? 'checked' : '' }}>
                                <span class="inline-block px-3 py-1.5 border border-neutral-300 text-xs peer-checked:bg-wine-700 peer-checked:text-cream peer-checked:border-wine-700">{{ $colour }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
                @endif

                <div>
                    <p class="label">Max price (₦)</p>
                    <input type="number" name="max_price" value="{{ request('max_price') }}" min="0" class="input" placeholder="e.g. 50000">
                </div>

                <label class="flex items-center gap-2 text-sm">
                    <input type="checkbox" name="in_stock" value="1" {{ request('in_stock') ? 'checked' : '' }} class="accent-wine-700 w-4 h-4">
                    In stock only
                </label>

                <button class="btn-primary w-full">Apply</button>
                <a href="{{ $category ? route('shop.category', $category->slug) : route('shop') }}" class="block text-center text-xs text-neutral-500 underline">Clear filters</a>
            </form>
        </aside>

        {{-- Grid --}}
        <div>
            <div class="flex items-center justify-between mb-6">
                <p class="text-sm text-neutral-500">{{ $products->total() }} {{ Str::plural('item', $products->total()) }}</p>
                <form method="GET">
                    @foreach(request()->except('sort', 'page') as $k => $v)
                        <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                    @endforeach
                    <select name="sort" onchange="this.form.submit()" class="text-sm border-neutral-300 focus:border-wine-700 focus:ring-wine-700">
                        <option value="newest" {{ request('sort') === 'newest' ? 'selected' : '' }}>Newest</option>
                        <option value="price_asc" {{ request('sort') === 'price_asc' ? 'selected' : '' }}>Price: low to high</option>
                        <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>Price: high to low</option>
                        <option value="popular" {{ request('sort') === 'popular' ? 'selected' : '' }}>Most popular</option>
                    </select>
                </form>
            </div>

            @if($products->isEmpty())
                <div class="text-center py-20">
                    <p class="font-display text-2xl mb-3">Nothing here yet</p>
                    <p class="text-neutral-500 mb-6">Try a different search or browse the full collection.</p>
                    <a href="{{ route('shop') }}" class="btn-primary">Shop all</a>
                </div>
            @else
                <div class="grid grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-6">
                    @foreach($products as $product)
                        <x-product-card :product="$product" />
                    @endforeach
                </div>
                <div class="mt-10">{{ $products->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection

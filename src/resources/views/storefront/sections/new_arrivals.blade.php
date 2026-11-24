@if($newArrivals->isNotEmpty())
<section class="section-pad py-14 sm:py-20 {{ $preset->key === 'boutique' ? 'bg-white border-y border-neutral-200' : '' }}">
    <div class="flex items-end justify-between mb-8">
        <div>
            <p class="eyebrow mb-2">{{ $section->get('eyebrow', 'Just in') }}</p>
            <h2 class="heading-display">{{ $section->get('heading', 'New arrivals') }}</h2>
        </div>
        <a href="{{ route('shop', ['sort' => 'newest']) }}" class="hidden sm:block text-sm uppercase tracking-wider underline underline-offset-4 hover:text-wine-700">Shop new in</a>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-6">
        @foreach($newArrivals->take(8) as $product)
            <x-product-card :product="$product" />
        @endforeach
    </div>

    <div class="mt-8 text-center sm:hidden">
        <a href="{{ route('shop') }}" class="btn-outline">Shop all</a>
    </div>
</section>
@endif

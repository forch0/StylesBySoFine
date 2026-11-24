@if($bestsellers->isNotEmpty())
<section class="section-pad py-14 sm:py-20 bg-white border-y border-neutral-200">
    <div class="flex items-end justify-between mb-8">
        <div>
            <p class="eyebrow mb-2">{{ $section->get('eyebrow', 'Most loved') }}</p>
            <h2 class="heading-display">{{ $section->get('heading', 'Bestsellers') }}</h2>
        </div>
    </div>
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-6">
        @foreach($bestsellers->take(4) as $product)
            <x-product-card :product="$product" />
        @endforeach
    </div>
</section>
@endif

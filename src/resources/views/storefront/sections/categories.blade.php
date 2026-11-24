@if($categories->isNotEmpty())
<section class="section-pad py-14 sm:py-20">
    <div class="flex items-end justify-between mb-8">
        <div>
            <p class="eyebrow mb-2">{{ $section->get('eyebrow', 'Browse') }}</p>
            <h2 class="heading-display">{{ $section->get('heading', 'Shop by category') }}</h2>
        </div>
        <a href="{{ route('shop') }}" class="hidden sm:block text-sm uppercase tracking-wider underline underline-offset-4 hover:text-wine-700">View all</a>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-5">
        @foreach($categories as $category)
            <a href="{{ route('shop.category', $category->slug) }}" class="group relative aspect-[4/5] overflow-hidden bg-neutral-100 reveal">
                @if($category->image)
                    <img src="{{ Storage::url($category->image) }}" alt="{{ $category->name }}" loading="lazy"
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                @endif
                <div class="absolute inset-0 bg-gradient-to-t from-ink/70 to-transparent"></div>
                <span class="absolute bottom-4 left-4 text-cream font-display text-xl sm:text-2xl">{{ $category->name }}</span>
            </a>
        @endforeach
    </div>
</section>
@endif

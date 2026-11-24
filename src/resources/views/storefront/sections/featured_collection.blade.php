@php $collection = $collections->first(); @endphp
@if($collection)
<section class="section-pad py-14 sm:py-24">
    <div class="grid lg:grid-cols-2 gap-8 lg:gap-16 items-center">
        <div class="relative aspect-[4/5] lg:aspect-[5/6] overflow-hidden bg-neutral-100 reveal">
            @if($collection->image)
                <img src="{{ Storage::url($collection->image) }}" alt="{{ $collection->name }}" loading="lazy"
                     class="w-full h-full object-cover">
            @endif
        </div>
        <div class="reveal">
            <p class="eyebrow mb-3">{{ $section->get('eyebrow', 'Featured collection') }}</p>
            <h2 class="heading-display mb-5">{{ $section->get('heading', $collection->name) }}</h2>
            <p class="text-neutral-600 leading-relaxed max-w-md mb-8">{{ $section->get('text', $collection->description) }}</p>
            <a href="{{ route('shop') }}" class="btn-primary">{{ $section->get('cta_text', 'Explore the edit') }}</a>
        </div>
    </div>
</section>
@endif

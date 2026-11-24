<section class="section-pad py-14 sm:py-24">
    <div class="grid lg:grid-cols-2 gap-8 lg:gap-16 items-center">
        <div class="order-2 lg:order-1 reveal">
            <p class="eyebrow mb-3">{{ $section->get('eyebrow', 'Style notes') }}</p>
            <h2 class="heading-display mb-5">{{ $section->get('heading', 'How to wear it') }}</h2>
            <p class="text-neutral-600 leading-relaxed max-w-md mb-8">{{ $section->get('text') }}</p>
            @if($section->get('cta_text'))
                <a href="{{ $section->get('cta_url', route('shop')) }}" class="btn-outline">{{ $section->get('cta_text') }}</a>
            @endif
        </div>
        <div class="order-1 lg:order-2 relative aspect-[4/5] lg:aspect-[5/6] overflow-hidden bg-neutral-100 reveal">
            @if($section->get('image'))
                <img src="{{ Str::startsWith($section->get('image'), 'http') ? $section->get('image') : Storage::url($section->get('image')) }}" alt="" loading="lazy"
                     class="w-full h-full object-cover">
            @endif
        </div>
    </div>
</section>

<section class="section-pad py-8">
    <div class="relative overflow-hidden bg-wine-800 text-cream">
        @if($section->get('image'))
            <img src="{{ Str::startsWith($section->get('image'), 'http') ? $section->get('image') : Storage::url($section->get('image')) }}" alt="" loading="lazy"
                 class="absolute inset-0 w-full h-full object-cover opacity-40">
        @endif
        <div class="relative px-6 py-14 sm:py-20 text-center">
            <h2 class="font-display text-3xl sm:text-5xl mb-4">{{ $section->get('heading', 'The Sale Edit') }}</h2>
            @if($section->get('text'))
                <p class="text-wine-100 max-w-lg mx-auto mb-7">{{ $section->get('text') }}</p>
            @endif
            <a href="{{ $section->get('cta_url', route('shop')) }}" class="btn bg-cream text-ink hover:bg-wine-200">{{ $section->get('cta_text', 'Shop now') }}</a>
        </div>
    </div>
</section>

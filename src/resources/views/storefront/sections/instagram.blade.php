<section class="section-pad py-14 sm:py-20 text-center">
    <p class="eyebrow mb-2">{{ $section->get('eyebrow', 'On Instagram') }}</p>
    <h2 class="heading-display mb-8">{{ $section->get('heading', '@stylesby_sofine') }}</h2>
    @if($section->get('images'))
        <div class="grid grid-cols-3 sm:grid-cols-6 gap-2">
            @foreach(array_slice($section->get('images', []), 0, 6) as $img)
                <div class="aspect-square overflow-hidden bg-neutral-100">
                    <img src="{{ $img }}" alt="" loading="lazy" class="w-full h-full object-cover hover:scale-105 transition-transform duration-500">
                </div>
            @endforeach
        </div>
    @endif
    @if($settings['instagram_url'] ?? false)
        <a href="{{ $settings['instagram_url'] }}" target="_blank" rel="noopener" class="btn-outline mt-8">Follow along</a>
    @endif
</section>

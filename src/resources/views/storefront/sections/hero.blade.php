@php
    // Prefer slides[]; fall back to legacy single-slide fields
    $slides = $section->get('slides');
    if (! is_array($slides) || empty($slides)) {
        $slides = [[
            'eyebrow' => $section->get('eyebrow', 'New season'),
            'headline' => $section->get('headline', 'Wear the moment'),
            'subcopy' => $section->get('subcopy', ''),
            'cta_text' => $section->get('cta_text', 'Shop the collection'),
            'cta_url' => $section->get('cta_url', route('shop')),
            'cta2_text' => $section->get('cta2_text', ''),
            'cta2_url' => $section->get('cta2_url', route('shop')),
            'image' => $section->get('image'),
        ]];
    }
    $count = count($slides);
@endphp

<section class="relative min-h-[70vh] lg:min-h-[88vh] flex items-end overflow-hidden bg-ink"
         x-data="{
             i: 0, total: {{ $count }}, timer: null,
             go(n) { this.i = (n + this.total) % this.total; this.restart() },
             restart() { clearInterval(this.timer); if (this.total > 1) this.timer = setInterval(() => this.i = (this.i + 1) % this.total, 6500) },
             touchX: null
         }"
         x-init="restart()"
         @touchstart="touchX = $event.touches[0].clientX"
         @touchend="touchX !== null && (Math.abs($event.changedTouches[0].clientX - touchX) > 40 ? go(i + ($event.changedTouches[0].clientX < touchX ? 1 : -1)) : null); touchX = null">

    {{-- Slides --}}
    @foreach($slides as $s => $slide)
        <div class="absolute inset-0 transition-opacity duration-1000"
             :class="i === {{ $s }} ? 'opacity-100 z-10' : 'opacity-0 z-0 pointer-events-none'">
            @if(!empty($slide['image']))
                <img src="{{ Str::startsWith($slide['image'], 'http') ? $slide['image'] : Storage::url($slide['image']) }}"
                     alt="{{ $slide['headline'] ?? '' }}"
                     @if($s === 0) fetchpriority="high" @else loading="lazy" @endif
                     decoding="async"
                     class="absolute inset-0 w-full h-full object-cover opacity-75">
            @endif
            <div class="absolute inset-0 bg-gradient-to-t from-ink/85 via-ink/25 to-ink/10"></div>

            <div class="relative section-pad pb-20 sm:pb-24 pt-48 w-full h-full flex flex-col justify-end">
                @if(!empty($slide['eyebrow']))
                    <p class="eyebrow text-wine-200 mb-4">{{ $slide['eyebrow'] }}</p>
                @endif
                @if(!empty($slide['headline']))
                    <h1 class="font-display text-cream text-5xl sm:text-6xl lg:text-8xl leading-[0.95] max-w-4xl">{{ $slide['headline'] }}</h1>
                @endif
                @if(!empty($slide['subcopy']))
                    <p class="mt-5 text-neutral-200 max-w-xl text-base sm:text-lg">{{ $slide['subcopy'] }}</p>
                @endif
                @if(!empty($slide['cta_text']) || !empty($slide['cta2_text']))
                    <div class="mt-8 flex flex-wrap gap-4">
                        @if(!empty($slide['cta_text']))
                            <a href="{{ $slide['cta_url'] ?? route('shop') }}" class="btn bg-cream text-ink hover:bg-wine-700 hover:text-cream">{{ $slide['cta_text'] }}</a>
                        @endif
                        @if(!empty($slide['cta2_text']))
                            <a href="{{ $slide['cta2_url'] ?? route('shop') }}" class="btn border border-cream text-cream hover:bg-cream hover:text-ink">{{ $slide['cta2_text'] }}</a>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    @endforeach

    @if($count > 1)
        {{-- Arrows --}}
        <button @click="go(i - 1)" aria-label="Previous slide"
                class="absolute left-3 sm:left-6 top-1/2 -translate-y-1/2 z-20 w-11 h-11 rounded-full border border-cream/40 text-cream flex items-center justify-center hover:bg-cream hover:text-ink transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"/></svg>
        </button>
        <button @click="go(i + 1)" aria-label="Next slide"
                class="absolute right-3 sm:right-6 top-1/2 -translate-y-1/2 z-20 w-11 h-11 rounded-full border border-cream/40 text-cream flex items-center justify-center hover:bg-cream hover:text-ink transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/></svg>
        </button>

        {{-- Dots --}}
        <div class="absolute bottom-6 left-1/2 -translate-x-1/2 z-20 flex gap-2">
            @foreach($slides as $s => $slide)
                <button @click="go({{ $s }})" aria-label="Slide {{ $s + 1 }}"
                        class="h-1.5 rounded-full transition-all duration-300"
                        :class="i === {{ $s }} ? 'w-8 bg-cream' : 'w-3 bg-cream/40'"></button>
            @endforeach
        </div>
    @endif
</section>

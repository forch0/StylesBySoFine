@php
    $items = $section->get('items');
    if (is_string($items)) {
        $items = array_values(array_filter(array_map('trim', preg_split('/[\n,]+/', $items))));
    }
    $items = $items ?: ['Style that speaks', 'Elegance in every detail', 'Your style, your statement'];
@endphp

<section class="bg-ink text-cream py-4 overflow-hidden" aria-hidden="true">
    <div class="flex whitespace-nowrap marquee-track">
        @for($r = 0; $r < 2; $r++)
            <div class="flex shrink-0">
                @foreach($items as $item)
                    <span class="mx-6 font-display text-lg sm:text-xl italic tracking-wide">{{ $item }}</span>
                    <span class="text-wine-400">✦</span>
                @endforeach
            </div>
        @endfor
    </div>
</section>

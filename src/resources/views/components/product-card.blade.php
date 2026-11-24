@props(['product'])

@php
    $media = $product->media->firstWhere('is_primary', true) ?? $product->media->first();
@endphp

<a href="{{ route('product.show', $product->slug) }}" class="group block">
    <div class="relative aspect-[3/4] overflow-hidden bg-neutral-100">
        @if($media)
            @if($media->isVideo())
                <video src="{{ $media->url() }}" poster="{{ $media->posterUrl() }}" muted loop playsinline preload="none"
                       class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700"
                       onmouseenter="this.play()" onmouseleave="this.pause()"></video>
                <span class="absolute top-3 left-3 bg-ink/70 text-cream text-[10px] uppercase tracking-wider px-2 py-1">Video</span>
            @else
                <img src="{{ $media->url() }}" alt="{{ $media->alt ?? $product->name }}" loading="lazy" decoding="async"
                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
            @endif
        @else
            <div class="w-full h-full flex items-center justify-center text-neutral-300">
                <svg class="w-12 h-12" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24"><path d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909M3.75 21h16.5A2.25 2.25 0 0 0 22.5 18.75V5.25A2.25 2.25 0 0 0 20.25 3H3.75A2.25 2.25 0 0 0 1.5 5.25v13.5A2.25 2.25 0 0 0 3.75 21Z"/></svg>
            </div>
        @endif

        @if($product->onSale())
            <span class="absolute top-3 right-3 bg-wine-700 text-cream text-[10px] uppercase tracking-wider px-2 py-1">Sale</span>
        @elseif($product->is_new_arrival)
            <span class="absolute top-3 right-3 bg-ink text-cream text-[10px] uppercase tracking-wider px-2 py-1">New</span>
        @endif

        @unless($product->inStock())
            @if($product->allow_preorder)
                <span class="absolute bottom-3 left-3 bg-wine-700 text-cream text-[10px] uppercase tracking-wider px-2 py-1">Pre-order</span>
            @else
                <span class="absolute inset-0 bg-cream/60 flex items-center justify-center text-sm uppercase tracking-wider text-ink">Sold out</span>
            @endif
        @endunless
    </div>
    <div class="pt-3 pb-1">
        <h3 class="text-sm font-medium group-hover:text-wine-700 transition-colors">{{ $product->name }}</h3>
        <p class="mt-1 text-sm">
            @if($product->onSale())
                <span class="text-neutral-400 line-through mr-2">{{ money($product->compare_at_price) }}</span>
            @endif
            <span class="font-semibold">{{ money($product->price) }}</span>
        </p>
    </div>
</a>

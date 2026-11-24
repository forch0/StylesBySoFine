@extends('layouts.storefront')

@section('title', ($product->meta_title ?: $product->name).' — '.($settings['store_name'] ?? ''))
@section('meta_description', $product->meta_description ?: $product->short_description)
@section('og_type', 'product')
@php $ogMedia = $product->media->firstWhere('is_primary', true) ?? $product->media->first(); @endphp
@section('og_image', $ogMedia?->url() ?? asset('images/logo.png'))
@section('json_ld')
<script type="application/ld+json">{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'Product',
    'name' => $product->name,
    'url' => route('product.show', $product->slug),
    'image' => $product->media->map(fn ($m) => $m->url())->filter()->values()->all(),
    'description' => $product->short_description ?: $product->name,
    'sku' => $product->sku,
    'brand' => ['@type' => 'Brand', 'name' => $settings['store_name'] ?? 'Styles By So Fine'],
    'offers' => [
        '@type' => 'Offer',
        'url' => route('product.show', $product->slug),
        'priceCurrency' => 'NGN',
        'price' => number_format($product->price / 100, 2, '.', ''),
        'availability' => $product->inStock()
            ? 'https://schema.org/InStock'
            : ($product->allow_preorder ? 'https://schema.org/PreOrder' : 'https://schema.org/OutOfStock'),
        'itemCondition' => 'https://schema.org/NewCondition',
    ],
], JSON_UNESCAPED_SLASHES) !!}</script>
@endsection

@section('content')
@php
    $variants = $product->variants->where('is_active', true)->values();
    $variantPayload = $variants->map(fn ($v) => [
        'uuid' => $v->uuid,
        'options' => $v->options,
        'price' => $v->effectivePrice(),
        'price_display' => money($v->effectivePrice()),
        'stock' => $v->stock_quantity,
    ]);
@endphp

<div class="section-pad py-8 lg:py-14" x-data="{
    selected: {},
    variants: {{ Js::from($variantPayload) }},
    preorder: {{ $product->allow_preorder ? 'true' : 'false' }},
    preorderNote: {{ Js::from($product->preorder_note ?: 'Made to order — we\'ll confirm your ship date on WhatsApp.') }},
    get matched() {
        return this.variants.find(v => Object.keys(v.options).every(k => v.options[k] === this.selected[k]));
    },
    get isPreorder() {
        return this.matched && this.matched.stock <= 0 && this.preorder;
    },
    get optionNames() {
        const names = new Set();
        this.variants.forEach(v => Object.keys(v.options).forEach(k => names.add(k)));
        return [...names];
    },
    valuesFor(name) {
        return [...new Set(this.variants.map(v => v.options[name]).filter(Boolean))];
    },
    isAvailable(name, value) {
        return this.variants.some(v => v.options[name] === value && (v.stock > 0 || this.preorder));
    }
}">
    <div class="grid lg:grid-cols-2 gap-10 lg:gap-16">
        {{-- Media gallery (images + video) --}}
        <div x-data="{ active: 0 }">
            <div class="relative aspect-[3/4] bg-neutral-100 overflow-hidden">
                @foreach($product->media as $i => $media)
                    <div x-show="active === {{ $i }}" @if($i > 0) x-cloak @endif class="absolute inset-0">
                        @if($media->isVideo())
                            @if($media->embedUrl())
                                <iframe src="{{ $media->embedUrl() }}" class="w-full h-full" frameborder="0"
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                            @else
                                <video src="{{ $media->url() }}" poster="{{ $media->posterUrl() }}" controls playsinline preload="none" class="w-full h-full object-cover"></video>
                            @endif
                        @else
                            <img src="{{ $media->url() }}" alt="{{ $media->alt ?? $product->name }}" class="w-full h-full object-cover"
                                 @if($i > 0) loading="lazy" @endif>
                        @endif
                    </div>
                @endforeach
                @if($product->media->isEmpty())
                    <div class="w-full h-full flex items-center justify-center text-neutral-300">
                        <svg class="w-16 h-16" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24"><path d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909M3.75 21h16.5A2.25 2.25 0 0 0 22.5 18.75V5.25A2.25 2.25 0 0 0 20.25 3H3.75A2.25 2.25 0 0 0 1.5 5.25v13.5A2.25 2.25 0 0 0 3.75 21Z"/></svg>
                    </div>
                @endif
            </div>
            @if($product->media->count() > 1)
                <div class="flex gap-2 mt-3 overflow-x-auto">
                    @foreach($product->media as $i => $media)
                        <button @click="active = {{ $i }}" class="relative w-20 aspect-[3/4] flex-shrink-0 overflow-hidden border-2"
                                :class="active === {{ $i }} ? 'border-wine-700' : 'border-transparent'">
                            @if($media->isVideo())
                                @if($media->posterUrl())
                                    <img src="{{ $media->posterUrl() }}" class="w-full h-full object-cover" alt="">
                                @else
                                    <div class="w-full h-full bg-ink flex items-center justify-center">
                                        <svg class="w-6 h-6 text-cream" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                    </div>
                                @endif
                                <span class="absolute inset-0 flex items-center justify-center">
                                    <span class="bg-ink/60 rounded-full p-1.5">
                                        <svg class="w-4 h-4 text-cream" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                    </span>
                                </span>
                            @else
                                <img src="{{ $media->url() }}" class="w-full h-full object-cover" alt="" loading="lazy">
                            @endif
                        </button>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Details --}}
        <div class="lg:py-4">
            @if($product->category)
                <a href="{{ route('shop.category', $product->category->slug) }}" class="eyebrow">{{ $product->category->name }}</a>
            @endif
            <h1 class="font-display text-3xl sm:text-4xl mt-2">{{ $product->name }}</h1>

            <p class="mt-4 text-xl">
                @if($product->onSale())
                    <span class="text-neutral-400 line-through mr-3 text-lg">{{ money($product->compare_at_price) }}</span>
                @endif
                <span class="font-semibold" x-text="matched ? matched.price_display : '{{ money($product->price) }}'">{{ money($product->price) }}</span>
            </p>

            {{-- Viewers badge --}}
            @php $baseViewers = 4 + (($product->id * 37 + now()->hour) % 14); @endphp
            <p class="mt-4 inline-flex items-center gap-2 rounded-full bg-wine-50 border border-wine-200 px-4 py-1.5 text-xs text-wine-900"
               x-data="{ v: {{ $baseViewers }} }"
               x-init="setInterval(() => { v = Math.min(20, Math.max(3, v + (Math.random() < 0.5 ? -1 : 1))) }, 8000)">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-wine-500 opacity-60"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-wine-700"></span>
                </span>
                <svg class="w-3.5 h-3.5 text-wine-700" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/></svg>
                <span><span class="font-semibold" x-text="v"></span> people are looking at this</span>
            </p>

            @if($product->short_description)
                <p class="mt-5 text-neutral-600 leading-relaxed">{{ $product->short_description }}</p>
            @endif

            <form action="{{ route('cart.add') }}" method="POST" class="mt-8 space-y-6">
                @csrf
                <input type="hidden" name="product_uuid" value="{{ $product->uuid }}">
                <input type="hidden" name="variant_uuid" :value="matched ? matched.uuid : ''">

                {{-- Option selectors --}}
                <template x-for="name in optionNames" :key="name">
                    <div>
                        <div class="flex items-end justify-between">
                            <p class="label" x-text="name"></p>
                            <a x-show="name.toLowerCase() === 'size'" href="{{ url('/pages/size-guide') }}" class="mb-1.5 text-xs text-wine-700 underline underline-offset-2 hover:text-wine-800">Size guide</a>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <template x-for="value in valuesFor(name)" :key="value">
                                <button type="button" @click="selected[name] = value"
                                        :disabled="!isAvailable(name, value)"
                                        :class="selected[name] === value ? 'bg-ink text-cream border-ink' : 'border-neutral-300 hover:border-ink'"
                                        class="px-4 py-2.5 border text-sm transition-colors disabled:opacity-30 disabled:line-through disabled:cursor-not-allowed"
                                        x-text="value"></button>
                            </template>
                        </div>
                    </div>
                </template>

                {{-- Availability --}}
                <div class="text-sm">
                    <template x-if="matched && matched.stock > 0 && matched.stock <= 3">
                        <p class="text-wine-700 font-medium">Only <span x-text="matched.stock"></span> left</p>
                    </template>
                    <template x-if="matched && matched.stock <= 0 && preorder">
                        <p class="inline-flex items-center gap-2 rounded-full bg-wine-50 border border-wine-200 text-wine-800 px-4 py-1.5 font-medium">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                            <span>Pre-order — <span x-text="preorderNote"></span></span>
                        </p>
                    </template>
                    <template x-if="matched && matched.stock <= 0 && !preorder">
                        <p class="text-red-700 font-medium">This option is sold out</p>
                    </template>
                </div>

                <div class="flex items-stretch gap-3">
                    <div class="flex items-center border border-neutral-300">
                        <button type="button" onclick="this.nextElementSibling.stepDown()" class="px-3 py-2 text-lg">−</button>
                        <input type="number" name="quantity" value="1" min="1" max="20" class="w-14 text-center border-0 focus:ring-0 py-2">
                        <button type="button" onclick="this.previousElementSibling.stepUp()" class="px-3 py-2 text-lg">+</button>
                    </div>
                    <button type="submit"
                            :disabled="(optionNames.length > 0 && !matched) || (matched && matched.stock <= 0 && !preorder)"
                            class="btn-primary flex-1">
                        <span x-text="optionNames.length > 0 && !matched ? 'Select an option' : (isPreorder ? 'Pre-order now' : 'Add to bag')">Add to bag</span>
                    </button>
                </div>

                <button type="submit" formaction="{{ route('wishlist.toggle', $product->uuid) }}" class="text-sm text-neutral-500 underline underline-offset-4 hover:text-wine-700">
                    Save to wishlist
                </button>
            </form>

            {{-- Accordion details --}}
            <div class="mt-10 divide-y divide-neutral-200 border-t border-b border-neutral-200">
                @if($product->description)
                <details class="py-4 group">
                    <summary class="flex justify-between items-center cursor-pointer text-sm font-semibold uppercase tracking-wider">Description
                        <span class="group-open:rotate-45 transition-transform text-xl font-light">+</span></summary>
                    <div class="pt-4 text-sm text-neutral-600 leading-relaxed prose">{!! nl2br(e($product->description)) !!}</div>
                </details>
                @endif
                @if($product->material || $product->care_instructions)
                <details class="py-4 group">
                    <summary class="flex justify-between items-center cursor-pointer text-sm font-semibold uppercase tracking-wider">Fabric &amp; care
                        <span class="group-open:rotate-45 transition-transform text-xl font-light">+</span></summary>
                    <div class="pt-4 text-sm text-neutral-600 space-y-2">
                        @if($product->material)<p><span class="font-medium">Material:</span> {{ $product->material }}</p>@endif
                        @if($product->care_instructions)<p><span class="font-medium">Care:</span> {{ $product->care_instructions }}</p>@endif
                    </div>
                </details>
                @endif
                <details class="py-4 group">
                    <summary class="flex justify-between items-center cursor-pointer text-sm font-semibold uppercase tracking-wider">Delivery &amp; returns
                        <span class="group-open:rotate-45 transition-transform text-xl font-light">+</span></summary>
                    <div class="pt-4 text-sm text-neutral-600 space-y-2">
                        <p>We dispatch every Monday, Wednesday &amp; Friday — order before 8:00 AM for same-day dispatch. Lagos: within 24h of dispatch. Outside Lagos: 48–72h.</p>
                        <p>Delivery is paid separately — after you order, we'll WhatsApp you the cost for your area. Pay the rider or transfer to us.</p>
                        <p>Exchanges only, no refunds — <a href="{{ url('/pages/returns-exchanges') }}" class="underline">read our returns policy</a>.</p>
                    </div>
                </details>
                <details class="py-4 group">
                    <summary class="flex justify-between items-center cursor-pointer text-sm font-semibold uppercase tracking-wider">Size guide
                        <span class="group-open:rotate-45 transition-transform text-xl font-light">+</span></summary>
                    <div class="pt-4 text-sm text-neutral-600">
                        <a href="{{ url('/pages/size-guide') }}" class="underline">View the size guide</a>
                    </div>
                </details>
            </div>
        </div>
    </div>

    {{-- Reviews --}}
    @php $approved = $product->reviews->where('is_approved', true); @endphp
    @if($approved->isNotEmpty())
        <section class="mt-16">
            <h2 class="heading-display mb-8">Reviews</h2>
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach($approved as $review)
                    <figure class="border border-neutral-200 p-6">
                        <div class="text-wine-700 mb-2 tracking-widest">@for($i = 0; $i < $review->rating; $i++)★@endfor</div>
                        <blockquote class="text-sm text-neutral-600">{{ $review->body }}</blockquote>
                        <figcaption class="mt-3 text-xs uppercase tracking-wider text-neutral-500">{{ $review->customer_name }}</figcaption>
                    </figure>
                @endforeach
            </div>
        </section>
    @endif

    {{-- Related --}}
    @if($related->isNotEmpty())
        <section class="mt-16">
            <h2 class="heading-display mb-8">You may also like</h2>
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-6">
                @foreach($related as $rel)
                    <x-product-card :product="$rel" />
                @endforeach
            </div>
        </section>
    @endif
</div>
@endsection

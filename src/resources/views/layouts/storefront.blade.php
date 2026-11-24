<!DOCTYPE html>
<html lang="en">
@php
    $siteName = $settings['store_name'] ?? 'Styles By So Fine';
    $pageTitle = $__env->yieldContent('title') ?: ($settings['seo_title'] ?? $siteName);
    $pageDesc = $__env->yieldContent('meta_description') ?: ($settings['seo_description'] ?? 'Considered fashion for the modern woman. Made in Lagos, delivered nationwide.');
    $ogImage = $__env->yieldContent('og_image') ?: asset('images/og-image.png');
    $pageUrl = url()->current();
@endphp
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $pageTitle }}</title>
    <meta name="description" content="{{ $pageDesc }}">
    <meta name="robots" content="@yield('robots', 'index, follow, max-image-preview:large')">
    <link rel="canonical" href="{{ $__env->yieldContent('canonical') ?: $pageUrl }}">

    {{-- Open Graph / Twitter — social shares get a proper card --}}
    <meta property="og:site_name" content="{{ $siteName }}">
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $pageDesc }}">
    <meta property="og:url" content="{{ $__env->yieldContent('canonical') ?: $pageUrl }}">
    <meta property="og:image" content="{{ $ogImage }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $pageTitle }}">
    <meta name="twitter:description" content="{{ $pageDesc }}">
    <meta name="twitter:image" content="{{ $ogImage }}">
    <meta name="theme-color" content="#3d1220">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    {{-- Non-blocking font load: render page immediately, swap fonts in async --}}
    <link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Inter:wght@300..700&display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Inter:wght@300..700&display=swap" media="print" onload="this.media='all'">
    <noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Inter:wght@300..700&display=swap"></noscript>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Sitewide Organization schema --}}
    <script type="application/ld+json">{!! json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'ClothingStore',
        'name' => $siteName,
        'url' => url('/'),
        'logo' => asset('images/logo.png'),
        'description' => $pageDesc,
        'telephone' => $settings['whatsapp_number'] ?? null,
        'email' => $settings['contact_email'] ?? null,
        'sameAs' => array_values(array_filter([$settings['instagram_url'] ?? null])),
    ], JSON_UNESCAPED_SLASHES) !!}</script>
    @yield('json_ld')
</head>
<body class="min-h-screen flex flex-col" x-data="{ mobileNav: false, searchOpen: false }">

    {{-- Announcement bar --}}
    @if($settings['announcement_bar'] ?? false)
        <div class="bg-wine-800 text-cream text-center text-xs sm:text-sm py-2 px-4 tracking-wide">
            {{ $settings['announcement_bar'] }}
        </div>
    @endif

    {{-- Header --}}
    <header class="sticky top-0 z-40 bg-cream/95 backdrop-blur border-b border-neutral-200">
        <div class="section-pad flex items-center justify-between h-16 lg:h-20">
            {{-- Mobile menu button --}}
            <button class="lg:hidden p-2 -ml-2" @click="mobileNav = true" aria-label="Open menu">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/></svg>
            </button>

            {{-- Logo --}}
            <a href="{{ route('home') }}" aria-label="{{ $settings['store_name'] ?? 'Styles By So Fine' }} — home">
                <img src="{{ ($settings['logo'] ?? false) ? Storage::url($settings['logo']) : asset('images/logo.png') }}"
                     alt="{{ $settings['store_name'] ?? 'Styles By So Fine' }}" class="h-12 lg:h-16 w-auto">
            </a>

            {{-- Desktop nav --}}
            <nav class="hidden lg:flex items-center gap-8 text-sm uppercase tracking-wider">
                @foreach($navItems as $item)
                    <a href="{{ $item->resolvedUrl() }}" class="hover:text-wine-700 transition-colors">{{ $item->label }}</a>
                @endforeach
            </nav>

            {{-- Icons --}}
            <div class="flex items-center gap-1 sm:gap-3">
                <a href="{{ route('shop') }}" class="p-2 hover:text-wine-700" aria-label="Search">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/></svg>
                </a>
                <a href="{{ route('wishlist.index') }}" class="p-2 hover:text-wine-700" aria-label="Wishlist">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z"/></svg>
                </a>
                <a href="{{ route('cart.index') }}" class="p-2 hover:text-wine-700 relative" aria-label="Bag">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007Z"/></svg>
                    @if(($cartCount ?? 0) > 0)
                        <span class="absolute -top-0.5 -right-0.5 bg-wine-700 text-cream text-[10px] font-semibold w-4.5 h-4.5 min-w-[18px] min-h-[18px] rounded-full flex items-center justify-center">{{ $cartCount }}</span>
                    @endif
                </a>
            </div>
        </div>
    </header>

    {{-- Mobile nav drawer --}}
    <div x-show="mobileNav" x-cloak class="fixed inset-0 z-50 lg:hidden">
        <div class="absolute inset-0 bg-ink/60 backdrop-blur-sm" @click="mobileNav = false"
             x-show="mobileNav" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"></div>

        <div class="absolute left-0 top-0 bottom-0 w-[86vw] max-w-sm bg-ink text-cream shadow-2xl flex flex-col"
             x-show="mobileNav"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="-translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="-translate-x-full">

            {{-- Ambient glow --}}
            <div class="absolute -top-24 -right-24 w-72 h-72 rounded-full bg-wine-700/20 blur-[100px] pointer-events-none"></div>

            <div class="relative flex items-center justify-between px-6 h-16 border-b border-white/10">
                <span class="inline-flex bg-cream rounded-lg px-2.5 py-1.5">
                    <img src="{{ asset('images/logo.png') }}" alt="{{ $settings['store_name'] ?? 'Styles By So Fine' }}" class="h-9 w-auto">
                </span>
                <button @click="mobileNav = false" aria-label="Close menu"
                        class="w-9 h-9 rounded-full border border-white/20 flex items-center justify-center hover:bg-white/10 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" d="M6 18 18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <nav class="relative flex-1 overflow-y-auto px-6 py-6">
                <p class="text-[10px] font-bold uppercase tracking-[0.3em] text-wine-300 mb-4">Shop</p>
                @foreach($navItems as $item)
                    <a href="{{ $item->resolvedUrl() }}" @click="mobileNav = false"
                       class="group flex items-center justify-between py-3 border-b border-white/10">
                        <span class="flex items-baseline gap-3">
                            <span class="text-[10px] text-neutral-500 font-mono">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                            <span class="font-display text-xl group-active:text-wine-300 transition-colors">{{ $item->label }}</span>
                        </span>
                        <svg class="w-4 h-4 text-neutral-500 group-active:text-wine-300 transition-colors" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/></svg>
                    </a>
                @endforeach

                <p class="text-[10px] font-bold uppercase tracking-[0.3em] text-wine-300 mt-8 mb-3">Help &amp; info</p>
                <div class="grid grid-cols-2 gap-2">
                    <a href="{{ route('track') }}" class="rounded-lg border border-white/15 px-3 py-3 text-sm text-neutral-300 hover:border-wine-400 hover:text-cream transition-colors">Track order</a>
                    <a href="{{ route('contact') }}" class="rounded-lg border border-white/15 px-3 py-3 text-sm text-neutral-300 hover:border-wine-400 hover:text-cream transition-colors">Contact us</a>
                    <a href="{{ url('/pages/size-guide') }}" class="rounded-lg border border-white/15 px-3 py-3 text-sm text-neutral-300 hover:border-wine-400 hover:text-cream transition-colors">Size guide</a>
                    <a href="{{ url('/pages/faq') }}" class="rounded-lg border border-white/15 px-3 py-3 text-sm text-neutral-300 hover:border-wine-400 hover:text-cream transition-colors">FAQ</a>
                </div>
            </nav>

            {{-- Bottom: contact chips + tagline --}}
            <div class="relative px-6 pb-8 pt-4 border-t border-white/10">
                <div class="flex gap-2 mb-5">
                    @if($settings['whatsapp_number'] ?? false)
                        <a href="https://wa.me/{{ preg_replace('/\D/', '', $settings['whatsapp_number']) }}"
                           class="flex-1 rounded-full bg-wine-700 text-center py-3 text-xs font-semibold uppercase tracking-wider hover:bg-wine-600 transition-colors">WhatsApp us</a>
                    @endif
                    @if($settings['instagram_url'] ?? false)
                        <a href="{{ $settings['instagram_url'] }}" target="_blank" rel="noopener"
                           class="flex-1 rounded-full border border-white/20 text-center py-3 text-xs font-semibold uppercase tracking-wider hover:bg-white/10 transition-colors">Instagram</a>
                    @endif
                </div>
                <p class="text-center text-[10px] uppercase tracking-[0.3em] text-neutral-500">Dress well · Feel confident · Be So Fine</p>
            </div>
        </div>
    </div>

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="bg-wine-700 text-cream text-center py-3 px-4 text-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-700 text-white text-center py-3 px-4 text-sm">{{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="bg-red-50 border-b border-red-200 text-red-800 py-3 px-4 text-sm text-center">
            {{ $errors->first() }}
        </div>
    @endif

    {{-- Content --}}
    <main class="flex-1">
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="bg-ink text-cream mt-20">
        <div class="section-pad py-14 grid grid-cols-2 lg:grid-cols-5 gap-x-6 gap-y-10 sm:gap-10">
            {{-- Brand + newsletter --}}
            <div class="col-span-2">
                <p class="mb-4"><span class="inline-flex bg-cream rounded-xl px-3 py-2">
                    <img src="{{ asset('images/logo.png') }}" alt="{{ $settings['store_name'] ?? 'Styles By So Fine' }}" class="h-14 w-auto">
                </span></p>
                <p class="text-sm text-neutral-400 leading-relaxed max-w-sm">{{ $settings['footer_about'] ?? 'Considered fashion for the modern woman.' }}</p>
                <form action="{{ route('newsletter') }}" method="POST" class="mt-6 max-w-sm">
                    @csrf
                    <div class="flex">
                        <input type="email" name="email" required placeholder="Your email" class="flex-1 rounded-l-full bg-white/10 border border-white/20 px-4 py-2.5 text-sm placeholder-neutral-500 focus:outline-none focus:border-wine-300">
                        <button class="rounded-r-full bg-wine-700 hover:bg-wine-600 px-5 text-sm font-semibold uppercase tracking-wider transition-colors">Join</button>
                    </div>
                    <p class="text-[11px] text-neutral-500 mt-2">By joining you agree to receive marketing emails. Unsubscribe anytime — every email has a link.</p>
                </form>
                <div class="flex gap-5 mt-5 text-sm text-neutral-400">
                    @if($settings['instagram_url'] ?? false)
                        <a href="{{ $settings['instagram_url'] }}" target="_blank" rel="noopener" class="hover:text-cream inline-flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.2" cy="6.8" r="0.9" fill="currentColor" stroke="none"/></svg>
                            Instagram</a>
                    @endif
                    @if($settings['whatsapp_number'] ?? false)
                        <a href="https://wa.me/{{ preg_replace('/\D/', '', $settings['whatsapp_number']) }}" target="_blank" rel="noopener" class="hover:text-cream inline-flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 0 1-2.555-.337A5.972 5.972 0 0 1 5.41 20.97a5.969 5.969 0 0 1-.474-.065 4.48 4.48 0 0 0 .978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25Z"/></svg>
                            WhatsApp</a>
                    @endif
                </div>
            </div>

            {{-- Shop --}}
            <div>
                <p class="eyebrow text-wine-300 mb-4">Shop</p>
                <ul class="space-y-2 text-sm text-neutral-300">
                    <li><a href="{{ route('shop') }}" class="hover:text-cream">Shop All</a></li>
                    @foreach($navItems->take(5) as $item)
                        <li><a href="{{ $item->resolvedUrl() }}" class="hover:text-cream">{{ $item->label }}</a></li>
                    @endforeach
                </ul>
            </div>

            {{-- Help --}}
            <div>
                <p class="eyebrow text-wine-300 mb-4">Help</p>
                <ul class="space-y-2 text-sm text-neutral-300">
                    <li><a href="{{ route('track') }}" class="hover:text-cream">Track My Order</a></li>
                    <li><a href="{{ route('contact') }}" class="hover:text-cream">Contact Us</a></li>
                    <li><a href="{{ url('/pages/faq') }}" class="hover:text-cream">FAQ</a></li>
                    <li><a href="{{ url('/pages/shipping-delivery') }}" class="hover:text-cream">Shipping &amp; Delivery</a></li>
                    <li><a href="{{ url('/pages/returns-exchanges') }}" class="hover:text-cream">Returns &amp; Exchanges</a></li>
                    <li><a href="{{ url('/pages/size-guide') }}" class="hover:text-cream">Size Guide</a></li>
                </ul>
            </div>

            {{-- Company / legal pages --}}
            <div>
                <p class="eyebrow text-wine-300 mb-4">Company</p>
                <ul class="space-y-2 text-sm text-neutral-300">
                    <li><a href="{{ url('/pages/about') }}" class="hover:text-cream">About Us</a></li>
                    <li><a href="{{ url('/pages/privacy-policy') }}" class="hover:text-cream">Privacy Policy</a></li>
                    <li><a href="{{ url('/pages/terms') }}" class="hover:text-cream">Terms &amp; Conditions</a></li>
                </ul>
            </div>

            {{-- Visit us (mobile only — fills the last grid cell) --}}
            <div class="lg:hidden">
                <p class="eyebrow text-wine-300 mb-4">Visit us</p>
                <ul class="space-y-2 text-sm text-neutral-300">
                    @if($settings['business_address'] ?? false)
                        <li class="text-neutral-400">{{ $settings['business_address'] }}</li>
                    @endif
                    @if($settings['contact_email'] ?? false)
                        <li><a href="mailto:{{ $settings['contact_email'] }}" class="hover:text-cream">{{ $settings['contact_email'] }}</a></li>
                    @endif
                    @if($settings['whatsapp_number'] ?? false)
                        <li><a href="https://wa.me/{{ preg_replace('/\D/', '', $settings['whatsapp_number']) }}" class="hover:text-cream">{{ $settings['whatsapp_number'] }}</a></li>
                    @endif
                    @if($settings['business_hours'] ?? false)
                        <li class="text-neutral-400">{{ $settings['business_hours'] }}</li>
                    @endif
                </ul>
            </div>
        </div>
        <div class="border-t border-white/10 py-5 text-center text-xs text-neutral-500">
            &copy; {{ date('Y') }} {{ $settings['store_name'] ?? 'Styles By So Fine' }} &mdash; All rights reserved.
            <span class="block sm:inline sm:ml-2 mt-1 sm:mt-0">Built by <a href="https://fortunegabriel.de" target="_blank" rel="noopener" class="text-neutral-300 hover:text-wine-300 transition-colors">fortunegabriel.de</a></span>
        </div>
    </footer>

    {{-- Cookie notice (essential cookies only) --}}
    <div x-data="{ show: !localStorage.getItem('sbf-cookies') }" x-show="show" x-cloak
         x-transition:enter="transition ease-out duration-300" x-transition:enter-start="translate-y-full" x-transition:enter-end="translate-y-0"
         class="fixed bottom-4 left-4 right-4 sm:right-auto sm:max-w-sm z-50 rounded-xl bg-ink text-cream shadow-2xl p-5 border border-white/10">
        <p class="text-sm leading-relaxed">We use essential cookies only — they keep your cart and session working. No tracking, no ads.</p>
        <div class="flex items-center gap-4 mt-4">
            <button @click="localStorage.setItem('sbf-cookies', 'ok'); show = false"
                    class="rounded-full bg-cream text-ink px-5 py-2 text-xs font-semibold uppercase tracking-wider hover:bg-wine-200 transition-colors">Got it</button>
            <a href="{{ url('/pages/privacy-policy') }}" class="text-xs text-neutral-400 underline underline-offset-2 hover:text-cream">Privacy policy</a>
        </div>
    </div>
</body>
</html>

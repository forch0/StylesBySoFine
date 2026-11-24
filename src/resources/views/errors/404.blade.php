<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Page not found — Styles By So Fine</title>
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Inter:wght@300..700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-cream min-h-screen text-ink antialiased">

<div class="min-h-screen grid lg:grid-cols-[1.15fr_1fr]">

    {{-- Visual panel --}}
    <div class="relative hidden lg:block overflow-hidden">
        <img src="https://picsum.photos/seed/sbf-atelier/1400/1800" alt=""
             class="absolute inset-0 w-full h-full object-cover scale-105">
        <div class="absolute inset-0 bg-gradient-to-t from-ink via-ink/50 to-wine-900/50"></div>
        <div class="absolute inset-0 bg-wine-900/20 mix-blend-multiply"></div>

        <div class="relative h-full flex flex-col justify-between p-14 xl:p-20 text-cream">
            <p><a href="{{ url('/') }}" class="inline-flex bg-cream rounded-xl px-3 py-2">
                <img src="{{ asset('images/logo.png') }}" alt="Styles By So Fine" class="h-14 w-auto">
            </a></p>

            <div>
                <p class="eyebrow text-wine-200 mb-6">Error 404</p>
                <h2 class="font-display text-6xl xl:text-7xl leading-[0.92] tracking-tight">
                    This thread<br>
                    <span class="italic text-wine-200">unravelled.</span>
                </h2>
                <p class="mt-8 max-w-sm text-sm leading-relaxed text-neutral-300">
                    The page you're looking for has been moved, renamed, or never made it off the cutting table.
                </p>
            </div>

            <div class="flex items-center justify-between text-[11px] uppercase tracking-[0.35em] text-neutral-400">
                <span>Dress well</span>
                <span class="text-wine-300">✦</span>
                <span>Feel confident</span>
                <span class="text-wine-300">✦</span>
                <span>Be So Fine</span>
            </div>
        </div>
    </div>

    {{-- Content panel --}}
    <div class="relative flex items-center justify-center p-6 sm:p-12 overflow-hidden">
        {{-- Ambient glow --}}
        <div class="absolute -top-40 -right-40 w-96 h-96 rounded-full bg-wine-700/10 blur-[120px] pointer-events-none"></div>
        <div class="absolute -bottom-40 -left-40 w-96 h-96 rounded-full bg-wine-900/10 blur-[120px] pointer-events-none"></div>

        <div class="relative w-full max-w-sm text-center">
            {{-- Mobile logo --}}
            <p class="lg:hidden mb-10"><a href="{{ url('/') }}" class="inline-flex">
                <img src="{{ asset('images/logo.png') }}" alt="Styles By So Fine" class="h-20 w-auto">
            </a></p>

            <p class="eyebrow text-wine-700 mb-3 lg:hidden">Error 404</p>
            <p class="eyebrow text-wine-700 mb-3 hidden lg:block">Page not found</p>
            <h1 class="font-display text-4xl sm:text-5xl leading-[1.02] tracking-tight">
                Nothing<br><span class="italic text-wine-700">here.</span>
            </h1>

            <p class="mt-6 text-sm leading-relaxed text-neutral-500 lg:hidden">
                Moved, renamed, or never made it off the cutting table.
            </p>

            <div class="mt-10 space-y-3">
                <a href="{{ url('/') }}" class="btn-primary w-full justify-center !py-4">Back to the boutique</a>
                <a href="{{ url('/shop') }}" class="block w-full rounded-full border border-neutral-300 px-7 py-3.5 text-xs font-semibold uppercase tracking-[0.15em] text-ink hover:border-ink transition-colors">
                    Continue shopping
                </a>
            </div>

            <div class="mt-10 flex items-center justify-center gap-4 text-[10px] uppercase tracking-[0.3em] text-neutral-400">
                <a href="{{ url('/pages/faq') }}" class="hover:text-wine-700 transition-colors">FAQ</a>
                <span class="text-wine-700">✦</span>
                <a href="{{ url('/contact') }}" class="hover:text-wine-700 transition-colors">Contact</a>
                <span class="text-wine-700">✦</span>
                <a href="{{ url('/track') }}" class="hover:text-wine-700 transition-colors">Track order</a>
            </div>
        </div>
    </div>
</div>

</body>
</html>

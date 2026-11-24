<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin sign in — {{ \App\Models\SiteSetting::get('store_name', 'Styles By So Fine') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Inter:wght@300..700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-ink min-h-screen text-cream antialiased">

<div class="min-h-screen grid lg:grid-cols-[1.15fr_1fr]">

    {{-- Visual panel --}}
    <div class="relative hidden lg:block overflow-hidden">
        <img src="https://picsum.photos/seed/sbf-atelier/1400/1800" alt=""
             class="absolute inset-0 w-full h-full object-cover scale-105">
        <div class="absolute inset-0 bg-gradient-to-t from-ink via-ink/50 to-wine-900/50"></div>
        <div class="absolute inset-0 bg-wine-900/20 mix-blend-multiply"></div>

        <div class="relative h-full flex flex-col justify-between p-14 xl:p-20">
            <p><span class="inline-flex bg-cream rounded-xl px-3 py-2">
                <img src="{{ asset('images/logo.png') }}" alt="Styles By So Fine" class="h-14 w-auto">
            </span></p>

            <div>
                <p class="eyebrow text-wine-200 mb-6">Back office</p>
                <h2 class="font-display text-6xl xl:text-7xl leading-[0.92] tracking-tight">
                    The atelier,<br>
                    <span class="italic text-wine-200">behind</span> the curtain.
                </h2>
                <p class="mt-8 max-w-sm text-sm leading-relaxed text-neutral-300">
                    Catalogue, orders, walk-in sales and the homepage — the whole house, run from one room.
                </p>
            </div>

            <div class="flex items-center justify-between text-[11px] uppercase tracking-[0.35em] text-neutral-400">
                <span>Lagos, Nigeria</span>
                <span class="text-wine-300">✦</span>
                <span>Nationwide delivery</span>
            </div>
        </div>
    </div>

    {{-- Form panel --}}
    <div class="relative flex items-center justify-center p-6 sm:p-12 overflow-hidden">
        {{-- Ambient glow --}}
        <div class="absolute -top-40 -right-40 w-96 h-96 rounded-full bg-wine-700/25 blur-[120px] pointer-events-none"></div>
        <div class="absolute -bottom-40 -left-40 w-96 h-96 rounded-full bg-wine-900/40 blur-[120px] pointer-events-none"></div>

        <div class="relative w-full max-w-sm">
            {{-- Mobile logo --}}
            <p class="lg:hidden text-center mb-10"><span class="inline-flex bg-cream rounded-xl px-3 py-2">
                <img src="{{ asset('images/logo.png') }}" alt="Styles By So Fine" class="h-16 w-auto">
            </span></p>

            <p class="eyebrow text-wine-300 mb-3">Admin access</p>
            <h1 class="font-display text-4xl sm:text-5xl leading-[1.02] tracking-tight mb-10">
                Welcome<br><span class="italic text-wine-200">back.</span>
            </h1>

            @if(session('error'))
                <p class="mb-6 rounded-xl border border-red-400/30 bg-red-500/10 px-4 py-3 text-sm text-red-300">{{ session('error') }}</p>
            @endif

            <form action="{{ route('admin.login.submit') }}" method="POST" class="space-y-5">
                @csrf
                <div>
                    <label class="mb-2 block text-[11px] font-semibold uppercase tracking-[0.2em] text-neutral-400" for="email">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                           class="w-full rounded-full border border-white/15 bg-white/5 px-5 py-3.5 text-sm text-cream placeholder-neutral-500 transition focus:border-wine-300 focus:outline-none focus:ring-2 focus:ring-wine-700/40"
                           placeholder="you@stylesbysofine.ng">
                </div>
                <div>
                    <label class="mb-2 block text-[11px] font-semibold uppercase tracking-[0.2em] text-neutral-400" for="password">Password</label>
                    <input id="password" type="password" name="password" required
                           class="w-full rounded-full border border-white/15 bg-white/5 px-5 py-3.5 text-sm text-cream placeholder-neutral-500 transition focus:border-wine-300 focus:outline-none focus:ring-2 focus:ring-wine-700/40"
                           placeholder="••••••••">
                </div>
                <button class="btn-primary w-full !py-4 mt-2">Sign in</button>
            </form>

            <div class="mt-10 flex items-center justify-between text-xs text-neutral-500">
                <a href="{{ route('home') }}" class="hover:text-cream transition-colors">← Back to store</a>
                <span class="uppercase tracking-[0.25em]">SBF Studio</span>
            </div>
        </div>
    </div>
</div>

</body>
</html>

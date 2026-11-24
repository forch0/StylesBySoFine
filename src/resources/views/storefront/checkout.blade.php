@extends('layouts.storefront')

@section('title', 'Checkout — '.($settings['store_name'] ?? ''))
@section('robots', 'noindex, nofollow')

@section('content')
@php
    $req = '<span class="text-wine-700">*</span>';
    $opt = '<span class="ml-1 normal-case font-normal text-neutral-400 text-[11px]">(optional)</span>';
@endphp

<div class="section-pad py-10 lg:py-14 max-w-6xl mx-auto">
    <div class="mb-10">
        <p class="eyebrow mb-2">Almost there</p>
        <h1 class="heading-display">Checkout</h1>
        <p class="mt-3 text-sm text-neutral-500">Fields marked <span class="text-wine-700 font-semibold">*</span> are required — everything else is optional.</p>
    </div>

    @if(!empty($problems))
        <div class="rounded-xl bg-red-50 border border-red-200 text-red-800 p-4 mb-8 text-sm space-y-1">
            @foreach($problems as $problem)<p>{{ $problem }}</p>@endforeach
        </div>
    @endif

    <form action="{{ route('checkout.store') }}" method="POST" class="grid lg:grid-cols-[1fr_380px] gap-8 lg:gap-10 items-start">
        @csrf
        <div class="space-y-6">
            {{-- Step 1: Contact --}}
            <section class="card p-6 sm:p-8">
                <div class="flex items-center gap-4 mb-6">
                    <span class="w-9 h-9 rounded-full bg-wine-700 text-cream flex items-center justify-center font-display text-sm shrink-0">1</span>
                    <div>
                        <h2 class="font-display text-xl">Contact details</h2>
                        <p class="text-xs text-neutral-500">We'll WhatsApp this number about delivery &amp; your order.</p>
                    </div>
                </div>
                <div class="grid sm:grid-cols-2 gap-5">
                    <div class="sm:col-span-2">
                        <label class="label" for="name">Full name {!! $req !!}</label>
                        <input class="input" id="name" name="name" value="{{ old('name') }}" placeholder="e.g. Adaeze Okafor" required>
                    </div>
                    <div>
                        <label class="label" for="email">Email {!! $req !!}</label>
                        <input class="input" id="email" type="email" name="email" value="{{ old('email') }}" placeholder="you@email.com" required>
                    </div>
                    <div>
                        <label class="label" for="phone">Phone / WhatsApp {!! $req !!}</label>
                        <input class="input" id="phone" name="phone" value="{{ old('phone') }}" placeholder="0801 234 5678" required>
                    </div>
                </div>
            </section>

            {{-- Step 2: Delivery --}}
            <section class="card p-6 sm:p-8">
                <div class="flex items-center gap-4 mb-6">
                    <span class="w-9 h-9 rounded-full bg-wine-700 text-cream flex items-center justify-center font-display text-sm shrink-0">2</span>
                    <div>
                        <h2 class="font-display text-xl">Delivery</h2>
                        <p class="text-xs text-neutral-500">We dispatch Mon, Wed &amp; Fri — order before 8:00 AM for same-day dispatch.</p>
                    </div>
                </div>
                <div class="grid sm:grid-cols-2 gap-5">
                    <div class="sm:col-span-2">
                        <label class="label" for="address">Delivery address {!! $req !!}</label>
                        <textarea class="input" id="address" name="address" rows="2" placeholder="Street, area, landmark…" required>{{ old('address') }}</textarea>
                    </div>
                    <div>
                        <label class="label" for="city">City {!! $req !!}</label>
                        <input class="input" id="city" name="city" value="{{ old('city') }}" placeholder="e.g. Lagos" required>
                    </div>
                    <div>
                        <label class="label" for="state">State {!! $req !!}</label>
                        <input class="input" id="state" name="state" value="{{ old('state') }}" placeholder="e.g. Lagos" required>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="label">Delivery area {!! $req !!}</label>
                        <div class="grid sm:grid-cols-2 gap-2" x-data="{ zoneId: '{{ old('delivery_zone_id', '') }}' }">
                            @foreach($zones as $zone)
                                <label class="flex items-center gap-3 rounded-lg border px-4 py-3.5 cursor-pointer transition-all"
                                       :class="zoneId == '{{ $zone->id }}' ? 'border-wine-700 bg-wine-50 ring-1 ring-wine-700' : 'border-neutral-300 hover:border-neutral-400'">
                                    <input type="radio" name="delivery_zone_id" value="{{ $zone->id }}" required
                                           x-model="zoneId" class="accent-wine-700" {{ old('delivery_zone_id') == $zone->id ? 'checked' : '' }}>
                                    <span>
                                        <span class="text-sm font-medium">{{ $zone->name }}</span>
                                        @if($zone->estimated_delivery)<span class="block text-xs text-neutral-500">{{ $zone->estimated_delivery }}</span>@endif
                                    </span>
                                </label>
                            @endforeach
                        </div>
                        <div class="mt-3 flex gap-3 rounded-lg bg-wine-50 border border-wine-200 p-4 text-xs text-wine-900 leading-relaxed">
                            <svg class="w-5 h-5 shrink-0 text-wine-700" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 0 0-10.026 0 1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12"/></svg>
                            <p><span class="font-semibold">Delivery is paid separately.</span> After your order, we'll WhatsApp you the delivery cost for your area — you can pay the delivery rider on arrival or transfer to us directly.</p>
                        </div>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="label" for="delivery_instructions">Delivery instructions {!! $opt !!}</label>
                        <input class="input" id="delivery_instructions" name="delivery_instructions" value="{{ old('delivery_instructions') }}" placeholder="e.g. Call when you arrive, leave with security">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="label" for="notes">Order notes {!! $opt !!}</label>
                        <input class="input" id="notes" name="notes" value="{{ old('notes') }}" placeholder="Anything we should know?">
                    </div>
                </div>
            </section>
        </div>

        {{-- Summary --}}
        <aside class="card p-6 sm:p-7 sticky top-24">
            <h2 class="font-display text-xl mb-5">Your order</h2>
            <div class="space-y-4 max-h-64 overflow-y-auto mb-5 pr-1">
                @foreach($cart->items as $item)
                    <div class="flex gap-3 text-sm">
                        <div class="w-12 h-14 rounded-md bg-neutral-100 overflow-hidden shrink-0">
                            @if($item->product->primaryMedia)
                                <img src="{{ $item->product->primaryMedia->url() }}" class="w-full h-full object-cover" alt="">
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-medium truncate">{{ $item->product->name }}</p>
                            @if($item->variant && $item->variant->summary() !== 'Default')
                                <p class="text-xs text-neutral-500">{{ $item->variant->summary() }}</p>
                            @endif
                            <p class="text-xs text-neutral-400">Qty {{ $item->quantity }}</p>
                            @if($item->product->allow_preorder && $item->variant && $item->variant->stock_quantity < $item->quantity)
                                <p class="text-[10px] font-semibold text-wine-700 uppercase tracking-wider mt-0.5">Pre-order</p>
                            @endif
                        </div>
                        <span class="whitespace-nowrap font-medium">{{ money($item->lineTotal()) }}</span>
                    </div>
                @endforeach
            </div>

            @php
                $preorderItems = $cart->items->filter(fn ($i) => $i->product->allow_preorder && $i->variant && $i->variant->stock_quantity < $i->quantity);
                $preorderNote = $preorderItems->map(fn ($i) => $i->product->preorder_note)->filter()->first();
            @endphp
            @if($preorderItems->isNotEmpty())
                <div class="rounded-lg bg-wine-50 border border-wine-200 p-3.5 mb-5 text-xs text-wine-900 leading-relaxed">
                    <span class="font-semibold">Contains pre-order items.</span>
                    {{ $preorderNote ?? 'Made to order — we\'ll confirm your ship date on WhatsApp.' }}
                </div>
            @endif

            <div class="mb-5">
                <label class="label" for="coupon_code">Coupon code {!! $opt !!}</label>
                <input class="input" id="coupon_code" name="coupon_code" value="{{ old('coupon_code') }}" placeholder="e.g. WELCOME10">
            </div>

            <div class="border-t border-neutral-200 pt-4 space-y-2.5 text-sm">
                <div class="flex justify-between"><span class="text-neutral-600">Subtotal</span><span>{{ money($cart->subtotal()) }}</span></div>
                <div class="flex justify-between"><span class="text-neutral-600">Delivery</span><span class="text-neutral-400">Quoted separately</span></div>
                <div class="flex justify-between font-semibold text-lg pt-3 border-t border-neutral-200">
                    <span>Total</span>
                    <span>{{ money($cart->subtotal()) }}</span>
                </div>
            </div>

            <button class="btn-primary w-full mt-6" @if(!empty($problems)) disabled @endif>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z"/></svg>
                Pay securely with Paystack
            </button>
            <p class="text-[11px] text-neutral-500 mt-3 text-center">Card, bank transfer &amp; USSD supported. You're charged for items only — delivery is arranged separately.</p>
        </aside>
    </form>
</div>
@endsection

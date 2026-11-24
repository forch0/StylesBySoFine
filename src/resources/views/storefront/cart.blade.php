@extends('layouts.storefront')

@section('title', 'Your Bag — '.($settings['store_name'] ?? ''))
@section('robots', 'noindex, nofollow')

@section('content')
<div class="section-pad py-10 lg:py-14 max-w-5xl mx-auto">
    <h1 class="heading-display mb-8">Your bag</h1>

    @if($cart->items->isEmpty())
        <div class="text-center py-16">
            <p class="font-display text-2xl mb-3">Your bag is empty</p>
            <p class="text-neutral-500 mb-6">Beautiful things are waiting for you.</p>
            <a href="{{ route('shop') }}" class="btn-primary">Start shopping</a>
        </div>
    @else
        <div class="grid lg:grid-cols-[1fr_340px] gap-10 items-start">
            {{-- Items --}}
            <div class="divide-y divide-neutral-200 border-t border-b border-neutral-200">
                @foreach($cart->items as $item)
                    <div class="py-5 flex gap-4">
                        <a href="{{ route('product.show', $item->product->slug) }}" class="w-24 aspect-[3/4] bg-neutral-100 overflow-hidden flex-shrink-0">
                            @php $m = $item->product->media->firstWhere('is_primary', true) ?? $item->product->media->first(); @endphp
                            @if($m && !$m->isVideo())
                                <img src="{{ $m->url() }}" alt="" class="w-full h-full object-cover">
                            @elseif($m)
                                <video src="{{ $m->url() }}" class="w-full h-full object-cover" muted></video>
                            @endif
                        </a>
                        <div class="flex-1 min-w-0">
                            <a href="{{ route('product.show', $item->product->slug) }}" class="font-medium hover:text-wine-700">{{ $item->product->name }}</a>
                            @if($item->variant && $item->variant->summary() !== 'Default')
                                <p class="text-xs text-neutral-500 mt-1">{{ $item->variant->summary() }}</p>
                            @endif
                            @if($item->product->allow_preorder && $item->variant && $item->variant->stock_quantity < $item->quantity)
                                <p class="mt-1.5 inline-flex items-center gap-1.5 text-[11px] font-medium text-wine-800 bg-wine-50 border border-wine-200 rounded-full px-2.5 py-0.5">
                                    Pre-order{{ $item->product->preorder_note ? ' — '.$item->product->preorder_note : '' }}
                                </p>
                            @endif
                            <p class="text-sm mt-1">{{ money($item->unit_price) }}</p>

                            <div class="flex items-center justify-between mt-3">
                                <form action="{{ route('cart.update', $item) }}" method="POST" class="flex items-center border border-neutral-300">
                                    @csrf @method('PATCH')
                                    <button name="quantity" value="{{ $item->quantity - 1 }}" class="px-3 py-1.5">−</button>
                                    <span class="w-10 text-center text-sm">{{ $item->quantity }}</span>
                                    <button name="quantity" value="{{ $item->quantity + 1 }}" class="px-3 py-1.5">+</button>
                                </form>
                                <form action="{{ route('cart.remove', $item) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button class="text-xs text-neutral-500 underline hover:text-red-700">Remove</button>
                                </form>
                            </div>
                        </div>
                        <p class="font-semibold text-sm whitespace-nowrap">{{ money($item->lineTotal()) }}</p>
                    </div>
                @endforeach
            </div>

            {{-- Summary --}}
            <aside class="card p-6 sticky top-24">
                <h2 class="font-display text-xl mb-4">Summary</h2>
                <div class="flex justify-between text-sm py-2 border-b border-neutral-100">
                    <span>Subtotal</span><span class="font-semibold">{{ money($cart->subtotal()) }}</span>
                </div>
                <p class="text-xs text-neutral-500 py-3">Delivery is paid separately — we'll WhatsApp you the cost for your area after you order.</p>
                <a href="{{ route('checkout.index') }}" class="btn-primary w-full">Checkout</a>
                <a href="{{ route('shop') }}" class="block text-center text-sm mt-4 underline underline-offset-4 text-neutral-500 hover:text-wine-700">Continue shopping</a>
            </aside>
        </div>
    @endif
</div>
@endsection

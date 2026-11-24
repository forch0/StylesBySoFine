@extends('layouts.storefront')

@section('title', 'Wishlist — '.($settings['store_name'] ?? ''))
@section('robots', 'noindex, nofollow')

@section('content')
<div class="section-pad py-10 lg:py-14">
    <h1 class="heading-display mb-8">Wishlist</h1>

    @if($wishlist->products->isEmpty())
        <div class="text-center py-16">
            <p class="font-display text-2xl mb-3">Nothing saved yet</p>
            <p class="text-neutral-500 mb-6">Tap the wishlist link on any product to keep it here.</p>
            <a href="{{ route('shop') }}" class="btn-primary">Browse the shop</a>
        </div>
    @else
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-6">
            @foreach($wishlist->products as $product)
                <div class="relative">
                    <x-product-card :product="$product" />
                    <form action="{{ route('wishlist.toggle', $product->uuid) }}" method="POST" class="absolute top-2 right-2">
                        @csrf
                        <button class="bg-cream/90 rounded-full p-2 shadow" aria-label="Remove from wishlist">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" d="M6 18 18 6M6 6l12 12"/></svg>
                        </button>
                    </form>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection

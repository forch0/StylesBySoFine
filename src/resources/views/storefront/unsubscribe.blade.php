@extends('layouts.storefront')

@section('title', 'Unsubscribe — '.($settings['store_name'] ?? ''))
@section('robots', 'noindex, nofollow')

@section('content')
<div class="section-pad py-16 max-w-md mx-auto text-center">
    <h1 class="heading-display mb-4">Unsubscribe?</h1>
    <p class="text-neutral-600 mb-8">You're about to remove <span class="font-medium text-ink">{{ $subscriber->email }}</span> from our mailing list. You'll stop receiving new drops and offers.</p>
    <form action="{{ route('newsletter.unsubscribe', $subscriber->unsubscribe_token) }}" method="POST">
        @csrf
        <button class="btn-primary w-full">Yes, unsubscribe me</button>
    </form>
    <a href="{{ route('home') }}" class="block mt-5 text-sm text-neutral-500 underline underline-offset-4 hover:text-wine-700">Actually, keep me subscribed</a>
</div>
@endsection

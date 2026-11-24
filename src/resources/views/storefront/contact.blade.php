@extends('layouts.storefront')

@section('title', 'Contact — '.($settings['store_name'] ?? ''))

@section('content')
<div class="section-pad py-12 lg:py-16">
    <div class="max-w-2xl mx-auto text-center mb-12">
        <p class="eyebrow mb-3">Say hello</p>
        <h1 class="heading-display">We'd love to hear from you</h1>
        <p class="mt-4 text-neutral-600">Questions about an order, sizing, or a piece you've got your eye on — send us a message.</p>
    </div>

    {{-- Contact channels — 3 columns --}}
    <div class="grid sm:grid-cols-3 gap-4 max-w-5xl mx-auto mb-10">
        @if($info['whatsapp_number'])
            <a href="https://wa.me/{{ preg_replace('/\D/', '', $info['whatsapp_number']) }}" target="_blank" rel="noopener"
               class="card p-5 flex items-center gap-4 hover:border-wine-700 hover:shadow-md transition-all">
                <span class="w-11 h-11 rounded-full bg-wine-700 flex items-center justify-center text-cream shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 0 1-2.555-.337A5.972 5.972 0 0 1 5.41 20.97a5.969 5.969 0 0 1-.474-.065 4.48 4.48 0 0 0 .978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25Z"/></svg>
                </span>
                <div><p class="font-semibold text-sm">WhatsApp us</p><p class="text-xs text-neutral-500">Fastest response</p></div>
            </a>
        @endif
        @if($info['contact_email'])
            <a href="mailto:{{ $info['contact_email'] }}" class="card p-5 flex items-center gap-4 hover:border-wine-700 hover:shadow-md transition-all">
                <span class="w-11 h-11 rounded-full bg-ink flex items-center justify-center text-cream shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/></svg>
                </span>
                <div><p class="font-semibold text-sm break-all">{{ $info['contact_email'] }}</p><p class="text-xs text-neutral-500">Email us</p></div>
            </a>
        @endif
        @if($info['instagram_url'])
            <a href="{{ $info['instagram_url'] }}" target="_blank" rel="noopener" class="card p-5 flex items-center gap-4 hover:border-wine-700 hover:shadow-md transition-all">
                <span class="w-11 h-11 rounded-full bg-wine-500 flex items-center justify-center text-cream shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.2" cy="6.8" r="0.9" fill="currentColor" stroke="none"/></svg>
                </span>
                <div><p class="font-semibold text-sm">Instagram</p><p class="text-xs text-neutral-500">@stylesby_sofine</p></div>
            </a>
        @endif
    </div>

    <div class="grid lg:grid-cols-[1fr_360px] gap-10 max-w-5xl mx-auto items-start">
        <form action="{{ route('contact.send') }}" method="POST" class="card p-6 sm:p-8 space-y-5">
            @csrf
            <input type="text" name="website" class="hidden" tabindex="-1" autocomplete="off">
            <div class="grid sm:grid-cols-2 gap-5">
                <div>
                    <label class="label" for="name">Full name</label>
                    <input class="input" id="name" name="name" value="{{ old('name') }}" required>
                </div>
                <div>
                    <label class="label" for="email">Email</label>
                    <input class="input" id="email" type="email" name="email" value="{{ old('email') }}" required>
                </div>
            </div>
            <div class="grid sm:grid-cols-2 gap-5">
                <div>
                    <label class="label" for="phone">Phone <span class="normal-case font-normal text-neutral-400">(optional)</span></label>
                    <input class="input" id="phone" name="phone" value="{{ old('phone') }}">
                </div>
                <div>
                    <label class="label" for="subject">Subject</label>
                    <input class="input" id="subject" name="subject" value="{{ old('subject') }}" required>
                </div>
            </div>
            <div>
                <label class="label" for="message">Message</label>
                <textarea class="input" id="message" name="message" rows="5" required>{{ old('message') }}</textarea>
            </div>
            <button class="btn-primary w-full sm:w-auto">Send message</button>
        </form>

        <aside class="space-y-4">
            <div class="card p-5 text-sm text-neutral-600 space-y-2">
                @if($info['business_hours'])<p><span class="font-medium text-ink">Hours:</span> {{ $info['business_hours'] }}</p>@endif
                @if($info['response_time'])<p><span class="font-medium text-ink">Response time:</span> {{ $info['response_time'] }}</p>@endif
                @if($info['business_address'])<p><span class="font-medium text-ink">Area:</span> {{ $info['business_address'] }}</p>@endif
            </div>
            <a href="{{ url('/pages/faq') }}" class="block text-sm underline underline-offset-4 text-neutral-500 hover:text-wine-700">Read the FAQ first →</a>
        </aside>
    </div>
</div>
@endsection

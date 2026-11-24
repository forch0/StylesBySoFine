@props(['title' => '', 'maxWidth' => 'max-w-lg'])

{{-- Requires an Alpine `open` boolean in the enclosing x-data scope --}}
<div x-show="open" x-cloak
     x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
     @keydown.escape.window="open = false"
     class="fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-ink/50 backdrop-blur-sm" @click="open = false"></div>
    <div class="relative bg-white rounded-xl shadow-2xl w-full {{ $maxWidth }} max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between px-6 pt-6 pb-4 border-b border-neutral-100 sticky top-0 bg-white rounded-t-xl">
            <h2 class="font-display text-lg">{{ $title }}</h2>
            <button type="button" @click="open = false" aria-label="Close"
                    class="w-8 h-8 rounded-full border border-neutral-200 flex items-center justify-center text-neutral-500 hover:border-neutral-400 hover:text-ink transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M6 18 18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="p-6">{{ $slot }}</div>
    </div>
</div>

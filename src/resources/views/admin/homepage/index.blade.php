@extends('layouts.admin')

@section('title', 'Homepage designs')

@section('content')
<p class="text-sm text-neutral-600 mb-6 max-w-2xl">
    Three complete homepage designs. Each keeps its own sections and content — switching designs never affects your products or orders.
    Edit a design first, then activate it when you're happy.
</p>

<div class="grid md:grid-cols-3 gap-6">
    @foreach($presets as $preset)
        <div class="card overflow-hidden {{ $preset->is_active ? 'ring-2 ring-wine-700' : '' }}">
            <div class="h-44 bg-gradient-to-br flex items-end p-4
                        {{ $preset->key === 'editorial' ? 'from-wine-900 to-wine-600' : ($preset->key === 'boutique' ? 'from-neutral-200 to-neutral-400' : 'from-pink-200 to-wine-300') }}">
                <span class="font-display text-xl {{ $preset->key === 'boutique' ? 'text-ink' : 'text-cream' }}">{{ $preset->name }}</span>
            </div>
            <div class="p-5">
                <p class="text-sm text-neutral-600 mb-4 min-h-[3rem]">{{ $preset->description }}</p>
                <p class="text-xs text-neutral-500 mb-4">{{ $preset->sections->where('is_enabled', true)->count() }} sections enabled</p>
                <div class="flex gap-2">
                    <a href="{{ route('admin.homepage.sections', $preset) }}" class="btn-outline flex-1 !py-2 text-xs">Edit sections</a>
                    @if($preset->is_active)
                        <span class="btn flex-1 !py-2 text-xs bg-wine-700 text-cream cursor-default">Live now</span>
                    @else
                        <form action="{{ route('admin.homepage.activate', $preset) }}" method="POST" class="flex-1"
                              onsubmit="return confirm('Make &quot;{{ $preset->name }}&quot; the live homepage?')">
                            @csrf
                            <button class="btn-primary w-full !py-2 text-xs">Activate</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    @endforeach
</div>
@endsection

@extends('layouts.admin')

@section('title', $message->subject)

@section('content')
<p class="mb-6"><a href="{{ route('admin.messages.index') }}" class="underline text-sm">← All messages</a></p>

<div class="card p-6 max-w-2xl">
    <div class="flex justify-between items-start mb-6">
        <div>
            <h2 class="font-display text-xl">{{ $message->subject }}</h2>
            <p class="text-sm text-neutral-500 mt-1">{{ $message->name }} &lt;{{ $message->email }}&gt;{{ $message->phone ? ' · '.$message->phone : '' }}</p>
        </div>
        <p class="text-xs text-neutral-500">{{ $message->created_at->format('j M Y, g:ia') }}</p>
    </div>
    <div class="text-sm leading-relaxed whitespace-pre-wrap">{{ $message->message }}</div>
    <div class="mt-6 pt-4 border-t border-neutral-200 flex gap-4">
        <a href="mailto:{{ $message->email }}?subject=Re: {{ $message->subject }}" class="btn-primary !py-2 text-sm">Reply by email</a>
        @if($message->phone)
            <a href="https://wa.me/{{ preg_replace('/\D/', '', $message->phone) }}" target="_blank" class="btn-outline !py-2 text-sm">WhatsApp</a>
        @endif
    </div>
</div>
@endsection

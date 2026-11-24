@extends('layouts.admin')

@section('title', 'Messages')

@section('content')
<div class="card overflow-x-auto">
    <table class="w-full text-sm">
        <thead><tr class="text-left text-xs uppercase tracking-wider text-neutral-500 border-b border-neutral-200">
            <th class="p-3">From</th><th class="p-3">Subject</th><th class="p-3">Received</th><th class="p-3"></th>
        </tr></thead>
        <tbody class="divide-y divide-neutral-100">
            @forelse($messages as $message)
                <tr class="{{ $message->is_read ? '' : 'bg-wine-50' }}">
                    <td class="p-3"><span class="{{ $message->is_read ? '' : 'font-semibold' }}">{{ $message->name }}</span><br><span class="text-xs text-neutral-500">{{ $message->email }}</span></td>
                    <td class="p-3">{{ $message->subject }}</td>
                    <td class="p-3 text-neutral-500">{{ $message->created_at->diffForHumans() }}</td>
                    <td class="p-3 text-right"><a href="{{ route('admin.messages.show', $message) }}" class="underline">Read</a></td>
                </tr>
            @empty
                <tr><td colspan="4" class="p-8 text-center text-neutral-500">No messages yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $messages->links() }}</div>
@endsection

@extends('layouts.admin')

@section('title', 'Audit log')

@section('content')
<div class="card overflow-x-auto">
    <table class="w-full text-sm">
        <thead><tr class="text-left text-xs uppercase tracking-wider text-neutral-500 border-b border-neutral-200">
            <th class="p-3">When</th><th class="p-3">User</th><th class="p-3">Action</th><th class="p-3">Subject</th><th class="p-3">IP</th>
        </tr></thead>
        <tbody class="divide-y divide-neutral-100">
            @forelse($logs as $log)
                <tr>
                    <td class="p-3 text-neutral-500">{{ $log->created_at->format('j M, g:ia') }}</td>
                    <td class="p-3">{{ $log->user?->name ?? '—' }}</td>
                    <td class="p-3 font-mono text-xs">{{ $log->action }}</td>
                    <td class="p-3 text-neutral-500 text-xs">{{ $log->subject_type ? class_basename($log->subject_type).' #'.$log->subject_id : '—' }}</td>
                    <td class="p-3 text-neutral-500 text-xs">{{ $log->ip_address }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="p-8 text-center text-neutral-500">No activity recorded.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $logs->links() }}</div>
@endsection

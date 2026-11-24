@extends('layouts.admin')

@section('title', 'Pages')

@section('content')
<div class="flex justify-between items-center mb-6">
    <p class="text-sm text-neutral-600">Content pages — about, policies, FAQ, size guide.</p>
    <a href="{{ route('admin.pages.create') }}" class="btn-primary">+ New page</a>
</div>

<div class="card overflow-x-auto">
    <table class="w-full text-sm">
        <thead><tr class="text-left text-xs uppercase tracking-wider text-neutral-500 border-b border-neutral-200">
            <th class="p-3">Title</th><th class="p-3">URL</th><th class="p-3">Published</th><th class="p-3"></th>
        </tr></thead>
        <tbody class="divide-y divide-neutral-100">
            @forelse($pages as $page)
                <tr>
                    <td class="p-3 font-medium">{{ $page->title }}</td>
                    <td class="p-3"><a href="{{ route('page.show', $page->slug) }}" target="_blank" class="underline text-neutral-500">/pages/{{ $page->slug }}</a></td>
                    <td class="p-3">{{ $page->is_published ? '✓' : '—' }}</td>
                    <td class="p-3 text-right whitespace-nowrap">
                        <a href="{{ route('admin.pages.edit', $page) }}" class="underline mr-3">Edit</a>
                        <form action="{{ route('admin.pages.destroy', $page) }}" method="POST" class="inline" onsubmit="return confirm('Delete this page?')">
                            @csrf @method('DELETE')<button class="underline text-red-700">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="p-8 text-center text-neutral-500">No pages yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection

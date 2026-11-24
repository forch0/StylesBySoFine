@extends('layouts.admin')

@section('title', 'Collections')

@section('content')
<div x-data="{ open: {{ $errors->any() ? 'true' : 'false' }} }" class="max-w-4xl">
    <div class="flex items-center justify-between mb-5">
        <p class="text-sm text-neutral-500">{{ $collections->count() }} {{ Str::plural('collection', $collections->count()) }} · add products from the product edit screen</p>
        <button @click="open = true" class="btn-primary !py-2.5 !px-5 text-xs">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            Add collection
        </button>
    </div>

    <div class="card overflow-x-auto">
        <table class="w-full text-sm">
            <thead><tr class="text-left text-xs uppercase tracking-wider text-neutral-500 border-b border-neutral-200">
                <th class="p-3">Name</th><th class="p-3">Products</th><th class="p-3">Window</th><th class="p-3">Active</th><th class="p-3"></th>
            </tr></thead>
            <tbody class="divide-y divide-neutral-100">
                @forelse($collections as $collection)
                    <tr>
                        <td class="p-3 font-medium">{{ $collection->name }}</td>
                        <td class="p-3">{{ $collection->products_count }}</td>
                        <td class="p-3 text-neutral-500 text-xs">
                            {{ $collection->starts_at?->format('j M') ?? '—' }} → {{ $collection->ends_at?->format('j M') ?? '—' }}
                        </td>
                        <td class="p-3">{{ $collection->is_active ? '✓' : '—' }}</td>
                        <td class="p-3 text-right whitespace-nowrap">
                            <form action="{{ route('admin.collections.destroy', $collection) }}" method="POST" class="inline" onsubmit="return confirm('Delete this collection?')">
                                @csrf @method('DELETE')<button class="underline text-red-700">Delete</button>
                            </form>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="5" class="p-0">
                            <details class="bg-neutral-50 px-4 py-3">
                                <summary class="text-xs uppercase tracking-wider text-neutral-500 cursor-pointer">Edit {{ $collection->name }}</summary>
                                <form action="{{ route('admin.collections.update', $collection) }}" method="POST" enctype="multipart/form-data" class="grid sm:grid-cols-2 gap-3 mt-3">
                                    @csrf @method('PUT')
                                    <input name="name" value="{{ $collection->name }}" class="input !py-2" required>
                                    <input name="description" value="{{ $collection->description }}" placeholder="Description" class="input !py-2">
                                    <input name="starts_at" type="date" value="{{ $collection->starts_at?->format('Y-m-d') }}" class="input !py-2">
                                    <input name="ends_at" type="date" value="{{ $collection->ends_at?->format('Y-m-d') }}" class="input !py-2">
                                    <input name="sort_order" type="number" value="{{ $collection->sort_order }}" class="input !py-2">
                                    <input name="image" type="file" accept="image/*" class="input !py-2">
                                    <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_active" value="1" {{ $collection->is_active ? 'checked' : '' }} class="accent-wine-700"> Active</label>
                                    <button class="btn-primary !py-2 text-sm">Save</button>
                                </form>
                            </details>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="p-8 text-center text-neutral-500">No collections yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <x-modal title="New collection">
        <form action="{{ route('admin.collections.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div>
                <label class="label">Name <span class="text-wine-700">*</span></label>
                <input name="name" value="{{ old('name') }}" class="input !py-2.5" required>
            </div>
            <div>
                <label class="label">Description</label>
                <textarea name="description" rows="2" class="input !py-2.5">{{ old('description') }}</textarea>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="label">Starts</label>
                    <input name="starts_at" type="date" value="{{ old('starts_at') }}" class="input !py-2.5">
                </div>
                <div>
                    <label class="label">Ends</label>
                    <input name="ends_at" type="date" value="{{ old('ends_at') }}" class="input !py-2.5">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="label">Sort order</label>
                    <input name="sort_order" type="number" value="{{ old('sort_order', 0) }}" class="input !py-2.5">
                </div>
                <div>
                    <label class="label">Image</label>
                    <input name="image" type="file" accept="image/*" class="input !py-2.5">
                </div>
            </div>
            <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_active" value="1" {{ old('is_active', '1') ? 'checked' : '' }} class="accent-wine-700 w-4 h-4"> Active</label>
            <button class="btn-primary w-full !py-3 text-sm">Add collection</button>
        </form>
    </x-modal>
</div>
@endsection

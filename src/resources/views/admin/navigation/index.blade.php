@extends('layouts.admin')

@section('title', 'Navigation')

@section('content')
<div x-data="{ open: {{ $errors->any() ? 'true' : 'false' }} }" class="max-w-4xl">
    <div class="flex items-center justify-between mb-5">
        <p class="text-sm text-neutral-500">{{ $items->count() }} menu {{ Str::plural('item', $items->count()) }}</p>
        <button @click="open = true" class="btn-primary !py-2.5 !px-5 text-xs">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            Add menu item
        </button>
    </div>

    <div class="card">
        <table class="w-full text-sm">
            <thead><tr class="text-left text-xs uppercase tracking-wider text-neutral-500 border-b border-neutral-200">
                <th class="p-3">Label</th><th class="p-3">URL</th><th class="p-3">Order</th><th class="p-3">Active</th><th class="p-3"></th>
            </tr></thead>
            <tbody class="divide-y divide-neutral-100">
                @forelse($items as $item)
                    <tr>
                        <td class="p-3 font-medium">{{ $item->label }}</td>
                        <td class="p-3 text-neutral-500">{{ $item->url }}</td>
                        <td class="p-3">{{ $item->sort_order }}</td>
                        <td class="p-3">{{ $item->is_active ? '✓' : '—' }}</td>
                        <td class="p-3 text-right">
                            <form action="{{ route('admin.navigation.destroy', $item) }}" method="POST" class="inline" onsubmit="return confirm('Remove this item?')">
                                @csrf @method('DELETE')<button class="underline text-red-700">Remove</button>
                            </form>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="5" class="p-0">
                            <details class="bg-neutral-50 px-4 py-3">
                                <summary class="text-xs uppercase tracking-wider text-neutral-500 cursor-pointer">Edit</summary>
                                <form action="{{ route('admin.navigation.update', $item) }}" method="POST" class="grid sm:grid-cols-4 gap-3 mt-3">
                                    @csrf @method('PUT')
                                    <input name="label" value="{{ $item->label }}" class="input !py-2" required>
                                    <input name="url" value="{{ $item->url }}" class="input !py-2 sm:col-span-2" required>
                                    <input name="sort_order" type="number" value="{{ $item->sort_order }}" class="input !py-2">
                                    <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_active" value="1" {{ $item->is_active ? 'checked' : '' }} class="accent-wine-700"> Active</label>
                                    <button class="btn-primary !py-2 text-sm">Save</button>
                                </form>
                            </details>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="p-8 text-center text-neutral-500">No menu items yet.</td></tr>
                @endforelse
            </tbody>
        </table>
        <p class="p-3 text-xs text-neutral-500">Tip: use <code>route:shop</code> style URLs for built-in pages (home, shop, contact, track, wishlist.index) or full paths like <code>/pages/faq</code>.</p>
    </div>

    <x-modal title="Add menu item">
        <form action="{{ route('admin.navigation.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="label">Label <span class="text-wine-700">*</span></label>
                <input name="label" value="{{ old('label') }}" placeholder="e.g. Dresses" class="input !py-2.5" required>
            </div>
            <div>
                <label class="label">URL <span class="text-wine-700">*</span></label>
                <input name="url" value="{{ old('url') }}" placeholder="/shop or route:shop" class="input !py-2.5" required>
                <p class="text-xs text-neutral-500 mt-1">Use <code>route:</code> for built-in pages, or a path like <code>/pages/faq</code>.</p>
            </div>
            <div>
                <label class="label">Sort order</label>
                <input name="sort_order" type="number" value="{{ old('sort_order', 0) }}" class="input !py-2.5">
            </div>
            <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_active" value="1" {{ old('is_active', '1') ? 'checked' : '' }} class="accent-wine-700 w-4 h-4"> Active</label>
            <button class="btn-primary w-full !py-3 text-sm">Add item</button>
        </form>
    </x-modal>
</div>
@endsection

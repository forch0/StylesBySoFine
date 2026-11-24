@extends('layouts.admin')

@section('title', 'Delivery zones')

@section('content')
<div x-data="{ open: {{ $errors->any() ? 'true' : 'false' }} }" class="max-w-5xl">
    <div class="flex items-center justify-between mb-5">
        <p class="text-sm text-neutral-500">{{ $zones->count() }} {{ Str::plural('zone', $zones->count()) }} · delivery is quoted per order, not charged online</p>
        <button @click="open = true" class="btn-primary !py-2.5 !px-5 text-xs">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            Add zone
        </button>
    </div>

    <div class="card overflow-x-auto">
        <table class="w-full text-sm">
            <thead><tr class="text-left text-xs uppercase tracking-wider text-neutral-500 border-b border-neutral-200">
                <th class="p-3">Zone</th><th class="p-3">Areas</th><th class="p-3">Fee</th><th class="p-3">ETA</th><th class="p-3">Active</th><th class="p-3"></th>
            </tr></thead>
            <tbody class="divide-y divide-neutral-100">
                @forelse($zones as $zone)
                    <tr>
                        <td class="p-3 font-medium">{{ $zone->name }}</td>
                        <td class="p-3 text-neutral-500 text-xs">{{ $zone->states }}</td>
                        <td class="p-3">{{ $zone->fee ? money($zone->fee) : 'Free' }}</td>
                        <td class="p-3 text-neutral-500 text-xs">{{ $zone->estimated_delivery }}</td>
                        <td class="p-3">{{ $zone->is_active ? '✓' : '—' }}</td>
                        <td class="p-3 text-right">
                            <form action="{{ route('admin.delivery-zones.destroy', $zone) }}" method="POST" class="inline" onsubmit="return confirm('Remove this zone?')">
                                @csrf @method('DELETE')<button class="underline text-red-700">Remove</button>
                            </form>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="6" class="p-0">
                            <details class="bg-neutral-50 px-4 py-3">
                                <summary class="text-xs uppercase tracking-wider text-neutral-500 cursor-pointer">Edit</summary>
                                <form action="{{ route('admin.delivery-zones.update', $zone) }}" method="POST" class="grid sm:grid-cols-3 gap-3 mt-3">
                                    @csrf @method('PUT')
                                    <input name="name" value="{{ $zone->name }}" class="input !py-2" required>
                                    <input name="states" value="{{ $zone->states }}" placeholder="States/areas, comma separated" class="input !py-2 sm:col-span-2">
                                    <input name="fee" type="number" step="0.01" value="{{ $zone->fee / 100 }}" placeholder="Fee ₦" class="input !py-2" required>
                                    <input name="estimated_delivery" value="{{ $zone->estimated_delivery }}" placeholder="e.g. 2–4 days" class="input !py-2">
                                    <input name="sort_order" type="number" value="{{ $zone->sort_order }}" class="input !py-2">
                                    <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_active" value="1" {{ $zone->is_active ? 'checked' : '' }} class="accent-wine-700"> Active</label>
                                    <button class="btn-primary !py-2 text-sm">Save</button>
                                </form>
                            </details>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="p-8 text-center text-neutral-500">No delivery zones yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <x-modal title="New delivery zone">
        <form action="{{ route('admin.delivery-zones.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="label">Zone name <span class="text-wine-700">*</span></label>
                <input name="name" value="{{ old('name') }}" placeholder="e.g. Lagos — Mainland" class="input !py-2.5" required>
            </div>
            <div>
                <label class="label">States / areas covered</label>
                <input name="states" value="{{ old('states') }}" placeholder="Comma separated, e.g. Ikeja, Yaba, Surulere" class="input !py-2.5">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="label">Delivery fee ₦</label>
                    <input name="fee" type="number" step="0.01" value="{{ old('fee', 0) }}" class="input !py-2.5" required>
                </div>
                <div>
                    <label class="label">ETA</label>
                    <input name="estimated_delivery" value="{{ old('estimated_delivery') }}" placeholder="e.g. Within 24 hrs" class="input !py-2.5">
                </div>
            </div>
            <div>
                <label class="label">Sort order</label>
                <input name="sort_order" type="number" value="{{ old('sort_order', 0) }}" class="input !py-2.5">
            </div>
            <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_active" value="1" {{ old('is_active', '1') ? 'checked' : '' }} class="accent-wine-700 w-4 h-4"> Active</label>
            <p class="text-xs text-neutral-500">Fees here are reference for your delivery quotes — online checkout doesn't charge them.</p>
            <button class="btn-primary w-full !py-3 text-sm">Add zone</button>
        </form>
    </x-modal>
</div>
@endsection

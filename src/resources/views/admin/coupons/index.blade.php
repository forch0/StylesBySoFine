@extends('layouts.admin')

@section('title', 'Coupons')

@section('content')
<div x-data="{ open: {{ $errors->any() ? 'true' : 'false' }} }" class="max-w-5xl">
    <div class="flex items-center justify-between mb-5">
        <p class="text-sm text-neutral-500">{{ $coupons->count() }} {{ Str::plural('coupon', $coupons->count()) }}</p>
        <button @click="open = true" class="btn-primary !py-2.5 !px-5 text-xs">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            Create coupon
        </button>
    </div>

    <div class="card overflow-x-auto">
        <table class="w-full text-sm">
            <thead><tr class="text-left text-xs uppercase tracking-wider text-neutral-500 border-b border-neutral-200">
                <th class="p-3">Code</th><th class="p-3">Type</th><th class="p-3">Value</th><th class="p-3">Used</th><th class="p-3">Status</th><th class="p-3"></th>
            </tr></thead>
            <tbody class="divide-y divide-neutral-100">
                @forelse($coupons as $coupon)
                    <tr>
                        <td class="p-3 font-mono font-semibold">{{ $coupon->code }}</td>
                        <td class="p-3 capitalize">{{ str_replace('_', ' ', $coupon->type) }}</td>
                        <td class="p-3">{{ $coupon->type === 'percentage' ? $coupon->value.'%' : ($coupon->type === 'fixed' ? money($coupon->value) : '—') }}</td>
                        <td class="p-3">{{ $coupon->redemptions_count }}{{ $coupon->usage_limit ? '/'.$coupon->usage_limit : '' }}</td>
                        <td class="p-3">{{ $coupon->isCurrentlyValid() ? '✓ Active' : 'Inactive' }}</td>
                        <td class="p-3 text-right whitespace-nowrap">
                            <form action="{{ route('admin.coupons.destroy', $coupon) }}" method="POST" class="inline" onsubmit="return confirm('Delete this coupon?')">
                                @csrf @method('DELETE')<button class="underline text-red-700">Delete</button>
                            </form>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="6" class="p-0">
                            <details class="bg-neutral-50 px-4 py-3">
                                <summary class="text-xs uppercase tracking-wider text-neutral-500 cursor-pointer">Edit {{ $coupon->code }}</summary>
                                <form action="{{ route('admin.coupons.update', $coupon) }}" method="POST" class="grid sm:grid-cols-3 gap-3 mt-3">
                                    @csrf @method('PUT')
                                    <input name="code" value="{{ $coupon->code }}" class="input !py-2" required>
                                    <select name="type" class="input !py-2">
                                        @foreach(['percentage','fixed','free_delivery'] as $t)
                                            <option value="{{ $t }}" {{ $coupon->type === $t ? 'selected' : '' }}>{{ ucfirst(str_replace('_',' ',$t)) }}</option>
                                        @endforeach
                                    </select>
                                    <input name="value" type="number" step="0.01" value="{{ $coupon->type === 'fixed' ? $coupon->value/100 : $coupon->value }}" class="input !py-2" required>
                                    <input name="min_order_value" type="number" step="0.01" value="{{ $coupon->min_order_value ? $coupon->min_order_value/100 : '' }}" placeholder="Min order ₦" class="input !py-2">
                                    <input name="usage_limit" type="number" value="{{ $coupon->usage_limit }}" placeholder="Usage limit" class="input !py-2">
                                    <input name="per_customer_limit" type="number" value="{{ $coupon->per_customer_limit }}" placeholder="Per customer" class="input !py-2">
                                    <input name="starts_at" type="date" value="{{ $coupon->starts_at?->format('Y-m-d') }}" class="input !py-2">
                                    <input name="ends_at" type="date" value="{{ $coupon->ends_at?->format('Y-m-d') }}" class="input !py-2">
                                    <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_active" value="1" {{ $coupon->is_active ? 'checked' : '' }} class="accent-wine-700"> Active</label>
                                    <button class="btn-primary !py-2 text-sm">Save</button>
                                </form>
                            </details>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="p-8 text-center text-neutral-500">No coupons yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <x-modal title="New coupon">
        <form action="{{ route('admin.coupons.store') }}" method="POST" class="space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="label">Code <span class="text-wine-700">*</span></label>
                    <input name="code" value="{{ old('code') }}" placeholder="e.g. WELCOME10" class="input !py-2.5 uppercase" required>
                </div>
                <div>
                    <label class="label">Type</label>
                    <select name="type" class="input !py-2.5">
                        <option value="percentage">Percentage off</option>
                        <option value="fixed">Fixed amount off (₦)</option>
                        <option value="free_delivery">Free delivery</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="label">Value (% or ₦) <span class="text-wine-700">*</span></label>
                    <input name="value" type="number" step="0.01" value="{{ old('value') }}" class="input !py-2.5" required>
                </div>
                <div>
                    <label class="label">Min order ₦</label>
                    <input name="min_order_value" type="number" step="0.01" value="{{ old('min_order_value') }}" class="input !py-2.5">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="label">Usage limit</label>
                    <input name="usage_limit" type="number" value="{{ old('usage_limit') }}" class="input !py-2.5">
                </div>
                <div>
                    <label class="label">Per customer</label>
                    <input name="per_customer_limit" type="number" value="{{ old('per_customer_limit') }}" class="input !py-2.5">
                </div>
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
            <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_active" value="1" {{ old('is_active', '1') ? 'checked' : '' }} class="accent-wine-700 w-4 h-4"> Active</label>
            <button class="btn-primary w-full !py-3 text-sm">Create coupon</button>
        </form>
    </x-modal>
</div>
@endsection

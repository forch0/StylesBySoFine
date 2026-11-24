@extends('layouts.admin')

@section('title', 'Staff accounts')

@section('content')
<div x-data="{ adding: {{ $errors->any() ? 'true' : 'false' }} }" class="max-w-4xl">

    {{-- Header + toggle --}}
    <div class="flex items-center justify-between mb-5">
        <div>
            <p class="text-sm text-neutral-500">{{ $staff->count() }} {{ Str::plural('account', $staff->count()) }} with admin access</p>
        </div>
        <button @click="adding = !adding" class="btn-primary !py-2.5 !px-5 text-xs">
            <svg x-show="!adding" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            <svg x-show="adding" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M6 18 18 6M6 6l12 12"/></svg>
            <span x-text="adding ? 'Close' : 'Add staff member'"></span>
        </button>
    </div>

    {{-- Add form (hidden until toggled) --}}
    <div x-show="adding" x-cloak x-transition.opacity.duration.200ms class="card p-6 mb-6">
        <h2 class="font-display text-lg mb-1">New staff account</h2>
        <p class="text-xs text-neutral-500 mb-5">Staff can manage orders &amp; inventory. Admins get full access including settings and this page.</p>
        <form action="{{ route('admin.staff.store') }}" method="POST" class="grid sm:grid-cols-2 gap-4">
            @csrf
            <div>
                <label class="label">Full name</label>
                <input name="name" value="{{ old('name') }}" class="input !py-2.5" required>
            </div>
            <div>
                <label class="label">Email</label>
                <input name="email" type="email" value="{{ old('email') }}" class="input !py-2.5" required>
            </div>
            <div>
                <label class="label">Password <span class="normal-case font-normal text-neutral-400">(min 8)</span></label>
                <input name="password" type="password" class="input !py-2.5" required>
            </div>
            <div>
                <label class="label">Role</label>
                <select name="role" class="input !py-2.5">
                    <option value="staff">Staff — orders &amp; inventory</option>
                    <option value="admin">Admin — full access</option>
                </select>
            </div>
            <div class="sm:col-span-2">
                <button class="btn-primary !py-2.5 !px-6 text-xs">Create account</button>
            </div>
        </form>
    </div>

    {{-- Staff list --}}
    <div class="card overflow-hidden">
        @foreach($staff as $member)
            <div class="border-b border-neutral-100 last:border-0">
                <div class="flex items-center gap-4 px-5 py-4">
                    <span class="w-10 h-10 rounded-full {{ $member->role === 'admin' ? 'bg-wine-700 text-cream' : 'bg-neutral-200 text-ink' }} flex items-center justify-center font-display shrink-0">{{ strtoupper(substr($member->name, 0, 1)) }}</span>
                    <div class="min-w-0 flex-1">
                        <p class="font-medium text-sm truncate">{{ $member->name }}
                            @if($member->id === auth()->id())<span class="text-xs text-neutral-400 font-normal">(you)</span>@endif
                        </p>
                        <p class="text-xs text-neutral-500 truncate">{{ $member->email }}</p>
                    </div>
                    <span class="hidden sm:inline-flex rounded-full px-3 py-1 text-[11px] font-semibold uppercase tracking-wider {{ $member->role === 'admin' ? 'bg-wine-50 text-wine-800' : 'bg-neutral-100 text-neutral-600' }}">{{ $member->role }}</span>
                    <span class="inline-flex items-center gap-1.5 text-xs {{ $member->is_active ? 'text-green-700' : 'text-neutral-400' }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ $member->is_active ? 'bg-green-600' : 'bg-neutral-300' }}"></span>
                        {{ $member->is_active ? 'Active' : 'Disabled' }}
                    </span>
                    <div class="flex items-center gap-3 text-sm">
                        <button type="button" @click="$refs.edit{{ $member->id }}.classList.toggle('hidden')" class="text-wine-700 hover:underline">Edit</button>
                        @if($member->id !== auth()->id())
                            <form action="{{ route('admin.staff.destroy', $member) }}" method="POST" class="inline" onsubmit="return confirm('Remove {{ $member->name }}?')">
                                @csrf @method('DELETE')
                                <button class="text-red-600 hover:underline">Remove</button>
                            </form>
                        @endif
                    </div>
                </div>

                {{-- Inline edit --}}
                <div x-ref="edit{{ $member->id }}" class="hidden bg-neutral-50 px-5 py-4 border-t border-neutral-100">
                    <form action="{{ route('admin.staff.update', $member) }}" method="POST" class="grid sm:grid-cols-2 lg:grid-cols-4 gap-3 items-end">
                        @csrf @method('PUT')
                        <div>
                            <label class="label">Name</label>
                            <input name="name" value="{{ $member->name }}" class="input !py-2" required>
                        </div>
                        <div>
                            <label class="label">Email</label>
                            <input name="email" type="email" value="{{ $member->email }}" class="input !py-2" required>
                        </div>
                        <div>
                            <label class="label">New password <span class="normal-case font-normal text-neutral-400">(blank = keep)</span></label>
                            <input name="password" type="password" class="input !py-2">
                        </div>
                        <div class="flex items-end gap-4">
                            <div>
                                <label class="label">Role</label>
                                <select name="role" class="input !py-2">
                                    <option value="staff" {{ $member->role === 'staff' ? 'selected' : '' }}>Staff</option>
                                    <option value="admin" {{ $member->role === 'admin' ? 'selected' : '' }}>Admin</option>
                                </select>
                            </div>
                            <label class="flex items-center gap-2 text-sm pb-2.5"><input type="checkbox" name="is_active" value="1" {{ $member->is_active ? 'checked' : '' }} class="accent-wine-700 w-4 h-4"> Active</label>
                            <button class="btn-primary !py-2 !px-5 text-xs">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection

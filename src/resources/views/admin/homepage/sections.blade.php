@extends('layouts.admin')

@section('title', 'Sections — '.$preset->name)

@section('content')
<p class="mb-6 text-sm"><a href="{{ route('admin.homepage.index') }}" class="underline">← Homepage designs</a></p>

<div class="max-w-3xl space-y-4">
    @foreach($preset->sections as $section)
        <div class="card p-5">
            <form action="{{ route('admin.homepage.sections.update', $section) }}" method="POST" enctype="multipart/form-data">
                @csrf @method('PATCH')
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="font-display text-lg">{{ $section->name }}</h2>
                        <p class="text-xs text-neutral-400">{{ $section->key }}</p>
                    </div>
                    <label class="flex items-center gap-2 text-sm">
                        <input type="checkbox" name="is_enabled" value="1" {{ $section->is_enabled ? 'checked' : '' }} class="accent-wine-700 w-4 h-4">
                        Enabled
                    </label>
                </div>

                @if($section->key === 'hero')
                    @include('admin.homepage.partials.hero-slides', ['section' => $section])
                @else
                <div class="grid sm:grid-cols-2 gap-3">
                    @php $fields = [
                        'marquee' => ['items'],
                        'categories' => ['eyebrow','heading'],
                        'new_arrivals' => ['eyebrow','heading'],
                        'featured_collection' => ['eyebrow','heading','text','cta_text'],
                        'brand_statement' => ['eyebrow','text'],
                        'bestsellers' => ['eyebrow','heading'],
                        'promo_banner' => ['heading','text','cta_text','cta_url','image'],
                        'editorial' => ['eyebrow','heading','text','cta_text','cta_url','image'],
                        'reviews' => ['eyebrow','heading'],
                        'instagram' => ['eyebrow','heading'],
                        'newsletter' => ['eyebrow','heading','text'],
                    ][$section->key] ?? ['heading','text']; @endphp

                    @foreach($fields as $field)
                        <div class="{{ in_array($field, ['subcopy','text','image','items']) ? 'sm:col-span-2' : '' }}">
                            <label class="label">{{ ucfirst(str_replace('_', ' ', $field)) }}{{ $field === 'items' ? ' (one per line)' : '' }}</label>
                            @if(in_array($field, ['subcopy','text','items']))
                                @php $val = $section->get($field); $val = is_array($val) ? implode("\n", $val) : $val; @endphp
                                <textarea name="content[{{ $field }}]" rows="{{ $field === 'items' ? 4 : 2 }}" class="input !py-2">{{ $val }}</textarea>
                            @else
                                <input name="content[{{ $field }}]" value="{{ $section->get($field) }}" class="input !py-2"
                                       placeholder="{{ $field === 'image' ? 'Image URL or storage path' : '' }}">
                            @endif
                        </div>
                    @endforeach
                </div>
                @endif
                <button class="btn-primary !py-2 !px-4 text-xs mt-4">Save section</button>
            </form>
        </div>
    @endforeach
</div>

<div class="card p-5 mt-6 max-w-3xl">
    <h2 class="font-display text-lg mb-3">Section order</h2>
    <form action="{{ route('admin.homepage.reorder', $preset) }}" method="POST" x-data="{
        items: {{ Js::from($preset->sections->map(fn($s) => ['id' => $s->id, 'name' => $s->name, 'on' => $s->is_enabled])->values()) }},
        move(i, dir) {
            const j = i + dir;
            if (j < 0 || j >= this.items.length) return;
            [this.items[i], this.items[j]] = [this.items[j], this.items[i]];
        }
    }">
        @csrf
        <ol class="space-y-2 mb-4">
            <template x-for="(item, i) in items" :key="item.id">
                <li class="flex items-center gap-2 border border-neutral-200 bg-white px-3 py-2 text-sm">
                    <span x-text="item.name"></span>
                    <input type="hidden" name="order[]" :value="item.id">
                    <span class="ml-auto text-xs" :class="item.on ? 'text-green-700' : 'text-neutral-400'" x-text="item.on ? 'on' : 'off'"></span>
                    <button type="button" @click="move(i, -1)" class="px-2 py-0.5 border border-neutral-300 text-xs" :disabled="i === 0">↑</button>
                    <button type="button" @click="move(i, 1)" class="px-2 py-0.5 border border-neutral-300 text-xs" :disabled="i === items.length - 1">↓</button>
                </li>
            </template>
        </ol>
        <button class="btn-outline !py-2 !px-4 text-xs">Save order</button>
    </form>
</div>
@endsection

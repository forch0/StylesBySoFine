@extends('layouts.admin')

@section('title', $product->exists ? 'Edit: '.$product->name : 'New product')

@section('content')
@php
    $variantRows = old('variants', $product->variants->map(fn ($v) => [
        'id' => $v->id,
        'options' => $v->options,
        'sku' => $v->sku,
        'price' => $v->price !== null ? $v->price / 100 : '',
        'stock_quantity' => $v->stock_quantity,
        'low_stock_threshold' => $v->low_stock_threshold,
        'is_active' => $v->is_active,
    ])->values());
    if (empty($variantRows) || count($variantRows) === 0) {
        $variantRows = [['id' => '', 'options' => [], 'sku' => '', 'price' => '', 'stock_quantity' => 0, 'low_stock_threshold' => '', 'is_active' => true]];
    }
@endphp

<form action="{{ $product->exists ? route('admin.products.update', $product) : route('admin.products.store') }}" method="POST" enctype="multipart/form-data"
      x-data="{
          options: {{ Js::from(old('option_defs', $product->exists ? $product->options->map(fn($o) => ['name' => $o->name, 'values' => $o->values->pluck('value')->implode(',')])->values() : [['name' => 'Size', 'values' => 'XS,S,M,L,XL'], ['name' => 'Colour', 'values' => '']])) }},
          variants: {{ Js::from($variantRows) }},
          addOption() { this.options.push({ name: '', values: '' }) },
          removeOption(i) { this.options.splice(i, 1) },
          generate() {
              const opts = this.options.filter(o => o.name && o.values);
              if (!opts.length) { if (!this.variants.length) this.variants.push(this.blankVariant({})); return; }
              let combos = [{}];
              for (const o of opts) {
                  const vals = o.values.split(',').map(v => v.trim()).filter(Boolean);
                  const next = [];
                  for (const c of combos) for (const v of vals) next.push({ ...c, [o.name]: v });
                  combos = next;
              }
              const existing = this.variants;
              this.variants = combos.map(c => {
                  const match = existing.find(e => JSON.stringify(e.options || {}) === JSON.stringify(c));
                  return match || this.blankVariant(c);
              });
          },
          blankVariant(options) {
              return { id: '', options, sku: '', price: '', stock_quantity: 0, low_stock_threshold: '', is_active: true };
          },
          addVariant() { this.variants.push(this.blankVariant({})) },
          removeVariant(i) { this.variants.splice(i, 1) },
          newMedia: [],
          addMedia() { this.newMedia.push({ type: 'image', source: 'upload' }) },
          removeMedia(i) { this.newMedia.splice(i, 1) },
      }">
    @csrf
    @if($product->exists) @method('PUT') @endif

    <div class="grid lg:grid-cols-[1fr_320px] gap-6 items-start">
        <div class="space-y-6">
            {{-- Basics --}}
            <div class="card p-5 space-y-4">
                <div>
                    <label class="label">Name *</label>
                    <input name="name" value="{{ old('name', $product->name) }}" class="input" required>
                </div>
                <div>
                    <label class="label">Short description</label>
                    <input name="short_description" value="{{ old('short_description', $product->short_description) }}" class="input" maxlength="500">
                </div>
                <div>
                    <label class="label">Full description</label>
                    <textarea name="description" rows="6" class="input">{{ old('description', $product->description) }}</textarea>
                </div>
                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="label">Material</label>
                        <input name="material" value="{{ old('material', $product->material) }}" class="input">
                    </div>
                    <div>
                        <label class="label">Care instructions</label>
                        <input name="care_instructions" value="{{ old('care_instructions', $product->care_instructions) }}" class="input">
                    </div>
                </div>
            </div>

            {{-- Media --}}
            <div class="card p-5">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="font-display text-lg">Photos &amp; video</h2>
                    <button type="button" @click="addMedia()" class="btn-outline !py-1.5 !px-3 text-xs">+ Add media</button>
                </div>

                @foreach($product->media as $i => $media)
                    <div class="flex items-center gap-3 border border-neutral-200 p-3 mb-2">
                        <input type="hidden" name="media[{{ $i }}][id]" value="{{ $media->id }}">
                        <input type="hidden" name="media[{{ $i }}][type]" value="{{ $media->type }}">
                        <input type="hidden" name="media[{{ $i }}][source]" value="{{ $media->source }}">
                        <input type="hidden" name="media[{{ $i }}][url]" value="{{ $media->source === 'url' ? $media->path : '' }}">
                        <div class="w-14 h-16 bg-neutral-100 flex-shrink-0 flex items-center justify-center overflow-hidden">
                            @if($media->isVideo())
                                <svg class="w-6 h-6 text-neutral-400" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                            @else
                                <img src="{{ $media->url() }}" class="w-full h-full object-cover" alt="">
                            @endif
                        </div>
                        <div class="flex-1 text-xs text-neutral-500 truncate">
                            <span class="uppercase font-semibold text-ink">{{ $media->type }}</span> — {{ Str::limit($media->path, 50) }}
                        </div>
                        <label class="text-xs flex items-center gap-1.5"><input type="radio" name="primary_media" value="{{ $i }}" onclick="document.querySelectorAll('[data-primary]').forEach(el => el.value=''); document.querySelector('[name=\'media[{{ $i }}][is_primary]\']').value='1'" {{ $media->is_primary ? 'checked' : '' }}> Primary</label>
                        <input type="hidden" data-primary name="media[{{ $i }}][is_primary]" value="{{ $media->is_primary ? 1 : '' }}">
                        <label class="text-xs flex items-center gap-1.5 text-red-700"><input type="checkbox" name="media[{{ $i }}][delete]" value="1"> Delete</label>
                    </div>
                @endforeach

                <template x-for="(m, i) in newMedia" :key="'new-' + i">
                    <div class="flex flex-wrap items-center gap-3 border border-dashed border-neutral-300 p-3 mb-2">
                        <select :name="'media[new_' + i + '][type]'" x-model="m.type" class="input !py-1.5 !w-28 text-xs">
                            <option value="image">Image</option>
                            <option value="video">Video</option>
                        </select>
                        <select :name="'media[new_' + i + '][source]'" x-model="m.source" class="input !py-1.5 !w-28 text-xs">
                            <option value="upload">Upload</option>
                            <option value="url">URL</option>
                        </select>
                        <template x-if="m.source === 'upload'">
                            <input type="file" :name="'media[new_' + i + '][file]'" :accept="m.type === 'video' ? 'video/mp4,video/webm' : 'image/*'" class="text-xs">
                        </template>
                        <template x-if="m.source === 'url'">
                            <input :name="'media[new_' + i + '][url]'" placeholder="https://… (mp4, YouTube, Vimeo)" class="input !py-1.5 flex-1 text-xs">
                        </template>
                        <button type="button" @click="removeMedia(i)" class="text-xs text-red-700 underline">Remove</button>
                    </div>
                </template>
                <p class="text-xs text-neutral-500 mt-2">Images: JPG/PNG/WebP. Video: MP4/WebM upload or paste a YouTube/Vimeo/direct link.</p>
            </div>

            {{-- Options + variants --}}
            <div class="card p-5">
                <h2 class="font-display text-lg mb-1">Options &amp; variants</h2>
                <p class="text-xs text-neutral-500 mb-4">Define option sets (e.g. Size, Colour), generate the variant grid, then set stock per variant.</p>

                <div class="space-y-2 mb-4">
                    <template x-for="(o, i) in options" :key="'opt-' + i">
                        <div class="flex gap-2">
                            <input x-model="o.name" placeholder="Option name (e.g. Size)" class="input !py-2 !w-44 text-sm">
                            <input x-model="o.values" placeholder="Values, comma separated (XS,S,M)" class="input !py-2 flex-1 text-sm">
                            <button type="button" @click="removeOption(i)" class="text-xs text-red-700 underline">Remove</button>
                        </div>
                    </template>
                </div>
                <div class="flex gap-3 mb-5">
                    <button type="button" @click="addOption()" class="btn-outline !py-1.5 !px-3 text-xs">+ Add option</button>
                    <button type="button" @click="generate()" class="btn-primary !py-1.5 !px-3 text-xs">Generate variants</button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead><tr class="text-left text-xs uppercase tracking-wider text-neutral-500 border-b border-neutral-200">
                            <th class="pb-2 pr-2">Variant</th><th class="pb-2 pr-2">SKU</th><th class="pb-2 pr-2">Price ₦ (opt)</th><th class="pb-2 pr-2">Stock</th><th class="pb-2 pr-2">Low alert</th><th class="pb-2 pr-2">Active</th><th></th>
                        </tr></thead>
                        <tbody class="divide-y divide-neutral-100">
                            <template x-for="(v, i) in variants" :key="'var-' + i">
                                <tr>
                                    <td class="py-2 pr-2 min-w-[140px]">
                                        <input type="hidden" :name="'variants[' + i + '][id]'" :value="v.id">
                                        <template x-for="(val, key) in v.options" :key="key">
                                            <input type="hidden" :name="'variants[' + i + '][options][' + key + ']'" :value="val">
                                        </template>
                                        <span class="text-xs" x-text="Object.keys(v.options).length ? Object.entries(v.options).map(([k,val]) => k + ': ' + val).join(' / ') : 'Default'"></span>
                                    </td>
                                    <td class="py-2 pr-2"><input :name="'variants[' + i + '][sku]'" x-model="v.sku" class="input !py-1 !w-24 text-xs"></td>
                                    <td class="py-2 pr-2"><input :name="'variants[' + i + '][price]'" x-model="v.price" type="number" step="0.01" min="0" class="input !py-1 !w-24 text-xs" placeholder="Base"></td>
                                    <td class="py-2 pr-2"><input :name="'variants[' + i + '][stock_quantity]'" x-model="v.stock_quantity" type="number" min="0" class="input !py-1 !w-20 text-xs" required></td>
                                    <td class="py-2 pr-2"><input :name="'variants[' + i + '][low_stock_threshold]'" x-model="v.low_stock_threshold" type="number" min="0" class="input !py-1 !w-16 text-xs" placeholder="3"></td>
                                    <td class="py-2 pr-2"><input type="checkbox" :name="'variants[' + i + '][is_active]'" value="1" x-model="v.is_active" class="accent-wine-700 w-4 h-4"></td>
                                    <td class="py-2"><button type="button" @click="removeVariant(i)" class="text-xs text-red-700 underline">✕</button></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
                <button type="button" @click="addVariant()" class="btn-outline !py-1.5 !px-3 text-xs mt-3">+ Add single variant</button>
            </div>

            {{-- SEO --}}
            <div class="card p-5 space-y-4">
                <h2 class="font-display text-lg">SEO</h2>
                <div><label class="label">Meta title</label><input name="meta_title" value="{{ old('meta_title', $product->meta_title) }}" class="input"></div>
                <div><label class="label">Meta description</label><input name="meta_description" value="{{ old('meta_description', $product->meta_description) }}" class="input"></div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6 sticky top-20">
            <div class="card p-5 space-y-4">
                <div>
                    <label class="label">Status</label>
                    <select name="status" class="input">
                        @foreach(['draft' => 'Draft', 'active' => 'Active', 'archived' => 'Archived'] as $v => $l)
                            <option value="{{ $v }}" {{ old('status', $product->status ?? 'draft') === $v ? 'selected' : '' }}>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="label">Category</label>
                    <select name="category_id" class="input">
                        <option value="">—</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->parent_id ? '— ' : '' }}{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="label">Price (₦) *</label>
                    <input name="price" type="number" step="0.01" min="0" value="{{ old('price', $product->exists ? $product->price / 100 : '') }}" class="input" required>
                </div>
                <div>
                    <label class="label">Compare-at price (₦)</label>
                    <input name="compare_at_price" type="number" step="0.01" min="0" value="{{ old('compare_at_price', $product->compare_at_price ? $product->compare_at_price / 100 : '') }}" class="input">
                </div>
                <div>
                    <label class="label">SKU</label>
                    <input name="sku" value="{{ old('sku', $product->sku) }}" class="input">
                </div>
                <div class="space-y-2 pt-2">
                    @foreach(['is_featured' => 'Featured', 'is_bestseller' => 'Bestseller', 'is_new_arrival' => 'New arrival'] as $f => $l)
                        <label class="flex items-center gap-2 text-sm">
                            <input type="checkbox" name="{{ $f }}" value="1" {{ old($f, $product->$f) ? 'checked' : '' }} class="accent-wine-700 w-4 h-4"> {{ $l }}
                        </label>
                    @endforeach
                </div>
                <div class="border-t border-neutral-100 pt-4">
                    <label class="flex items-center gap-2 text-sm">
                        <input type="checkbox" name="allow_preorder" value="1" {{ old('allow_preorder', $product->allow_preorder) ? 'checked' : '' }} class="accent-wine-700 w-4 h-4">
                        <span>Allow pre-orders <span class="text-neutral-400 text-xs">(sell when out of stock)</span></span>
                    </label>
                    <input name="preorder_note" value="{{ old('preorder_note', $product->preorder_note) }}"
                           placeholder="e.g. Ships in 2 weeks" class="input !py-2 mt-2.5 text-sm">
                    <p class="text-[11px] text-neutral-400 mt-1">Shown to customers on pre-order items.</p>
                </div>
            </div>

            <div class="card p-5">
                <label class="label">Collections</label>
                <div class="space-y-2">
                    @foreach($collections as $collection)
                        <label class="flex items-center gap-2 text-sm">
                            <input type="checkbox" name="collections[]" value="{{ $collection->id }}"
                                   {{ in_array($collection->id, old('collections', $product->exists ? $product->collections->pluck('id')->all() : [])) ? 'checked' : '' }}
                                   class="accent-wine-700 w-4 h-4"> {{ $collection->name }}
                        </label>
                    @endforeach
                </div>
            </div>

            <button class="btn-primary w-full">{{ $product->exists ? 'Save product' : 'Create product' }}</button>
        </div>
    </div>
</form>

@if($product->exists)
    <form action="{{ route('admin.products.destroy', $product) }}" method="POST" onsubmit="return confirm('Delete this product? This cannot be undone.')" class="mt-4 max-w-xs mx-auto lg:mx-0">
        @csrf @method('DELETE')
        <button class="w-full text-sm text-red-700 underline">Delete product</button>
    </form>
@endif
@endsection

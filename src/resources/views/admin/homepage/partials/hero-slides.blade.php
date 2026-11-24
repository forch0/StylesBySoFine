@php
    $slides = $section->get('slides');
    if (! is_array($slides) || empty($slides)) {
        $slides = [[
            'eyebrow' => $section->get('eyebrow'),
            'headline' => $section->get('headline'),
            'subcopy' => $section->get('subcopy'),
            'cta_text' => $section->get('cta_text'),
            'cta_url' => $section->get('cta_url'),
            'cta2_text' => $section->get('cta2_text'),
            'cta2_url' => $section->get('cta2_url'),
            'image' => $section->get('image'),
        ]];
    }
@endphp

<p class="text-xs text-ink-soft mb-3">Hero slides — each slide has its own image, headline, text and buttons. Slides auto-rotate on the storefront.</p>

<div x-data="{
        slides: {{ Js::from(array_values($slides)) }},
        add() { this.slides.push({ eyebrow: '', headline: '', subcopy: '', cta_text: '', cta_url: '', cta2_text: '', cta2_url: '', image: '' }) },
        remove(i) { this.slides.splice(i, 1) },
        move(i, dir) { const j = i + dir; if (j < 0 || j >= this.slides.length) return; [this.slides[i], this.slides[j]] = [this.slides[j], this.slides[i]] }
     }">

    <template x-for="(slide, i) in slides" :key="i">
        <div class="border border-ink/10 rounded-lg p-4 mb-4 bg-neutral-50">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-ink-soft">Slide <span x-text="i + 1"></span></span>
                <div class="flex gap-1">
                    <button type="button" @click="move(i, -1)" class="text-xs border rounded px-2 py-1 hover:border-wine-700" title="Move up">↑</button>
                    <button type="button" @click="move(i, 1)" class="text-xs border rounded px-2 py-1 hover:border-wine-700" title="Move down">↓</button>
                    <button type="button" @click="remove(i)" x-show="slides.length > 1" class="text-xs border rounded px-2 py-1 text-red-600 hover:border-red-500" title="Delete slide">✕</button>
                </div>
            </div>

            <div class="grid sm:grid-cols-2 gap-3">
                <div class="sm:col-span-2">
                    <label class="label">Background image</label>
                    <div class="flex flex-col sm:flex-row gap-3">
                        <input :name="'content[slides]['+i+'][image]'" x-model="slide.image" class="input !py-2 flex-1" placeholder="https://... image URL, or upload →">
                        <input type="file" :name="'slide_images['+i+']'" accept="image/*" class="text-xs file:mr-3 file:border-0 file:bg-wine-700 file:text-cream file:px-3 file:py-2 file:rounded file:text-xs file:font-semibold">
                    </div>
                    <template x-if="slide.image">
                        <img :src="slide.image.startsWith('http') ? slide.image : '/storage/' + slide.image" class="mt-2 h-20 w-32 object-cover rounded border border-ink/10">
                    </template>
                </div>
                <div>
                    <label class="label">Eyebrow (small label)</label>
                    <input :name="'content[slides]['+i+'][eyebrow]'" x-model="slide.eyebrow" class="input !py-2">
                </div>
                <div>
                    <label class="label">Headline</label>
                    <input :name="'content[slides]['+i+'][headline]'" x-model="slide.headline" class="input !py-2">
                </div>
                <div class="sm:col-span-2">
                    <label class="label">Supporting text</label>
                    <textarea :name="'content[slides]['+i+'][subcopy]'" x-model="slide.subcopy" rows="2" class="input !py-2"></textarea>
                </div>
                <div>
                    <label class="label">Button 1 text</label>
                    <input :name="'content[slides]['+i+'][cta_text]'" x-model="slide.cta_text" class="input !py-2">
                </div>
                <div>
                    <label class="label">Button 1 link</label>
                    <input :name="'content[slides]['+i+'][cta_url]'" x-model="slide.cta_url" class="input !py-2" placeholder="/shop">
                </div>
                <div>
                    <label class="label">Button 2 text (optional)</label>
                    <input :name="'content[slides]['+i+'][cta2_text]'" x-model="slide.cta2_text" class="input !py-2">
                </div>
                <div>
                    <label class="label">Button 2 link</label>
                    <input :name="'content[slides]['+i+'][cta2_url]'" x-model="slide.cta2_url" class="input !py-2">
                </div>
            </div>
        </div>
    </template>

    <button type="button" @click="add()" class="text-sm font-semibold text-wine-700 hover:underline">+ Add slide</button>
</div>

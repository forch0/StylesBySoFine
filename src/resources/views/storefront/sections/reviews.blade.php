@if($reviews->isNotEmpty())
<section class="section-pad py-14 sm:py-20 bg-white border-y border-neutral-200">
    <p class="eyebrow mb-2 text-center">{{ $section->get('eyebrow', 'Kind words') }}</p>
    <h2 class="heading-display text-center mb-10">{{ $section->get('heading', 'What customers say') }}</h2>
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
        @foreach($reviews->take(3) as $review)
            <figure class="border border-neutral-200 p-6 flex flex-col">
                <div class="text-wine-700 mb-3 tracking-widest">
                    @for($i = 0; $i < $review->rating; $i++) ★ @endfor
                </div>
                <blockquote class="text-sm text-neutral-600 leading-relaxed flex-1">{{ $review->body }}</blockquote>
                <figcaption class="mt-4 text-xs uppercase tracking-wider text-neutral-500">{{ $review->customer_name }}</figcaption>
            </figure>
        @endforeach
    </div>
</section>
@endif

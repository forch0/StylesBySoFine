<section class="section-pad py-14 sm:py-20">
    <div class="max-w-xl mx-auto text-center">
        <p class="eyebrow mb-2">{{ $section->get('eyebrow', 'Join the list') }}</p>
        <h2 class="heading-display mb-4">{{ $section->get('heading', 'First to know') }}</h2>
        <p class="text-neutral-600 mb-7">{{ $section->get('text', 'New drops, restocks and private offers — straight to your inbox.') }}</p>
        <form action="{{ route('newsletter') }}" method="POST" class="flex max-w-md mx-auto">
            @csrf
            <input type="email" name="email" required placeholder="Your email address" class="input rounded-none border-r-0">
            <button class="btn-primary whitespace-nowrap">Subscribe</button>
        </form>
    </div>
</section>

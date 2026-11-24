@extends('layouts.admin')

@section('title', 'Settings')

@section('content')
<form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data" class="max-w-3xl space-y-6">
    @csrf @method('PUT')

    <div class="card p-5 space-y-4">
        <h2 class="font-display text-lg">Store</h2>
        <div class="grid sm:grid-cols-2 gap-4">
            <div><label class="label">Store name</label><input name="store_name" value="{{ $values['store_name'] }}" class="input" required></div>
            <div><label class="label">Tagline</label><input name="tagline" value="{{ $values['tagline'] }}" class="input"></div>
        </div>
        <div><label class="label">Announcement bar</label><input name="announcement_bar" value="{{ $values['announcement_bar'] }}" class="input" placeholder="e.g. Free delivery on orders over ₦100,000"></div>
        <div><label class="label">Logo</label><input name="logo" type="file" accept="image/*" class="input !py-2">
            @if($values['logo'])<img src="{{ Storage::url($values['logo']) }}" class="h-10 mt-2" alt="Logo">@endif</div>
        <div><label class="label">Footer about text</label><textarea name="footer_about" rows="2" class="input">{{ $values['footer_about'] }}</textarea></div>
    </div>

    <div class="card p-5 space-y-4">
        <h2 class="font-display text-lg">Contact details</h2>
        <div class="grid sm:grid-cols-2 gap-4">
            <div><label class="label">Email</label><input name="contact_email" type="email" value="{{ $values['contact_email'] }}" class="input"></div>
            <div><label class="label">WhatsApp number</label><input name="whatsapp_number" value="{{ $values['whatsapp_number'] }}" class="input" placeholder="+234…"></div>
            <div><label class="label">Instagram URL</label><input name="instagram_url" value="{{ $values['instagram_url'] }}" class="input"></div>
            <div><label class="label">Business hours</label><input name="business_hours" value="{{ $values['business_hours'] }}" class="input"></div>
            <div><label class="label">Response time</label><input name="response_time" value="{{ $values['response_time'] }}" class="input" placeholder="e.g. Within 24 hours"></div>
            <div><label class="label">Address / service area</label><input name="business_address" value="{{ $values['business_address'] }}" class="input"></div>
        </div>
    </div>

    <div class="card p-5 space-y-4">
        <h2 class="font-display text-lg">Delivery</h2>
        <div><label class="label">Free delivery threshold (₦)</label><input name="free_delivery_threshold" type="number" step="0.01" value="{{ $values['free_delivery_threshold'] !== '' && $values['free_delivery_threshold'] !== null ? $values['free_delivery_threshold'] / 100 : '' }}" class="input !w-56"></div>
    </div>

    <div class="card p-5 space-y-4">
        <h2 class="font-display text-lg">SEO</h2>
        <div><label class="label">Site title</label><input name="seo_title" value="{{ $values['seo_title'] }}" class="input"></div>
        <div><label class="label">Site description</label><input name="seo_description" value="{{ $values['seo_description'] }}" class="input"></div>
    </div>

    <div class="card p-5 space-y-4">
        <h2 class="font-display text-lg">Payments</h2>
        <div>
            <label class="label">Paystack public key</label>
            <input name="paystack_public_key" value="{{ $values['paystack_public_key'] }}" class="input" placeholder="pk_live_… or pk_test_…">
        </div>
        <div>
            <label class="label">Paystack secret key</label>
            @if($values['paystack_secret_masked'])
                <div class="flex items-center gap-2 mb-2">
                    <code class="text-xs bg-neutral-100 border border-neutral-200 rounded-md px-2.5 py-1.5 font-mono">{{ $values['paystack_secret_masked'] }}</code>
                    <span class="text-[11px] font-semibold text-green-700 bg-green-50 border border-green-200 rounded-full px-2 py-0.5">Saved</span>
                </div>
            @endif
            <input name="paystack_secret_key" type="password" autocomplete="new-password" class="input"
                   placeholder="{{ $values['paystack_secret_masked'] ? 'Paste a new key to replace' : 'sk_live_… or sk_test_…' }}">
            <p class="text-xs text-neutral-500 mt-1.5">Stored in the database, masked after saving — it's never shown again. Leave blank to keep the current key.</p>
        </div>
    </div>

    <button class="btn-primary">Save settings</button>
</form>

@can('admin')
<div class="card p-5 mt-6 max-w-3xl border-wine-200">
    <h2 class="font-display text-lg">Demo data</h2>
    <p class="text-sm text-neutral-500 mt-1">Fill the shop with sample products to preview the design, or clear them out when you're ready to add real stock.</p>
    <p class="text-xs text-neutral-500 mt-2"><span class="font-semibold text-ink">Always kept:</span> categories, UK size system, pages, navigation, delivery zones, coupons, homepage designs, settings and staff.</p>

    <div class="flex flex-wrap items-center gap-3 mt-4">
        <form action="{{ route('admin.demo.seed') }}" method="POST">
            @csrf
            <button class="btn-primary !py-2.5 !px-5 text-xs">Seed demo products</button>
        </form>
        <form action="{{ route('admin.demo.wipe') }}" method="POST" onsubmit="return confirm('Remove the demo products? Your real products and standard content are kept.')">
            @csrf
            <button class="rounded-full border border-neutral-300 px-5 py-2.5 text-xs font-semibold uppercase tracking-wider hover:border-red-400 hover:text-red-700 transition-colors">Wipe demo products</button>
        </form>
    </div>

    <form action="{{ route('admin.demo.wipe') }}" method="POST" class="mt-3 flex items-center gap-2"
          onsubmit="return confirm('This deletes EVERY order in the system. Are you absolutely sure?')">
        @csrf
        <input type="hidden" name="also_orders" value="1">
        <button class="text-xs text-red-700 underline underline-offset-2 hover:text-red-800">Also clear all orders (test sales)</button>
    </form>
</div>
@endcan
@endsection

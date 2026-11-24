@extends('layouts.admin')

@section('title', 'POS — Sell in person')

@section('content')
<div x-data="pos()" x-init="load()">
    <div class="grid lg:grid-cols-[1fr_400px] gap-6 items-start">
        {{-- Product picker --}}
        <div class="card p-4">
            <div class="relative mb-4">
                <svg class="w-5 h-5 absolute left-4 top-1/2 -translate-y-1/2 text-neutral-400 pointer-events-none" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/></svg>
                <input x-model="q" @input.debounce.300ms="search()" type="search" placeholder="Search products or SKU…"
                       class="input flex-1 !pl-11" autofocus>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 max-h-[70vh] overflow-y-auto">
                <template x-for="product in results" :key="product.id">
                    <button @click="pick(product)" class="text-left border border-neutral-200 hover:border-wine-700 bg-white group">
                        <div class="aspect-square bg-neutral-100 overflow-hidden">
                            <template x-if="product.image">
                                <img :src="product.image" class="w-full h-full object-cover" alt="">
                            </template>
                            <template x-if="!product.image">
                                <div class="w-full h-full flex items-center justify-center text-neutral-300">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z"/></svg>
                                </div>
                            </template>
                        </div>
                        <div class="p-2.5">
                            <p class="text-xs font-medium truncate" x-text="product.name"></p>
                            <p class="text-xs text-neutral-500" x-text="product.price_display"></p>
                        </div>
                    </button>
                </template>
            </div>
            <p x-show="!loading && results.length === 0" class="text-sm text-neutral-500 py-8 text-center" x-cloak>No products found.</p>
        </div>

        {{-- Sale panel --}}
        <div class="card p-4 sticky top-20 flex flex-col max-h-[calc(100vh-6rem)]">
            <div class="flex items-center justify-between mb-3">
                <h2 class="font-display text-lg flex items-center gap-2">
                    <svg class="w-5 h-5 text-wine-700" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z"/></svg>
                    Current sale
                </h2>
                <button @click="reset()" class="text-xs text-neutral-500 underline">Clear</button>
            </div>

            <div class="flex-1 overflow-y-auto space-y-2 mb-4">
                <template x-for="(line, i) in lines" :key="line.variant_id">
                    <div class="flex items-center gap-2 border border-neutral-200 p-2">
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-medium truncate" x-text="line.name"></p>
                            <p class="text-[11px] text-neutral-500" x-text="line.summary"></p>
                            <p class="text-xs font-semibold mt-0.5" x-text="line.price_display"></p>
                        </div>
                        <div class="flex items-center border border-neutral-300">
                            <button @click="line.qty = Math.max(1, line.qty - 1)" class="px-2 py-1">−</button>
                            <span class="w-8 text-center text-sm" x-text="line.qty"></span>
                            <button @click="line.qty = Math.min(line.stock, line.qty + 1)" class="px-2 py-1">+</button>
                        </div>
                        <button @click="lines.splice(i, 1)" class="text-neutral-400 hover:text-red-700 px-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" d="M6 18 18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                </template>
                <p x-show="lines.length === 0" class="text-sm text-neutral-400 text-center py-10" x-cloak>Tap a product to add it.</p>
            </div>

            {{-- Customer (optional) --}}
            <details class="mb-3">
                <summary class="text-xs uppercase tracking-wider text-neutral-500 cursor-pointer flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"/></svg>
                    Customer <span class="normal-case">(optional)</span>
                </summary>
                <div class="grid grid-cols-2 gap-2 mt-2">
                    <input x-model="customer.name" class="input !py-2 text-sm col-span-2" placeholder="Name">
                    <input x-model="customer.phone" class="input !py-2 text-sm" placeholder="Phone">
                    <input x-model="customer.email" class="input !py-2 text-sm" placeholder="Email">
                </div>
            </details>

            <div class="mb-3">
                <label class="label flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6Z"/></svg>
                    Discount (₦)
                </label>
                <input x-model.number="discount" type="number" min="0" class="input !py-2 text-sm" placeholder="0">
            </div>

            <div class="border-t border-neutral-200 pt-3 space-y-1 text-sm">
                <div class="flex justify-between"><span>Subtotal</span><span x-text="fmt(subtotal)"></span></div>
                <div class="flex justify-between" x-show="discount > 0"><span>Discount</span><span x-text="'−' + fmt(discountKobo)"></span></div>
                <div class="flex justify-between font-semibold text-lg"><span>Total</span><span x-text="fmt(total)"></span></div>
            </div>

            {{-- Payment methods --}}
            <div class="grid grid-cols-2 gap-2 mt-4">
                <template x-for="m in methods" :key="m.key">
                    <button @click="checkout(m.key)" :disabled="lines.length === 0 || busy"
                            class="btn !rounded-xl text-sm py-3 border justify-start !px-4"
                            :class="busy ? 'opacity-50 border-wine-700 bg-wine-700 text-cream' : 'border-wine-700 bg-wine-700 text-cream hover:bg-wine-800'">
                        <span x-html="m.icon" class="shrink-0"></span>
                        <span x-text="m.label"></span>
                    </button>
                </template>
            </div>
            <p x-show="error" class="text-red-700 text-xs mt-2" x-text="error" x-cloak></p>
        </div>
    </div>

    {{-- Success modal --}}
    <div x-show="receipt" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-ink/60" @click="receipt = null"></div>
        <div class="relative bg-white p-8 max-w-sm w-full text-center">
            <div class="w-14 h-14 mx-auto mb-4 rounded-full bg-wine-700 flex items-center justify-center">
                <svg class="w-7 h-7 text-cream" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
            </div>
            <p class="font-display text-2xl mb-1">Sale complete</p>
            <p class="text-sm text-neutral-500 mb-6" x-text="receipt?.order_number"></p>
            <div class="flex gap-3">
                <a :href="receipt?.receipt_url" target="_blank" class="btn-outline flex-1 text-sm">Receipt</a>
                <button @click="receipt = null" class="btn-primary flex-1 text-sm">New sale</button>
            </div>
        </div>
    </div>
</div>

<script>
function pos() {
    return {
        q: '', results: [], lines: [], busy: false, loading: true,
        customer: { name: '', phone: '', email: '' },
        discount: 0, error: '', receipt: null,
        methods: [
            { key: 'cash', label: 'Cash', icon: '<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z"/></svg>' },
            { key: 'transfer', label: 'Transfer', icon: '<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21 3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5"/></svg>' },
            { key: 'pos_terminal', label: 'POS Terminal', icon: '<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z"/></svg>' },
            { key: 'other', label: 'Other', icon: '<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a2.25 2.25 0 0 0-2.25-2.25H15a3 3 0 1 1-6 0H5.25A2.25 2.25 0 0 0 3 12m18 0v6a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 18v-6m18 0V9M3 12V9m18 0a2.25 2.25 0 0 0-2.25-2.25H5.25A2.25 2.25 0 0 0 3 9m18 0V6a2.25 2.25 0 0 0-2.25-2.25H5.25A2.25 2.25 0 0 0 3 6v3"/></svg>' },
        ],
        get subtotal() { return this.lines.reduce((s, l) => s + l.price * l.qty, 0) },
        get discountKobo() { return Math.min(Math.round((this.discount || 0) * 100), this.subtotal) },
        get total() { return this.subtotal - this.discountKobo },
        fmt(kobo) { return '₦' + (kobo / 100).toLocaleString('en-NG', { minimumFractionDigits: 2 }) },
        async load() { this.loading = true; await this.search(); this.loading = false },
        async search() {
            const res = await fetch('{{ route('admin.pos.search') }}?q=' + encodeURIComponent(this.q));
            this.results = await res.json();
        },
        pick(product) {
            if (product.variants.length === 1) {
                this.addLine(product, product.variants[0]);
                return;
            }
            const summary = prompt('Choose option:\n' + product.variants.map((v, i) => `${i + 1}. ${v.summary} — ${v.price_display} (${v.stock} left)`).join('\n') + '\n\nEnter number:');
            const idx = parseInt(summary) - 1;
            if (product.variants[idx]) this.addLine(product, product.variants[idx]);
        },
        addLine(product, variant) {
            if (variant.stock < 1) { this.error = `${product.name} (${variant.summary}) is out of stock.`; return }
            const existing = this.lines.find(l => l.variant_id === variant.id);
            if (existing) { existing.qty = Math.min(existing.qty + 1, variant.stock); return }
            this.lines.push({ variant_id: variant.id, name: product.name, summary: variant.summary, price: variant.price, price_display: variant.price_display, qty: 1, stock: variant.stock });
            this.error = '';
        },
        async checkout(method) {
            this.busy = true; this.error = '';
            try {
                const res = await fetch('{{ route('admin.pos.store') }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                    body: JSON.stringify({
                        items: this.lines.map(l => ({ variant_id: l.variant_id, quantity: l.qty })),
                        payment_method: method,
                        customer_name: this.customer.name, customer_phone: this.customer.phone, customer_email: this.customer.email,
                        discount_naira: this.discount, notes: '',
                    }),
                });
                const data = await res.json();
                if (!res.ok) throw new Error(data.error || data.message || 'Could not complete sale.');
                this.receipt = data;
                this.reset();
            } catch (e) { this.error = e.message }
            this.busy = false;
        },
        reset() { this.lines = []; this.discount = 0; this.customer = { name: '', phone: '', email: '' } },
    }
}
</script>

<div class="mt-8 card p-5">
    <div class="flex justify-between items-center mb-3">
        <h2 class="font-display text-lg flex items-center gap-2">
            <svg class="w-5 h-5 text-wine-700" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
            Recent in-person sales
        </h2>
        <p class="text-sm text-neutral-500">Today: <span class="font-semibold text-ink">{{ money($todaySales) }}</span></p>
    </div>
    @if($recentSales->isEmpty())
        <p class="text-sm text-neutral-500">No POS sales yet.</p>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="text-left text-xs uppercase tracking-wider text-neutral-500 border-b border-neutral-200">
                    <th class="pb-2">Order</th><th class="pb-2">Customer</th><th class="pb-2">Method</th><th class="pb-2">Total</th><th class="pb-2">When</th><th class="pb-2"></th>
                </tr></thead>
                <tbody class="divide-y divide-neutral-100">
                    @foreach($recentSales as $sale)
                        <tr>
                            <td class="py-2.5">{{ $sale->order_number }}</td>
                            <td class="py-2.5">{{ $sale->customerDisplayName() }}</td>
                            <td class="py-2.5 capitalize">{{ str_replace('_', ' ', $sale->payment_method) }}</td>
                            <td class="py-2.5 font-medium">{{ money($sale->total) }}</td>
                            <td class="py-2.5 text-neutral-500">{{ $sale->created_at->diffForHumans() }}</td>
                            <td class="py-2.5"><a href="{{ route('admin.pos.receipt', $sale) }}" target="_blank" class="inline-flex items-center gap-1 underline">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/></svg>
                                Receipt</a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection

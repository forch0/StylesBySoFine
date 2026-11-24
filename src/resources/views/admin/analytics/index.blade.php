@extends('layouts.admin')

@section('title', 'Analytics')

@section('content')
<div class="max-w-6xl">
    {{-- Range filter --}}
    <div class="flex items-center gap-2 mb-6">
        @foreach(['7' => '7 days', '30' => '30 days', '90' => '90 days', 'all' => 'All time'] as $r => $label)
            <a href="{{ route('admin.analytics.index', ['range' => $r]) }}"
               class="rounded-full px-4 py-2 text-xs font-semibold uppercase tracking-wider transition-colors
                      {{ $range === $r ? 'bg-ink text-cream' : 'bg-white border border-neutral-200 hover:border-ink' }}">{{ $label }}</a>
        @endforeach
    </div>

    {{-- KPIs --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
        <div class="card p-4"><p class="text-[11px] uppercase tracking-wider text-neutral-500">Revenue</p><p class="font-display text-2xl mt-1">{{ money($kpis['revenue']) }}</p></div>
        <div class="card p-4"><p class="text-[11px] uppercase tracking-wider text-neutral-500">Orders</p><p class="font-display text-2xl mt-1">{{ $kpis['orders'] }}</p></div>
        <div class="card p-4"><p class="text-[11px] uppercase tracking-wider text-neutral-500">Avg order</p><p class="font-display text-2xl mt-1">{{ money($kpis['aov']) }}</p></div>
        <div class="card p-4"><p class="text-[11px] uppercase tracking-wider text-neutral-500">Items sold</p><p class="font-display text-2xl mt-1">{{ $kpis['items'] }}</p></div>
        <div class="card p-4"><p class="text-[11px] uppercase tracking-wider text-neutral-500">Customers</p><p class="font-display text-2xl mt-1">{{ $kpis['customers'] }}</p></div>
        <div class="card p-4"><p class="text-[11px] uppercase tracking-wider text-neutral-500">Pre-orders</p><p class="font-display text-2xl mt-1">{{ $kpis['preorders'] }}</p></div>
    </div>

    <div class="grid lg:grid-cols-3 gap-6 mb-6">
        {{-- Revenue trend --}}
        <div class="card p-5 lg:col-span-2">
            <h2 class="font-display text-lg mb-4">Revenue trend</h2>
            @if($trend->isEmpty())
                <p class="text-sm text-neutral-500">No paid sales in this period.</p>
            @else
                @php $max = $trend->max('total') ?: 1; @endphp
                <div class="flex items-end gap-1 h-40">
                    @foreach($trend as $day)
                        <div class="flex-1 group relative">
                            <div class="bg-wine-700/80 group-hover:bg-wine-700 rounded-t-sm min-h-[2px] transition-colors"
                                 style="height: {{ max(2, round($day->total / $max * 150)) }}px"></div>
                            <div class="hidden group-hover:block absolute -top-9 left-1/2 -translate-x-1/2 bg-ink text-cream text-[10px] rounded px-2 py-1 whitespace-nowrap z-10">
                                {{ \Carbon\Carbon::parse($day->day)->format('j M') }} · {{ money($day->total) }}
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="flex justify-between text-[10px] text-neutral-400 mt-2">
                    <span>{{ \Carbon\Carbon::parse($trend->first()->day)->format('j M') }}</span>
                    <span>{{ \Carbon\Carbon::parse($trend->last()->day)->format('j M') }}</span>
                </div>
            @endif
        </div>

        {{-- Channel split --}}
        <div class="card p-5 space-y-5">
            <h2 class="font-display text-lg">Channels</h2>
            @php $totalRev = max(1, $channels['online']['revenue'] + $channels['pos']['revenue']); @endphp
            @foreach(['online' => 'Online store', 'pos' => 'POS / in person'] as $key => $label)
                @php $share = round($channels[$key]['revenue'] / $totalRev * 100); @endphp
                <div>
                    <div class="flex justify-between text-sm mb-1.5">
                        <span>{{ $label }}</span>
                        <span class="text-neutral-500">{{ money($channels[$key]['revenue']) }} · {{ $channels[$key]['orders'] }} orders</span>
                    </div>
                    <div class="h-2 rounded-full bg-neutral-100 overflow-hidden">
                        <div class="h-full {{ $key === 'online' ? 'bg-wine-700' : 'bg-ink' }}" style="width: {{ $share }}%"></div>
                    </div>
                    <p class="text-[11px] text-neutral-400 mt-1">{{ $share }}% of revenue</p>
                </div>
            @endforeach

            <div class="border-t border-neutral-100 pt-4">
                <h3 class="text-xs uppercase tracking-wider text-neutral-500 mb-3">Payment methods</h3>
                @forelse($methods as $method => $m)
                    <div class="flex justify-between text-sm py-1.5">
                        <span class="capitalize">{{ str_replace('_', ' ', $method) }}</span>
                        <span class="text-neutral-500">{{ $m['orders'] }} · {{ money($m['revenue']) }}</span>
                    </div>
                @empty
                    <p class="text-sm text-neutral-400">No data yet.</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="grid lg:grid-cols-2 gap-6 mb-6">
        {{-- Top products --}}
        <div class="card p-5">
            <h2 class="font-display text-lg mb-4">Top products by revenue</h2>
            @if($topProducts->isEmpty())
                <p class="text-sm text-neutral-500">No sales in this period.</p>
            @else
                @php $maxRev = $topProducts->max('revenue') ?: 1; @endphp
                <ul class="space-y-3">
                    @foreach($topProducts as $p)
                        <li>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="truncate pr-3">{{ $p->product_name }}</span>
                                <span class="shrink-0 text-neutral-500">{{ $p->units }} sold · <span class="font-semibold text-ink">{{ money($p->revenue) }}</span></span>
                            </div>
                            <div class="h-1.5 rounded-full bg-neutral-100 overflow-hidden">
                                <div class="h-full bg-wine-700" style="width: {{ round($p->revenue / $maxRev * 100) }}%"></div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        {{-- Top categories --}}
        <div class="card p-5">
            <h2 class="font-display text-lg mb-4">Top categories</h2>
            @if($topCategories->isEmpty())
                <p class="text-sm text-neutral-500">No sales in this period.</p>
            @else
                @php $maxCat = $topCategories->max('revenue') ?: 1; @endphp
                <ul class="space-y-3">
                    @foreach($topCategories as $c)
                        <li>
                            <div class="flex justify-between text-sm mb-1">
                                <span>{{ $c->category }}</span>
                                <span class="text-neutral-500">{{ $c->units }} items · <span class="font-semibold text-ink">{{ money($c->revenue) }}</span></span>
                            </div>
                            <div class="h-1.5 rounded-full bg-neutral-100 overflow-hidden">
                                <div class="h-full bg-ink" style="width: {{ round($c->revenue / $maxCat * 100) }}%"></div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>

    <div class="grid lg:grid-cols-2 gap-6">
        {{-- Product views → conversion --}}
        <div class="card p-5">
            <div class="flex items-baseline justify-between mb-4">
                <h2 class="font-display text-lg">Most viewed products</h2>
                <p class="text-xs text-neutral-400">{{ number_format($engagement['total_views']) }} total views</p>
            </div>
            @if($mostViewed->isEmpty())
                <p class="text-sm text-neutral-500">No product views recorded yet.</p>
            @else
                <table class="w-full text-sm">
                    <thead><tr class="text-left text-[11px] uppercase tracking-wider text-neutral-500 border-b border-neutral-200">
                        <th class="pb-2">Product</th><th class="pb-2 text-right">Views</th><th class="pb-2 text-right">Sold</th><th class="pb-2 text-right">Conv.</th>
                    </tr></thead>
                    <tbody class="divide-y divide-neutral-100">
                        @foreach($mostViewed as $p)
                            <tr>
                                <td class="py-2.5 pr-3 truncate">{{ $p->name }}</td>
                                <td class="py-2.5 text-right text-neutral-500">{{ number_format($p->views_count) }}</td>
                                <td class="py-2.5 text-right">{{ $p->units_sold }}</td>
                                <td class="py-2.5 text-right"><span class="{{ $p->conversion >= 10 ? 'text-green-700 font-semibold' : 'text-neutral-500' }}">{{ $p->conversion }}%</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        <div class="space-y-6">
            {{-- Top customers --}}
            <div class="card p-5">
                <h2 class="font-display text-lg mb-4">Top customers</h2>
                @if($topCustomers->isEmpty())
                    <p class="text-sm text-neutral-500">No customer data yet.</p>
                @else
                    <ul class="divide-y divide-neutral-100 text-sm">
                        @foreach($topCustomers as $c)
                            <li class="py-2.5 flex justify-between items-center">
                                <a href="{{ route('admin.customers.show', $c->email) }}" class="hover:text-wine-700">
                                    <span class="font-medium">{{ $c->name ?: $c->email }}</span>
                                    <span class="block text-xs text-neutral-400">{{ $c->orders }} {{ Str::plural('order', $c->orders) }}</span>
                                </a>
                                <span class="font-semibold">{{ money($c->spent) }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            {{-- Pipeline + engagement --}}
            <div class="card p-5">
                <h2 class="font-display text-lg mb-4">Order pipeline</h2>
                <div class="flex flex-wrap gap-2">
                    @foreach(['paid' => 'Paid', 'pending' => 'Pending', 'failed' => 'Failed', 'abandoned' => 'Abandoned', 'refunded' => 'Refunded'] as $s => $l)
                        <span class="text-xs px-3 py-1.5 rounded-full border {{ $s === 'paid' ? 'bg-green-50 border-green-200 text-green-800' : ($s === 'pending' ? 'bg-amber-50 border-amber-200 text-amber-800' : 'bg-neutral-50 border-neutral-200 text-neutral-600') }}">
                            {{ $l }}: {{ $pipeline[$s] ?? 0 }}
                        </span>
                    @endforeach
                </div>
                <div class="border-t border-neutral-100 mt-4 pt-4 flex gap-6 text-sm">
                    <p><span class="font-display text-xl">{{ number_format($engagement['subscribers']) }}</span> <span class="text-neutral-500">newsletter subscribers</span></p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

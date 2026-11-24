<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $range = in_array($request->range, ['7', '30', '90', 'all']) ? $request->range : '30';
        $from = $range === 'all' ? null : now()->subDays((int) $range)->startOfDay();

        $paid = fn ($q) => $q->where('payment_status', Order::PAYMENT_PAID)
            ->when($from, fn ($q) => $q->where('updated_at', '>=', $from));

        $paidOrders = Order::where($paid)->get();

        // Headline KPIs
        $kpis = [
            'revenue' => $paidOrders->sum('total'),
            'orders' => $paidOrders->count(),
            'aov' => $paidOrders->count() ? (int) round($paidOrders->avg('total')) : 0,
            'items' => OrderItem::whereHas('order', $paid)->sum('quantity'),
            'customers' => $paidOrders->whereNotNull('customer_email')->pluck('customer_email')->map('strtolower')->unique()->count(),
            'preorders' => OrderItem::whereHas('order', $paid)->where('is_preorder', true)->count(),
        ];

        // Daily revenue trend
        $trend = Order::where($paid)
            ->selectRaw('DATE(updated_at) as day, SUM(total) as total, COUNT(*) as orders')
            ->groupBy('day')->orderBy('day')->get();

        // Channel split
        $channels = [
            'online' => ['orders' => 0, 'revenue' => 0],
            'pos' => ['orders' => 0, 'revenue' => 0],
        ];
        foreach ($paidOrders as $o) {
            $channels[$o->channel === 'pos' ? 'pos' : 'online']['orders']++;
            $channels[$o->channel === 'pos' ? 'pos' : 'online']['revenue'] += $o->total;
        }

        // Payment methods
        $methods = $paidOrders->groupBy(fn ($o) => $o->payment_method ?? 'other')
            ->map(fn ($g) => ['orders' => $g->count(), 'revenue' => $g->sum('total')])
            ->sortByDesc('revenue');

        // Top products by revenue
        $topProducts = OrderItem::selectRaw('product_name, SUM(quantity) as units, SUM(total) as revenue')
            ->whereHas('order', $paid)
            ->groupBy('product_name')->orderByDesc('revenue')->take(8)->get();

        // Top categories (via order item → product → category)
        $topCategories = OrderItem::selectRaw('categories.name as category, SUM(order_items.total) as revenue, SUM(order_items.quantity) as units')
            ->join('products', 'products.id', '=', 'order_items.product_id')
            ->join('categories', 'categories.id', '=', 'products.category_id')
            ->whereHas('order', $paid)
            ->groupBy('categories.name')->orderByDesc('revenue')->take(6)->get();

        // Most viewed products (all-time views) + units sold in range → conversion
        $soldByProduct = OrderItem::selectRaw('product_id, SUM(quantity) as units')
            ->whereHas('order', $paid)->groupBy('product_id')->pluck('units', 'product_id');
        $mostViewed = Product::where('status', 'active')->where('views_count', '>', 0)
            ->orderByDesc('views_count')->take(8)->get()
            ->map(function ($p) use ($soldByProduct) {
                $p->units_sold = (int) ($soldByProduct[$p->id] ?? 0);
                $p->conversion = $p->views_count > 0 ? round($p->units_sold / $p->views_count * 100, 1) : 0;
                return $p;
            });

        // Top customers by spend in range
        $topCustomers = $paidOrders->whereNotNull('customer_email')
            ->groupBy(fn ($o) => strtolower($o->customer_email))
            ->map(fn ($g) => (object) [
                'email' => strtolower($g->first()->customer_email),
                'name' => $g->first()->customer_name,
                'orders' => $g->count(),
                'spent' => $g->sum('total'),
            ])->sortByDesc('spent')->take(6)->values();

        // Order pipeline snapshot (all orders in range regardless of status)
        $pipeline = Order::when($from, fn ($q) => $q->where('created_at', '>=', $from))
            ->selectRaw('payment_status, COUNT(*) as n')->groupBy('payment_status')->pluck('n', 'payment_status');

        $engagement = [
            'subscribers' => NewsletterSubscriber::where('is_active', true)->count(),
            'total_views' => Product::sum('views_count'),
        ];

        return view('admin.analytics.index', compact(
            'range', 'kpis', 'trend', 'channels', 'methods',
            'topProducts', 'topCategories', 'mostViewed', 'topCustomers',
            'pipeline', 'engagement'
        ));
    }
}

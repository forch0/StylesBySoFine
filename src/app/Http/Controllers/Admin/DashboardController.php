<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\Order;
use App\Models\ProductVariant;

class DashboardController extends Controller
{
    public function index()
    {
        $today = now()->startOfDay();

        return view('admin.dashboard', [
            'ordersToday' => Order::where('created_at', '>=', $today)->count(),
            'awaitingProcessing' => Order::where('payment_status', Order::PAYMENT_PAID)
                ->whereIn('fulfilment_status', [Order::FULFIL_UNFULFILLED, Order::FULFIL_PROCESSING])
                ->count(),
            'revenueToday' => Order::where('payment_status', Order::PAYMENT_PAID)->where('updated_at', '>=', $today)->sum('total'),
            'revenueMonth' => Order::where('payment_status', Order::PAYMENT_PAID)
                ->where('updated_at', '>=', now()->startOfMonth())->sum('total'),
            'recentOrders' => Order::latest()->take(8)->get(),
            'lowStock' => ProductVariant::where('is_active', true)
                ->whereRaw('stock_quantity <= COALESCE(low_stock_threshold, 3)')
                ->with('product')->orderBy('stock_quantity')->take(8)->get(),
            'bestsellers' => \App\Models\OrderItem::selectRaw('product_name, SUM(quantity) as sold')
                ->whereHas('order', fn ($q) => $q->where('payment_status', Order::PAYMENT_PAID))
                ->groupBy('product_name')->orderByDesc('sold')->take(5)->get(),
            'unreadMessages' => ContactMessage::where('is_read', false)->count(),
            'salesTrend' => Order::where('payment_status', Order::PAYMENT_PAID)
                ->where('updated_at', '>=', now()->subDays(14))
                ->selectRaw('DATE(updated_at) as day, SUM(total) as total')
                ->groupBy('day')->orderBy('day')->get(),
        ]);
    }
}

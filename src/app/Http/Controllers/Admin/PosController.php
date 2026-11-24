<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Order;
use App\Models\Product;
use App\Services\OrderService;
use Illuminate\Http\Request;

class PosController extends Controller
{
    public function __construct(protected OrderService $orders) {}

    public function index()
    {
        return view('admin.pos.index', [
            'recentSales' => Order::where('channel', Order::CHANNEL_POS)->latest()->take(10)->get(),
            'todaySales' => Order::where('channel', Order::CHANNEL_POS)
                ->where('payment_status', Order::PAYMENT_PAID)
                ->where('created_at', '>=', now()->startOfDay())
                ->sum('total'),
        ]);
    }

    /** JSON product/variant search for the POS screen. */
    public function search(Request $request)
    {
        $q = trim($request->string('q')->toString());

        $products = Product::active()
            ->with(['media', 'variants' => fn ($v) => $v->where('is_active', true)])
            ->when($q, fn ($query) => $query->where(fn ($w) => $w->where('name', 'like', "%{$q}%")->orWhere('sku', 'like', "%{$q}%")))
            ->orderBy('name')
            ->take(12)
            ->get()
            ->map(fn (Product $p) => [
                'id' => $p->id,
                'name' => $p->name,
                'price' => $p->price,
                'price_display' => money($p->price),
                'image' => $p->media->first()?->url(),
                'variants' => $p->variants->map(fn ($v) => [
                    'id' => $v->id,
                    'summary' => $v->summary(),
                    'price' => $v->effectivePrice(),
                    'price_display' => money($v->effectivePrice()),
                    'stock' => $v->stock_quantity,
                ])->values(),
            ]);

        return response()->json($products);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.variant_id' => 'required|exists:product_variants,id',
            'items.*.quantity' => 'required|integer|min:1|max:100',
            'payment_method' => 'required|in:cash,transfer,pos_terminal,other',
            'customer_name' => 'nullable|string|max:120',
            'customer_phone' => 'nullable|string|max:30',
            'customer_email' => 'nullable|email|max:160',
            'discount_naira' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        $sale = [
            'payment_method' => $data['payment_method'],
            'customer_name' => $data['customer_name'] ?? null,
            'customer_phone' => $data['customer_phone'] ?? null,
            'customer_email' => $data['customer_email'] ?? null,
            'discount' => \App\Support\Money::toKobo($data['discount_naira'] ?? 0),
            'notes' => $data['notes'] ?? null,
        ];

        try {
            $order = $this->orders->createPosSale($data['items'], $sale, $request->user()->id);
        } catch (\RuntimeException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }

        AuditLog::record('pos.sale', $order, ['total' => $order->total, 'method' => $order->payment_method]);

        return response()->json([
            'order_number' => $order->order_number,
            'receipt_url' => route('admin.pos.receipt', $order),
        ]);
    }

    public function receipt(Order $order)
    {
        abort_unless($order->channel === Order::CHANNEL_POS, 404);

        $order->load('items', 'soldBy');

        return view('admin.pos.receipt', compact('order'));
    }
}

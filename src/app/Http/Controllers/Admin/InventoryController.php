<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\ProductVariant;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    public function __construct(protected InventoryService $inventory) {}

    public function index(Request $request)
    {
        $variants = ProductVariant::with('product.media')
            ->when($request->q, fn ($q) => $q->whereHas('product', fn ($w) => $w->where('name', 'like', '%'.$request->q.'%')))
            ->when($request->filter === 'low', fn ($q) => $q->whereRaw('stock_quantity <= COALESCE(low_stock_threshold, 3)')->where('stock_quantity', '>', 0))
            ->when($request->filter === 'out', fn ($q) => $q->where('stock_quantity', '<=', 0))
            ->join('products', 'products.id', '=', 'product_variants.product_id')
            ->orderBy('products.name')
            ->select('product_variants.*')
            ->paginate(30)->withQueryString();

        return view('admin.inventory.index', compact('variants'));
    }

    public function adjust(Request $request, ProductVariant $variant)
    {
        $data = $request->validate([
            'change' => 'required|integer|not_in:0',
            'reason' => 'required|string|max:200',
        ]);

        try {
            DB::transaction(function () use ($request, $variant, $data) {
                $variant = ProductVariant::where('id', $variant->id)->lockForUpdate()->firstOrFail();

                if ($variant->stock_quantity + $data['change'] < 0) {
                    throw new \RuntimeException('Stock cannot go below zero.');
                }

                $this->inventory->adjust($variant, $data['change'], $data['reason'], null, $request->user()->id);
            });
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        AuditLog::record('inventory.adjusted', $variant, $data);

        return back()->with('success', 'Stock updated.');
    }

    public function history(ProductVariant $variant)
    {
        $variant->load('product', 'movements.user', 'movements.order');

        return view('admin.inventory.history', compact('variant'));
    }
}

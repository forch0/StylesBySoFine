<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\OrderStatusMail;
use App\Models\AuditLog;
use App\Models\Order;
use App\Models\ProductVariant;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class OrderController extends Controller
{
    public function __construct(protected InventoryService $inventory) {}

    public function index(Request $request)
    {
        $orders = Order::with('items')
            ->when($request->q, fn ($q) => $q->where(fn ($w) => $w
                ->where('order_number', 'like', '%'.$request->q.'%')
                ->orWhere('customer_name', 'like', '%'.$request->q.'%')
                ->orWhere('customer_email', 'like', '%'.$request->q.'%')))
            ->when($request->payment_status, fn ($q) => $q->where('payment_status', $request->payment_status))
            ->when($request->fulfilment_status, fn ($q) => $q->where('fulfilment_status', $request->fulfilment_status))
            ->when($request->channel, fn ($q) => $q->where('channel', $request->channel))
            ->latest()->paginate(20)->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load('items', 'payments', 'statusHistories.user', 'deliveryZone', 'soldBy');

        return view('admin.orders.show', compact('order'));
    }

    public function updateFulfilment(Request $request, Order $order)
    {
        $data = $request->validate([
            'fulfilment_status' => 'required|in:unfulfilled,processing,ready,shipped,delivered,cancelled',
            'note' => 'nullable|string|max:500',
        ]);

        $from = $order->fulfilment_status;
        $order->update(['fulfilment_status' => $data['fulfilment_status']]);
        $order->recordStatusChange('fulfilment_status', $from, $data['fulfilment_status'], $request->user()->id, $data['note'] ?? null);

        AuditLog::record('order.fulfilment', $order, ['from' => $from, 'to' => $data['fulfilment_status']]);

        // Notify the customer on meaningful transitions (email failures are non-fatal)
        if ($from !== $order->fulfilment_status && $order->customer_email
            && in_array($order->fulfilment_status, ['processing', 'shipped', 'delivered', 'cancelled'], true)) {
            try {
                Mail::to($order->customer_email)->send(new OrderStatusMail($order));
            } catch (\Throwable $e) {
                Log::warning('Status email failed for '.$order->order_number.': '.$e->getMessage());
            }
        }

        return back()->with('success', 'Order status updated.');
    }

    public function cancel(Request $request, Order $order)
    {
        if ($order->fulfilment_status === Order::FULFIL_CANCELLED) {
            return back()->with('error', 'Order is already cancelled.');
        }
        if ($order->fulfilment_status === Order::FULFIL_DELIVERED) {
            return back()->with('error', 'Delivered orders cannot be cancelled — process a refund instead.');
        }

        DB::transaction(function () use ($request, $order) {
            $from = $order->fulfilment_status;
            $order->update(['fulfilment_status' => Order::FULFIL_CANCELLED]);
            $order->recordStatusChange('fulfilment_status', $from, Order::FULFIL_CANCELLED, $request->user()->id, $request->input('reason'));

            // Restock items if stock was already deducted (paid orders)
            if ($order->payment_status === Order::PAYMENT_PAID) {
                foreach ($order->items as $item) {
                    if ($variant = ProductVariant::find($item->product_variant_id)) {
                        $this->inventory->adjust($variant, $item->quantity, 'Restock — cancelled '.$order->order_number, $order->id, $request->user()->id);
                    }
                }
            }
        });

        AuditLog::record('order.cancelled', $order);

        return back()->with('success', 'Order cancelled'.($order->isPaid() ? ' and stock restored.' : '.'));
    }

    public function addNote(Request $request, Order $order)
    {
        $data = $request->validate(['note' => 'required|string|max:1000']);

        $order->recordStatusChange('note', null, 'note', $request->user()->id, $data['note']);

        return back()->with('success', 'Note added.');
    }
}

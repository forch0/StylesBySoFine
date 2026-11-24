<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Receipt {{ $order->order_number }}</title>
    @vite(['resources/css/app.css'])
    <style>@media print { .no-print { display: none; } body { background: white; } }</style>
</head>
<body class="bg-neutral-100 py-8">
    <div class="max-w-sm mx-auto bg-white p-8 shadow">
        <div class="text-center mb-6">
            <img src="{{ asset('images/logo.png') }}" alt="Styles By So Fine" class="h-16 w-auto mx-auto">
            <p class="text-xs text-neutral-500 mt-1">Receipt — {{ $order->created_at->format('j M Y, g:ia') }}</p>
        </div>

        <div class="border-t border-dashed border-neutral-300 pt-4 mb-4 text-xs space-y-1">
            <p><span class="text-neutral-500">Order:</span> {{ $order->order_number }}</p>
            <p><span class="text-neutral-500">Customer:</span> {{ $order->customerDisplayName() }}</p>
            <p><span class="text-neutral-500">Sold by:</span> {{ $order->soldBy?->name ?? '—' }}</p>
            <p><span class="text-neutral-500">Payment:</span> <span class="capitalize">{{ str_replace('_', ' ', $order->payment_method) }}</span></p>
        </div>

        <div class="border-t border-dashed border-neutral-300 pt-4 space-y-2 text-sm">
            @foreach($order->items as $item)
                <div class="flex justify-between">
                    <span>{{ $item->product_name }} × {{ $item->quantity }}
                        @if($item->variant_summary)<span class="block text-xs text-neutral-500">{{ $item->variant_summary }}</span>@endif
                    </span>
                    <span>{{ money($item->total) }}</span>
                </div>
            @endforeach
        </div>

        <div class="border-t border-dashed border-neutral-300 mt-4 pt-4 space-y-1 text-sm">
            <div class="flex justify-between"><span>Subtotal</span><span>{{ money($order->subtotal) }}</span></div>
            @if($order->discount > 0)
                <div class="flex justify-between"><span>Discount</span><span>−{{ money($order->discount) }}</span></div>
            @endif
            <div class="flex justify-between font-semibold text-base pt-2">
                <span>Total</span><span>{{ money($order->total) }}</span>
            </div>
        </div>

        <p class="text-center text-xs text-neutral-500 mt-8">Thank you for shopping with us.</p>
    </div>

    <div class="text-center mt-6 no-print space-x-4">
        <button onclick="window.print()" class="btn-primary text-sm">Print receipt</button>
        <a href="{{ route('admin.pos.index') }}" class="btn-outline text-sm">Back to POS</a>
    </div>
</body>
</html>

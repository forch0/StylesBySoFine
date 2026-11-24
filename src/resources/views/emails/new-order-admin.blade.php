@extends('emails.layout')
@section('title', 'New paid order — ' . $order->order_number)
@section('preheader', $order->customer_name . ' paid ₦' . number_format($order->total / 100, 2))

@section('content')
<h1 style="margin:0 0 6px;font-size:24px;color:#2b2b2b;font-weight:normal;">New paid order 🛍</h1>
<p style="margin:0 0 20px;font-family:Arial,sans-serif;font-size:13px;line-height:1.7;color:#6b6b6b;">
    <strong>{{ $order->customer_name }}</strong> paid <strong style="color:#7d1f3f;">₦{{ number_format($order->total / 100, 2) }}</strong>
    for order <strong>{{ $order->order_number }}</strong>.
</p>

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="font-family:Arial,sans-serif;font-size:13px;color:#2b2b2b;border-top:1px solid #e8e4df;">
    @foreach($order->items as $item)
        <tr>
            <td style="padding:10px 0;border-bottom:1px solid #f0ece7;">
                {{ $item->product_name }}{{ $item->variant_summary ? ' · '.$item->variant_summary : '' }}
                @if($item->is_preorder)<span style="font-size:11px;color:#7d1f3f;">· PRE-ORDER</span>@endif
            </td>
            <td align="right" style="padding:10px 0;border-bottom:1px solid #f0ece7;">×{{ $item->quantity }}</td>
        </tr>
    @endforeach
</table>

<p style="margin:18px 0 4px;font-family:Arial,sans-serif;font-size:12px;color:#6b6b6b;">
    <strong>Deliver to:</strong> {{ $order->delivery_address }}{{ $order->city ? ', '.$order->city : '' }}{{ $order->state ? ', '.$order->state : '' }}
    ({{ $order->delivery_zone_id ? ($order->deliveryZone?->name ?? 'no zone') : 'no zone' }})
</p>
<p style="margin:0 0 24px;font-family:Arial,sans-serif;font-size:12px;color:#6b6b6b;">
    <strong>Contact:</strong> {{ $order->customer_email }} · {{ $order->customer_phone }}
</p>

<table role="presentation" cellpadding="0" cellspacing="0" style="margin:0 auto;">
    <tr><td style="border-radius:999px;background-color:#7d1f3f;">
        <a href="{{ route('admin.orders.show', $order) }}" style="display:inline-block;padding:13px 36px;font-family:Arial,sans-serif;font-size:13px;color:#ffffff;text-decoration:none;">Open order in admin →</a>
    </td></tr>
</table>
@endsection

@extends('emails.layout')
@section('title', 'Order confirmed — ' . $order->order_number)
@section('preheader', 'Payment received. Your order ' . $order->order_number . ' is confirmed — dispatch Mon/Wed/Fri.')

@section('content')
<h1 style="margin:0 0 6px;font-size:24px;color:#2b2b2b;font-weight:normal;">Thank you, {{ Str::of($order->customer_name)->before(' ') }}.</h1>
<p style="margin:0 0 24px;font-family:Arial,sans-serif;font-size:13px;line-height:1.7;color:#6b6b6b;">
    Payment received — your order <strong style="color:#7d1f3f;">{{ $order->order_number }}</strong> is confirmed.
    We dispatch every <strong>Monday, Wednesday &amp; Friday</strong>; orders confirmed before 8:00 AM go out the same day.
</p>

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-top:1px solid #e8e4df;">
    @foreach($order->items as $item)
        <tr>
            <td style="padding:14px 0;border-bottom:1px solid #f0ece7;font-family:Arial,sans-serif;font-size:13px;color:#2b2b2b;">
                {{ $item->product_name }}
                @if($item->variant_summary)<span style="color:#8a8a8a;">· {{ $item->variant_summary }}</span>@endif
                @if($item->is_preorder)<br><span style="font-size:11px;color:#7d1f3f;">Pre-order{{ $item->product?->preorder_note ? ' — '.$item->product->preorder_note : '' }}</span>@endif
            </td>
            <td align="right" style="padding:14px 0;border-bottom:1px solid #f0ece7;font-family:Arial,sans-serif;font-size:13px;color:#2b2b2b;white-space:nowrap;">
                ×{{ $item->quantity }} · ₦{{ number_format($item->total / 100, 2) }}
            </td>
        </tr>
    @endforeach
</table>

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-top:14px;font-family:Arial,sans-serif;font-size:13px;color:#6b6b6b;">
    <tr><td style="padding:3px 0;">Subtotal</td><td align="right">₦{{ number_format($order->subtotal / 100, 2) }}</td></tr>
    @if($order->discount > 0)
        <tr><td style="padding:3px 0;">Discount{{ $order->coupon_code ? ' ('.$order->coupon_code.')' : '' }}</td><td align="right">−₦{{ number_format($order->discount / 100, 2) }}</td></tr>
    @endif
    <tr><td style="padding:3px 0;">Delivery</td><td align="right">Quoted separately</td></tr>
    <tr><td style="padding:10px 0 0;font-size:15px;font-weight:bold;color:#2b2b2b;border-top:1px solid #e8e4df;">Paid</td>
        <td align="right" style="padding:10px 0 0;font-size:15px;font-weight:bold;color:#7d1f3f;border-top:1px solid #e8e4df;">₦{{ number_format($order->total / 100, 2) }}</td></tr>
</table>

<div style="margin:24px 0;padding:14px 16px;background-color:#faf5f2;border-left:3px solid #7d1f3f;border-radius:0 8px 8px 0;">
    <p style="margin:0;font-family:Arial,sans-serif;font-size:12px;line-height:1.6;color:#5c5c5c;">
        <strong style="color:#7d1f3f;">Delivery is paid separately.</strong> We'll WhatsApp you the delivery cost for
        {{ $order->delivery_zone_id ? ($order->deliveryZone?->name ?? 'your area') : 'your area' }} — pay the rider on arrival or transfer to us directly.
        Delivering to: {{ $order->delivery_address }}{{ $order->city ? ', '.$order->city : '' }}{{ $order->state ? ', '.$order->state : '' }}.
    </p>
</div>

<table role="presentation" cellpadding="0" cellspacing="0" style="margin:0 auto;">
    <tr><td style="border-radius:999px;background-color:#7d1f3f;">
        <a href="{{ route('track') }}" style="display:inline-block;padding:13px 36px;font-family:Arial,sans-serif;font-size:13px;color:#ffffff;text-decoration:none;">Track your order →</a>
    </td></tr>
</table>
<p style="margin:18px 0 0;text-align:center;font-family:Arial,sans-serif;font-size:11px;color:#8a8a8a;">
    You'll need your order number ({{ $order->order_number }}) and the email address used at checkout.
</p>
@endsection

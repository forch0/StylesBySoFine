@extends('emails.layout')
@section('title', $isShipped ? 'Your order is on its way' : 'Order ' . $order->order_number . ' ' . $order->fulfilment_status)
@section('preheader', $isShipped ? 'Order ' . $order->order_number . ' has been dispatched.' : 'Update on order ' . $order->order_number)

@section('content')
@if($isShipped)
    <h1 style="margin:0 0 6px;font-size:24px;color:#2b2b2b;font-weight:normal;">It's on its way, {{ Str::of($order->customer_name)->before(' ') }}.</h1>
    <p style="margin:0 0 20px;font-family:Arial,sans-serif;font-size:13px;line-height:1.7;color:#6b6b6b;">
        Order <strong style="color:#7d1f3f;">{{ $order->order_number }}</strong> has been dispatched.
        Delivery within Lagos is typically <strong>24 hours</strong>; elsewhere in Nigeria <strong>48–72 hours</strong>.
    </p>
    <div style="margin:0 0 20px;padding:14px 16px;background-color:#faf5f2;border-left:3px solid #7d1f3f;border-radius:0 8px 8px 0;">
        <p style="margin:0;font-family:Arial,sans-serif;font-size:12px;line-height:1.6;color:#5c5c5c;">
            <strong style="color:#7d1f3f;">Reminder — delivery is paid separately.</strong> The rider will confirm the
            delivery cost, or we'll WhatsApp it to you. Delivering to: {{ $order->delivery_address }}{{ $order->city ? ', '.$order->city : '' }}.
        </p>
    </div>
@elseif($order->fulfilment_status === 'delivered')
    <h1 style="margin:0 0 6px;font-size:24px;color:#2b2b2b;font-weight:normal;">Delivered — hope you love it.</h1>
    <p style="margin:0 0 20px;font-family:Arial,sans-serif;font-size:13px;line-height:1.7;color:#6b6b6b;">
        Order <strong style="color:#7d1f3f;">{{ $order->order_number }}</strong> is marked as delivered.
        If anything isn't right, please message us on WhatsApp within 24 hours — we offer exchanges on unworn items.
    </p>
@else
    <h1 style="margin:0 0 6px;font-size:24px;color:#2b2b2b;font-weight:normal;">An update on your order.</h1>
    <p style="margin:0 0 20px;font-family:Arial,sans-serif;font-size:13px;line-height:1.7;color:#6b6b6b;">
        Order <strong style="color:#7d1f3f;">{{ $order->order_number }}</strong> is now
        <strong>{{ ucfirst($order->fulfilment_status) }}</strong>.
    </p>
@endif

<table role="presentation" cellpadding="0" cellspacing="0" style="margin:0 auto;">
    <tr><td style="border-radius:999px;background-color:#7d1f3f;">
        <a href="{{ route('track') }}" style="display:inline-block;padding:13px 36px;font-family:Arial,sans-serif;font-size:13px;color:#ffffff;text-decoration:none;">Track your order →</a>
    </td></tr>
</table>
@endsection

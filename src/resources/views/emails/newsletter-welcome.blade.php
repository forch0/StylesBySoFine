@extends('emails.layout')
@section('title', 'Welcome to the So Fine list')
@section('preheader', 'You are on the list — new arrivals, restocks and insider offers, straight to you.')

@section('content')
<h1 style="margin:0 0 6px;font-size:24px;color:#2b2b2b;font-weight:normal;">Welcome to the list.</h1>
<p style="margin:0 0 20px;font-family:Arial,sans-serif;font-size:13px;line-height:1.7;color:#6b6b6b;">
    Thanks for subscribing — you'll be first to hear about <strong>new arrivals</strong>, <strong>restocks</strong>
    and subscriber-only offers. We only send the good stuff, and you can unsubscribe anytime.
</p>

<table role="presentation" cellpadding="0" cellspacing="0" style="margin:0 auto;">
    <tr><td style="border-radius:999px;background-color:#7d1f3f;">
        <a href="{{ route('shop') }}" style="display:inline-block;padding:13px 36px;font-family:Arial,sans-serif;font-size:13px;color:#ffffff;text-decoration:none;">Shop the boutique →</a>
    </td></tr>
</table>
@endsection

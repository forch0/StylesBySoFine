<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name'))</title>
</head>
<body style="margin:0;padding:0;background-color:#faf8f5;font-family:Georgia,'Times New Roman',serif;">
    <div style="display:none;max-height:0;overflow:hidden;">@yield('preheader')</div>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#faf8f5;">
        <tr><td align="center" style="padding:32px 16px;">
            <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;">

                {{-- Header --}}
                <tr><td align="center" style="background-color:#3d1220;border-radius:12px 12px 0 0;padding:28px;">
                    <img src="{{ config('app.url') }}/images/logo.png" alt="Styles By So Fine" width="72" style="display:block;width:72px;height:auto;border-radius:8px;background-color:#faf8f5;">
                    <p style="margin:14px 0 0;color:#e8c4cf;font-family:Arial,sans-serif;font-size:10px;letter-spacing:3px;text-transform:uppercase;">Dress well · Feel confident · Be So Fine</p>
                </td></tr>

                {{-- Body --}}
                <tr><td style="background-color:#ffffff;padding:36px 32px;border:1px solid #e8e4df;border-top:0;border-radius:0 0 12px 12px;">
                    @yield('content')
                </td></tr>

                {{-- Footer --}}
                <tr><td style="padding:24px 16px;text-align:center;background-color:transparent;">
                    <p style="margin:0 0 8px;font-family:Arial,sans-serif;font-size:11px;color:#8a8a8a;">
                        Styles By So Fine · Lagos, Nigeria · Nationwide delivery
                    </p>
                    <p style="margin:0;font-family:Arial,sans-serif;font-size:11px;color:#8a8a8a;">
                        WhatsApp: 09020501822 · sales@stylesbysofine.ng
                    </p>
                    @if(isset($unsubscribeUrl) && $unsubscribeUrl)
                        <p style="margin:10px 0 0;font-family:Arial,sans-serif;font-size:11px;">
                            <a href="{{ $unsubscribeUrl }}" style="color:#7d1f3f;">Unsubscribe from these emails</a>
                        </p>
                    @endif
                </td></tr>

            </table>
        </td></tr>
    </table>
</body>
</html>

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');

        if ($request->secure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        // Allowlist what pages may load — Paystack checkout + Google Fonts.
        // 'unsafe-eval' is required: Alpine.js evaluates x-data expressions
        // via Function() at runtime (no CSP build in use).
        $response->headers->set('Content-Security-Policy', implode('; ', [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://js.paystack.co https://static.cloudflareinsights.com",
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com",
            "font-src 'self' https://fonts.gstatic.com data:",
            "img-src 'self' data: https:",
            "media-src 'self' https:",
            "connect-src 'self' https://api.paystack.co https://cloudflareinsights.com",
            "frame-src https://js.paystack.co https://checkout.paystack.com",
            "form-action 'self'",
            "object-src 'none'",
            "base-uri 'self'",
        ]));

        return $response;
    }
}

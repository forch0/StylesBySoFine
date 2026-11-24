<?php

namespace App\Services;

use App\Models\SiteSetting;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class PaystackService
{
    protected string $base = 'https://api.paystack.co';

    /** Secret key saved in admin settings takes precedence over .env. */
    protected function secretKey(): ?string
    {
        return SiteSetting::get('paystack_secret_key') ?: config('services.paystack.secret_key');
    }

    public function isConfigured(): bool
    {
        return filled($this->secretKey());
    }

    /**
     * Initialize a transaction. Returns ['authorization_url' => ..., 'reference' => ...].
     */
    public function initialize(string $email, int $amountKobo, string $reference, string $callbackUrl, array $metadata = []): array
    {
        $response = Http::withToken($this->secretKey())
            ->post("{$this->base}/transaction/initialize", [
                'email' => $email,
                'amount' => $amountKobo,
                'reference' => $reference,
                'callback_url' => $callbackUrl,
                'currency' => 'NGN',
                'metadata' => $metadata,
            ]);

        if (! $response->successful() || ! $response->json('status')) {
            throw new RuntimeException('Could not initialize payment: '.$response->json('message', 'unknown error'));
        }

        return $response->json('data');
    }

    /**
     * Verify a transaction server-side. Returns the data array.
     */
    public function verify(string $reference): array
    {
        $response = Http::withToken($this->secretKey())
            ->get("{$this->base}/transaction/verify/{$reference}");

        if (! $response->successful()) {
            throw new RuntimeException('Payment verification failed.');
        }

        return $response->json('data') ?? [];
    }

    /** Verify a webhook payload signature against the secret key. */
    public function validWebhookSignature(string $payload, string $signature): bool
    {
        $expected = hash_hmac('sha512', $payload, $this->secretKey() ?? '');

        return hash_equals($expected, $signature);
    }
}

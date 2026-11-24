<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Mail\NewOrderAdminMail;
use App\Mail\OrderConfirmationMail;
use App\Models\Order;
use App\Models\Payment;
use App\Models\SiteSetting;
use App\Services\OrderService;
use App\Services\PaystackService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PaymentController extends Controller
{
    public function __construct(
        protected PaystackService $paystack,
        protected OrderService $orders,
    ) {}

    /** Shopper returns here from Paystack checkout. */
    public function callback(Request $request)
    {
        $reference = $request->query('reference') ?? $request->query('trxref');

        $payment = Payment::where('reference', $reference)->first();
        if (! $payment) {
            return redirect()->route('home')->with('error', 'Payment not found.');
        }

        $order = $payment->order;

        try {
            $data = $this->paystack->verify($reference);
        } catch (\RuntimeException) {
            return redirect()->route('order.confirmation', $order->order_number)
                ->with('error', 'We could not verify your payment yet. If you were charged, contact us with your order number.');
        }

        $success = ($data['status'] ?? null) === 'success'
            && (int) ($data['amount'] ?? 0) === $order->total
            && ($data['currency'] ?? 'NGN') === 'NGN';

        if ($success) {
            $payment->update([
                'status' => 'success',
                'channel' => $data['channel'] ?? null,
                'gateway_response' => $data['gateway_response'] ?? null,
                'payload' => $data,
                'paid_at' => now(),
            ]);

            if ($this->orders->markPaid($order, $payment)) {
                $this->sendOrderEmails($order);
            }

            // Clear the cart + coupon session
            app(\App\Services\CartService::class)->clear();
            session()->forget('coupon_code');
            session(['last_order_id' => $order->id]);

            return redirect()->route('order.confirmation', $order->order_number)
                ->with('success', 'Payment confirmed — thank you!');
        }

        $payment->update(['status' => 'failed', 'payload' => $data]);
        $this->orders->markPaymentFailed($order);

        return redirect()->route('order.confirmation', $order->order_number)
            ->with('error', 'Payment was not successful. You can try checking out again.');
    }

    /** Paystack webhook — server to server. Idempotent. */
    public function webhook(Request $request)
    {
        $signature = $request->header('X-Paystack-Signature', '');

        if (! $this->paystack->validWebhookSignature($request->getContent(), $signature)) {
            return response('Invalid signature', 401);
        }

        $event = $request->json()->all();

        if (($event['event'] ?? null) === 'charge.success') {
            $data = $event['data'] ?? [];
            $reference = $data['reference'] ?? null;

            $payment = $reference ? Payment::where('reference', $reference)->first() : null;

            if ($payment && $payment->order) {
                $order = $payment->order;

                if ((int) ($data['amount'] ?? 0) === $order->total && ($data['currency'] ?? 'NGN') === 'NGN') {
                    $payment->update([
                        'status' => 'success',
                        'channel' => $data['channel'] ?? null,
                        'gateway_response' => $data['gateway_response'] ?? null,
                        'payload' => $data,
                        'paid_at' => now(),
                    ]);

                    if ($this->orders->markPaid($order, $payment)) {
                        $this->sendOrderEmails($order);
                    }
                }
            }
        }

        return response('OK', 200);
    }

    /** Confirmation to the customer + alert to the store. Mail failures never block checkout. */
    protected function sendOrderEmails(Order $order): void
    {
        try {
            if ($order->customer_email) {
                Mail::to($order->customer_email)->send(new OrderConfirmationMail($order));
            }
            $ownerEmail = SiteSetting::get('contact_email');
            if ($ownerEmail) {
                Mail::to($ownerEmail)->send(new NewOrderAdminMail($order));
            }
        } catch (\Throwable $e) {
            Log::warning('Order emails failed for '.$order->order_number.': '.$e->getMessage());
        }
    }
}

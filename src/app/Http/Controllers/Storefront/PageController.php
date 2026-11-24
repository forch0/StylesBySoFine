<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Mail\NewsletterWelcomeMail;
use App\Models\ContactMessage;
use App\Models\NewsletterSubscriber;
use App\Models\Page;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PageController extends Controller
{
    public function show(Page $page)
    {
        abort_unless($page->is_published, 404);

        return view('storefront.page', compact('page'));
    }

    public function contact()
    {
        return view('storefront.contact', [
            'info' => SiteSetting::getMany([
                'contact_email' => '',
                'whatsapp_number' => '',
                'instagram_url' => '',
                'business_hours' => '',
                'response_time' => '',
                'business_address' => '',
            ]),
        ]);
    }

    public function sendContact(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:120',
            'email' => 'required|email|max:160',
            'phone' => 'nullable|string|max:30',
            'subject' => 'required|string|max:160',
            'message' => 'required|string|max:5000',
            'website' => 'nullable|size:0', // honeypot
        ]);

        ContactMessage::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'subject' => $data['subject'],
            'message' => $data['message'],
        ]);

        return back()->with('success', "Thanks — we've received your message and will reply soon.");
    }

    public function newsletter(Request $request)
    {
        $data = $request->validate(['email' => 'required|email|max:160']);

        $subscriber = NewsletterSubscriber::firstOrCreate(
            ['email' => strtolower($data['email'])],
            ['is_active' => true]
        );

        if (! $subscriber->is_active) {
            $subscriber->update(['is_active' => true]); // re-subscribing after unsubscribe
        }

        if ($subscriber->wasRecentlyCreated || $subscriber->wasChanged('is_active')) {
            try {
                Mail::to($subscriber->email)->send(new NewsletterWelcomeMail($subscriber));
            } catch (\Throwable $e) {
                Log::warning('Newsletter welcome mail failed: '.$e->getMessage());
            }
        }

        return back()->with('success', 'You are on the list — thank you! You can unsubscribe anytime.');
    }

    public function unsubscribeConfirm(string $token)
    {
        $subscriber = NewsletterSubscriber::where('unsubscribe_token', $token)->firstOrFail();

        return view('storefront.unsubscribe', compact('subscriber'));
    }

    public function unsubscribe(string $token)
    {
        NewsletterSubscriber::where('unsubscribe_token', $token)
            ->update(['is_active' => false]);

        return redirect()->route('home')->with('success', "You've been unsubscribed — no more emails from us.");
    }
}

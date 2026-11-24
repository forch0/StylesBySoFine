<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\SiteSetting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    protected array $keys = [
        'store_name' => 'general',
        'tagline' => 'general',
        'logo' => 'general',
        'announcement_bar' => 'general',
        'currency' => 'general',
        'contact_email' => 'contact',
        'whatsapp_number' => 'contact',
        'instagram_url' => 'contact',
        'business_address' => 'contact',
        'business_hours' => 'contact',
        'response_time' => 'contact',
        'footer_about' => 'general',
        'free_delivery_threshold' => 'delivery',
        'seo_title' => 'seo',
        'seo_description' => 'seo',
        'paystack_public_key' => 'payments',
        'paystack_secret_key' => 'payments',
    ];

    public function edit()
    {
        $values = [];
        foreach ($this->keys as $key => $group) {
            $values[$key] = SiteSetting::get($key, '');
        }

        // Never render the real secret — show a masked version only.
        $secret = $values['paystack_secret_key'];
        $values['paystack_secret_key'] = '';
        $values['paystack_secret_masked'] = $secret
            ? substr($secret, 0, 8).str_repeat('•', 12).substr($secret, -4)
            : '';

        return view('admin.settings.edit', ['values' => $values]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'store_name' => 'required|string|max:120',
            'contact_email' => 'nullable|email|max:160',
            'free_delivery_threshold' => 'nullable|numeric|min:0',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png,webp,svg|max:2048',
        ]);

        foreach ($this->keys as $key => $group) {
            if ($key === 'logo') {
                if ($request->hasFile('logo')) {
                    SiteSetting::set('logo', $request->file('logo')->store('brand', 'public'), 'general');
                }
                continue;
            }
            if ($key === 'paystack_secret_key') {
                // Blank keeps the saved key; only a new paste overwrites.
                if (filled($request->input($key))) {
                    SiteSetting::set($key, trim($request->input($key)), $group);
                }
                continue;
            }
            if ($key === 'free_delivery_threshold') {
                $value = $request->input($key);
                SiteSetting::set($key, $value !== null && $value !== '' ? \App\Support\Money::toKobo($value) : '', $group);
                continue;
            }
            SiteSetting::set($key, $request->input($key, ''), $group);
        }

        AuditLog::record('settings.updated', null, ['keys' => array_keys($this->keys)]);

        return back()->with('success', 'Settings saved.');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Coupon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function index()
    {
        return view('admin.coupons.index', [
            'coupons' => Coupon::withCount('redemptions')->latest()->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $coupon = Coupon::create($this->moneyize($data) + ['is_active' => $request->boolean('is_active', true)]);

        AuditLog::record('coupon.created', $coupon);

        return back()->with('success', 'Coupon created.');
    }

    public function update(Request $request, Coupon $coupon)
    {
        $data = $this->validated($request, $coupon->id);
        $coupon->update($this->moneyize($data) + ['is_active' => $request->boolean('is_active')]);

        AuditLog::record('coupon.updated', $coupon);

        return back()->with('success', 'Coupon updated.');
    }

    /** Convert naira inputs to kobo where the type requires it. */
    protected function moneyize(array $data): array
    {
        if ($data['type'] === 'fixed') {
            $data['value'] = \App\Support\Money::toKobo($data['value']);
        }
        if (isset($data['min_order_value']) && $data['min_order_value'] !== null) {
            $data['min_order_value'] = \App\Support\Money::toKobo($data['min_order_value']);
        }

        return $data;
    }

    public function destroy(Coupon $coupon)
    {
        AuditLog::record('coupon.deleted', $coupon, ['code' => $coupon->code]);
        $coupon->delete();

        return back()->with('success', 'Coupon deleted.');
    }

    protected function validated(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'code' => 'required|string|max:50|unique:coupons,code'.($ignoreId ? ','.$ignoreId : ''),
            'type' => 'required|in:percentage,fixed,free_delivery',
            'value' => 'required|numeric|min:0',
            'min_order_value' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'per_customer_limit' => 'nullable|integer|min:1',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
        ]);
    }
}

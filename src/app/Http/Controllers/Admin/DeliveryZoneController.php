<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeliveryZone;
use App\Support\Money;
use Illuminate\Http\Request;

class DeliveryZoneController extends Controller
{
    public function index()
    {
        return view('admin.delivery.index', ['zones' => DeliveryZone::orderBy('sort_order')->get()]);
    }

    public function store(Request $request)
    {
        DeliveryZone::create($this->validated($request) + [
            'is_active' => $request->boolean('is_active', true),
        ]);

        return back()->with('success', 'Delivery zone added.');
    }

    public function update(Request $request, DeliveryZone $deliveryZone)
    {
        $deliveryZone->update($this->validated($request) + ['is_active' => $request->boolean('is_active')]);

        return back()->with('success', 'Delivery zone updated.');
    }

    public function destroy(DeliveryZone $deliveryZone)
    {
        $deliveryZone->delete();

        return back()->with('success', 'Delivery zone removed.');
    }

    protected function validated(Request $request): array
    {
        $data = $request->validate([
            'name' => 'required|string|max:120',
            'states' => 'nullable|string|max:500',
            'fee' => 'required|numeric|min:0',
            'estimated_delivery' => 'nullable|string|max:120',
            'sort_order' => 'nullable|integer|min:0',
        ]);
        $data['fee'] = Money::toKobo($data['fee']);

        return $data;
    }
}

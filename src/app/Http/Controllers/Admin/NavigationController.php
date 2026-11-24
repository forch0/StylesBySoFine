<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\NavigationItem;
use Illuminate\Http\Request;

class NavigationController extends Controller
{
    public function index()
    {
        return view('admin.navigation.index', [
            'items' => NavigationItem::orderBy('sort_order')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $item = NavigationItem::create($this->validated($request) + ['is_active' => $request->boolean('is_active', true)]);

        AuditLog::record('nav.created', $item);

        return back()->with('success', 'Menu item added.');
    }

    public function update(Request $request, NavigationItem $navigation)
    {
        $navigation->update($this->validated($request) + ['is_active' => $request->boolean('is_active')]);

        AuditLog::record('nav.updated', $navigation);

        return back()->with('success', 'Menu item updated.');
    }

    public function destroy(NavigationItem $navigation)
    {
        AuditLog::record('nav.deleted', $navigation, ['label' => $navigation->label]);
        $navigation->delete();

        return back()->with('success', 'Menu item removed.');
    }

    protected function validated(Request $request): array
    {
        return $request->validate([
            'label' => 'required|string|max:80',
            'url' => 'required|string|max:300',
            'sort_order' => 'nullable|integer|min:0',
        ]);
    }
}

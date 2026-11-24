<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CollectionController extends Controller
{
    public function index()
    {
        return view('admin.collections.index', [
            'collections' => Collection::withCount('products')->orderBy('sort_order')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $collection = new Collection($data);
        $collection->is_active = $request->boolean('is_active', true);
        if ($request->hasFile('image')) {
            $collection->image = $request->file('image')->store('collections', 'public');
        }
        $collection->save();

        AuditLog::record('collection.created', $collection);

        return back()->with('success', 'Collection created.');
    }

    public function update(Request $request, Collection $collection)
    {
        $collection->fill($this->validated($request));
        $collection->is_active = $request->boolean('is_active');
        if ($request->hasFile('image')) {
            if ($collection->image) {
                Storage::disk('public')->delete($collection->image);
            }
            $collection->image = $request->file('image')->store('collections', 'public');
        }
        $collection->save();

        AuditLog::record('collection.updated', $collection);

        return back()->with('success', 'Collection updated.');
    }

    public function destroy(Collection $collection)
    {
        AuditLog::record('collection.deleted', $collection, ['name' => $collection->name]);
        $collection->delete();

        return back()->with('success', 'Collection deleted.');
    }

    protected function validated(Request $request): array
    {
        return $request->validate([
            'name' => 'required|string|max:120',
            'description' => 'nullable|string|max:2000',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
            'sort_order' => 'nullable|integer|min:0',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Page;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function index()
    {
        return view('admin.pages.index', ['pages' => Page::orderBy('title')->get()]);
    }

    public function create()
    {
        return view('admin.pages.form', ['page' => new Page()]);
    }

    public function store(Request $request)
    {
        $page = Page::create($this->validated($request) + ['is_published' => $request->boolean('is_published', true)]);

        AuditLog::record('page.created', $page);

        return redirect()->route('admin.pages.index')->with('success', 'Page created.');
    }

    public function edit(Page $page)
    {
        return view('admin.pages.form', compact('page'));
    }

    public function update(Request $request, Page $page)
    {
        $page->update($this->validated($request, $page->id) + ['is_published' => $request->boolean('is_published')]);

        AuditLog::record('page.updated', $page);

        return back()->with('success', 'Page saved.');
    }

    public function destroy(Page $page)
    {
        AuditLog::record('page.deleted', $page, ['title' => $page->title]);
        $page->delete();

        return back()->with('success', 'Page deleted.');
    }

    protected function validated(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'title' => 'required|string|max:200',
            'slug' => 'nullable|string|max:200|unique:pages,slug'.($ignoreId ? ','.$ignoreId : ''),
            'content' => 'nullable|string',
            'meta_title' => 'nullable|string|max:200',
            'meta_description' => 'nullable|string|max:300',
        ]);
    }
}

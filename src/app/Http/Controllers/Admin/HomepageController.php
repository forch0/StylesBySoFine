<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\HomepagePreset;
use App\Models\HomepageSection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomepageController extends Controller
{
    public function index()
    {
        return view('admin.homepage.index', [
            'presets' => HomepagePreset::with('sections')->orderBy('id')->get(),
        ]);
    }

    public function activate(Request $request, HomepagePreset $preset)
    {
        DB::transaction(function () use ($preset) {
            HomepagePreset::where('is_active', true)->update(['is_active' => false]);
            $preset->update(['is_active' => true]);
        });

        AuditLog::record('homepage.activated', $preset);

        return back()->with('success', "\"{$preset->name}\" is now live on the homepage.");
    }

    public function sections(HomepagePreset $preset)
    {
        $preset->load('sections');

        return view('admin.homepage.sections', compact('preset'));
    }

    public function updateSection(Request $request, HomepageSection $section)
    {
        $data = $request->validate([
            'is_enabled' => 'nullable|boolean',
            'content' => 'nullable|array',
            'content.*' => 'nullable',
            'slide_images' => 'nullable|array',
            'slide_images.*' => 'image|mimes:jpg,jpeg,png,webp,avif|max:4096',
        ]);

        $content = array_merge($section->content ?? [], $data['content'] ?? []);

        // Hero slide image uploads override the URL/path field per index
        if ($section->key === 'hero' && $request->hasFile('slide_images')) {
            $slides = $content['slides'] ?? [];
            foreach ($request->file('slide_images') as $i => $file) {
                if ($file && isset($slides[$i])) {
                    $slides[$i]['image'] = $file->store('homepage', 'public');
                }
            }
            $content['slides'] = array_values($slides);
        }
        $section->update([
            'is_enabled' => $request->boolean('is_enabled'),
            'content' => $content,
        ]);

        AuditLog::record('homepage.section_updated', $section);

        return back()->with('success', 'Section saved.');
    }

    public function reorder(Request $request, HomepagePreset $preset)
    {
        $data = $request->validate([
            'order' => 'required|array',
            'order.*' => 'integer|exists:homepage_sections,id',
        ]);

        foreach ($data['order'] as $position => $sectionId) {
            $preset->sections()->where('id', $sectionId)->update(['sort_order' => $position]);
        }

        return back()->with('success', 'Section order updated.');
    }
}

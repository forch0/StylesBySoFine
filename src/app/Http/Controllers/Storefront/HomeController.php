<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Collection;
use App\Models\HomepagePreset;
use App\Models\Product;
use App\Models\Review;

class HomeController extends Controller
{
    public function index()
    {
        $preset = \Illuminate\Support\Facades\Cache::remember(
            'home.preset', 3600,
            fn () => HomepagePreset::active()->load('sections')
        );

        $sections = $preset->sections->where('is_enabled', true)->sortBy('sort_order');

        $data = [
            'preset' => $preset,
            'sections' => $sections,
            'categories' => Category::active()->whereNull('parent_id')->orderBy('sort_order')->take(6)->get(),
            'newArrivals' => Product::active()->with('media')->latest()->take(8)->get(),
            'bestsellers' => Product::active()->where('is_bestseller', true)->with('media')->take(8)->get(),
            'featured' => Product::active()->where('is_featured', true)->with('media')->take(4)->get(),
            'collections' => Collection::active()->orderBy('sort_order')->take(3)->get(),
            'reviews' => Review::approved()->latest()->take(6)->get(),
        ];

        return view('storefront.home', $data);
    }
}

<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        return $this->listing($request);
    }

    public function category(Request $request, Category $category)
    {
        abort_unless($category->is_active, 404);

        return $this->listing($request, $category);
    }

    protected function listing(Request $request, ?Category $category = null)
    {
        $query = Product::active()->with('media', 'category', 'variants');

        if ($category) {
            $categoryIds = $category->children()->pluck('id')->push($category->id);
            $query->whereIn('category_id', $categoryIds);
        }

        if ($search = $request->string('q')->trim()->toString()) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('short_description', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($size = $request->string('size')->toString()) {
            $query->whereHas('variants', fn ($q) => $q->where('options->Size', $size)->where('is_active', true));
        }

        if ($colour = $request->string('colour')->toString()) {
            $query->whereHas('variants', fn ($q) => $q->where('options->Colour', $colour)->where('is_active', true));
        }

        if ($maxPrice = $request->integer('max_price')) {
            $query->where('price', '<=', $maxPrice * 100);
        }

        if ($request->boolean('in_stock')) {
            $query->whereHas('variants', fn ($q) => $q->where('is_active', true)->where('stock_quantity', '>', 0));
        }

        match ($request->string('sort')->toString()) {
            'price_asc' => $query->orderBy('price'),
            'price_desc' => $query->orderByDesc('price'),
            'popular' => $query->orderByDesc('is_bestseller')->latest(),
            default => $query->latest(),
        };

        $products = $query->paginate(12)->withQueryString();

        // Available filter values from active variants
        $sizes = ['XS', 'S', 'M', 'L', 'XL', 'XXL'];
        $colours = \App\Models\ProductVariant::where('is_active', true)
            ->whereHas('product', fn ($q) => $q->active())
            ->get()
            ->map(fn ($v) => $v->options['Colour'] ?? null)
            ->filter()->unique()->sort()->values();

        return view('storefront.shop', [
            'products' => $products,
            'category' => $category,
            'categories' => Category::active()->whereNull('parent_id')->orderBy('sort_order')->get(),
            'sizes' => $sizes,
            'colours' => $colours,
            'search' => $search ?? '',
        ]);
    }
}

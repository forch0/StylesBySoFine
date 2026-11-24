<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Product;

class ProductController extends Controller
{
    public function show(Product $product)
    {
        abort_unless($product->status === 'active', 404);

        // Anonymous aggregate view count — no personal data recorded.
        Product::where('id', $product->id)->increment('views_count');

        $product->load('media', 'options.values', 'variants', 'category', 'reviews');

        $related = Product::active()
            ->where('id', '!=', $product->id)
            ->when($product->category_id, fn ($q) => $q->where('category_id', $product->category_id))
            ->with('media')
            ->take(4)
            ->get();

        return view('storefront.product', compact('product', 'related'));
    }
}

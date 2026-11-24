<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Wishlist;

class WishlistController extends Controller
{
    protected function currentWishlist(): Wishlist
    {
        return Wishlist::firstOrCreate(['session_id' => session()->getId()]);
    }

    public function index()
    {
        $wishlist = $this->currentWishlist()->load('products.media');

        return view('storefront.wishlist', compact('wishlist'));
    }

    public function toggle(Product $product)
    {
        $wishlist = $this->currentWishlist();

        $existing = $wishlist->items()->where('product_id', $product->id)->first();

        if ($existing) {
            $existing->delete();
            $added = false;
        } else {
            $wishlist->items()->create(['product_id' => $product->id]);
            $added = true;
        }

        if (request()->expectsJson()) {
            return response()->json(['added' => $added]);
        }

        return back()->with('success', $added ? 'Saved to your wishlist.' : 'Removed from your wishlist.');
    }
}

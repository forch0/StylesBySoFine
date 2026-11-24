<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(protected CartService $cart) {}

    public function index()
    {
        $cart = $this->cart->currentCart()->load('items.product.media', 'items.variant');

        return view('storefront.cart', compact('cart'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_uuid' => 'required|exists:products,uuid',
            'variant_uuid' => 'nullable|exists:product_variants,uuid',
            'quantity' => 'nullable|integer|min:1|max:20',
        ]);

        $product = Product::active()->where('uuid', $request->product_uuid)->firstOrFail();
        $variant = $request->variant_uuid
            ? ProductVariant::where('uuid', $request->variant_uuid)->where('product_id', $product->id)->firstOrFail()
            : null;

        try {
            $this->cart->add($product, $variant, (int) $request->input('quantity', 1));
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('cart.index')->with('success', 'Added to bag.');
    }

    public function update(Request $request, CartItem $item)
    {
        $this->authorizeItem($item);

        try {
            $this->cart->updateQuantity($item, (int) $request->input('quantity', 1));
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Bag updated.');
    }

    public function remove(CartItem $item)
    {
        $this->authorizeItem($item);
        $this->cart->remove($item);

        return back()->with('success', 'Item removed.');
    }

    protected function authorizeItem(CartItem $item): void
    {
        abort_unless($item->cart->session_id === session()->getId(), 403);
    }
}

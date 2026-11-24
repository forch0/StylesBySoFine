<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Cart;
use App\Models\CouponRedemption;
use App\Models\Order;
use App\Models\Product;
use App\Models\Wishlist;
use Database\Seeders\DemoCatalogSeeder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class DemoController extends Controller
{
    /**
     * Restore the demo catalogue (sample products, media, variants, review).
     * Never touches categories, pages, settings, navigation, zones or coupons.
     */
    public function seed()
    {
        Artisan::call('db:seed', ['--class' => DemoCatalogSeeder::class, '--force' => true]);

        AuditLog::record('demo.seeded');

        return back()->with('success', 'Demo catalogue restored — 8 sample products with UK sizes.');
    }

    /**
     * Remove demo products (by slug) and leftover carts/wishlists.
     * Standard content — categories, sizes structure, pages, settings — is untouched.
     * Orders are only deleted when explicitly confirmed, since they are financial records.
     */
    public function wipe(Request $request)
    {
        $slugs = DemoCatalogSeeder::demoSlugs();
        $count = Product::whereIn('slug', $slugs)->count();

        Product::whereIn('slug', $slugs)->delete(); // cascades: media, options, variants, cart/wishlist items, reviews
        Cart::query()->delete();
        Wishlist::query()->delete();

        $cleared = ['products' => $count];

        if ($request->boolean('also_orders')) {
            $cleared['orders'] = Order::count();
            CouponRedemption::query()->delete();
            Order::query()->delete(); // cascades: items, payments, status history
        }

        AuditLog::record('demo.wiped', null, $cleared);

        $msg = "Removed {$count} demo products and cleared carts/wishlists.";
        if (isset($cleared['orders'])) {
            $msg .= " Deleted {$cleared['orders']} orders.";
        }

        return back()->with('success', $msg.' Categories, pages, settings and other standard content were kept.');
    }
}

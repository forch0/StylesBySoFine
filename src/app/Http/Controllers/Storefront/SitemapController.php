<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Page;
use App\Models\Product;
use Illuminate\Support\Facades\Cache;

class SitemapController extends Controller
{
    public function index()
    {
        $urls = Cache::remember('sitemap.urls', now()->addHour(), function () {
            $urls = [
                ['loc' => route('home'), 'priority' => '1.0', 'changefreq' => 'daily'],
                ['loc' => route('shop'), 'priority' => '0.9', 'changefreq' => 'daily'],
                ['loc' => route('contact'), 'priority' => '0.4', 'changefreq' => 'monthly'],
            ];

            foreach (Category::where('is_active', true)->get() as $category) {
                $urls[] = [
                    'loc' => route('shop.category', $category->slug),
                    'priority' => '0.8',
                    'changefreq' => 'weekly',
                    'lastmod' => $category->updated_at,
                ];
            }

            foreach (Product::active()->get(['slug', 'updated_at']) as $product) {
                $urls[] = [
                    'loc' => route('product.show', $product->slug),
                    'priority' => '0.8',
                    'changefreq' => 'weekly',
                    'lastmod' => $product->updated_at,
                ];
            }

            foreach (Page::where('is_published', true)->get(['slug', 'updated_at']) as $page) {
                $urls[] = [
                    'loc' => url('/pages/'.$page->slug),
                    'priority' => '0.5',
                    'changefreq' => 'monthly',
                    'lastmod' => $page->updated_at,
                ];
            }

            return $urls;
        });

        return response()
            ->view('storefront.sitemap', compact('urls'))
            ->header('Content-Type', 'application/xml; charset=utf-8');
    }

    public function robots()
    {
        $lines = [
            'User-agent: *',
            'Disallow: /admin',
            'Disallow: /cart',
            'Disallow: /checkout',
            'Disallow: /wishlist',
            'Disallow: /track',
            'Disallow: /order/',
            'Disallow: /payment/',
            'Disallow: /newsletter',
            'Allow: /',
            '',
            'Sitemap: '.url('/sitemap.xml'),
        ];

        return response(implode("\n", $lines)."\n", 200, ['Content-Type' => 'text/plain']);
    }
}

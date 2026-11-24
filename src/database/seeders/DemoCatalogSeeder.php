<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Collection;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Demo-only catalogue: sample products, media, variants and a sample review.
 * Never touches standard content (categories are only created if missing,
 * and nothing here updates settings, pages, navigation, zones or coupons).
 */
class DemoCatalogSeeder extends Seeder
{
    /** Demo products — slugs are also used to identify demo rows on wipe. */
    public static array $products = [
        ['name' => 'The Adaugo Wrap Dress', 'cat' => 'Dresses', 'price' => 48500, 'compare' => 56000, 'featured' => true, 'new' => true, 'video' => false],
        ['name' => 'Chidinma Satin Slip Dress', 'cat' => 'Dresses', 'price' => 42000, 'compare' => null, 'featured' => true, 'new' => true, 'video' => true],
        ['name' => 'Efe Pleated Midi Skirt', 'cat' => 'Skirts', 'price' => 32000, 'compare' => null, 'featured' => false, 'new' => true, 'video' => false],
        ['name' => 'Ngozi Linen Co-ord Set', 'cat' => 'Sets', 'price' => 65000, 'compare' => null, 'featured' => true, 'new' => false, 'video' => false],
        ['name' => 'Temi Wide-Leg Trousers', 'cat' => 'Corporate Casuals', 'price' => 38500, 'compare' => 44000, 'featured' => false, 'new' => false, 'video' => false],
        ['name' => 'Amara Fitted Rib Top', 'cat' => 'Tops', 'price' => 18500, 'compare' => null, 'featured' => false, 'new' => true, 'video' => false],
        ['name' => 'Kiki Off-Shoulder Top', 'cat' => 'Tops', 'price' => 22000, 'compare' => null, 'featured' => false, 'new' => false, 'video' => false],
        ['name' => 'Zara Evening Maxi Dress', 'cat' => 'Dresses', 'price' => 78000, 'compare' => null, 'featured' => true, 'new' => false, 'video' => true],
    ];

    public static function demoSlugs(): array
    {
        return collect(static::$products)->map(fn ($p) => Str::slug($p['name']))->all();
    }

    public function run(): void
    {
        $categories = collect([
            'Dresses' => 'Stylish and feminine dresses for everyday wear, work, outings, dinners and special occasions',
            'Corporate Casuals' => 'Smart, stylish and sophisticated pieces for the modern working woman',
            'Tops' => 'Stylish tops that pair beautifully with skirts, trousers, jeans and wardrobe staples',
            'Sets' => 'Co-ords and matching sets',
            'Skirts' => 'Feminine and versatile skirts for both casual and polished looks',
        ])->map(fn ($desc, $name) => Category::updateOrCreate(['slug' => Str::slug($name)], [
            'name' => $name, 'description' => $desc, 'is_active' => true,
        ]));

        $collection = Collection::updateOrCreate(['slug' => 'the-signature-edit'], [
            'name' => 'The Signature Edit',
            'description' => 'The pieces our customers reach for again and again.',
            'is_active' => true,
        ]);

        $sizes = ['UK 6', 'UK 8', 'UK 10', 'UK 12', 'UK 14', 'UK 16', 'UK 18'];
        $colours = ['Wine', 'Black', 'Ivory'];

        foreach (static::$products as $i => $row) {
            $product = Product::updateOrCreate(['slug' => Str::slug($row['name'])], [
                'name' => $row['name'],
                'category_id' => $categories[$row['cat']]->id,
                'short_description' => 'A signature piece from the new collection — flattering, comfortable, and made to last.',
                'description' => "Designed in Lagos with love.\n\nCut from quality fabric with careful attention to fit and finish. Pair it with your favourite accessories — this one does the work for you.",
                'price' => $row['price'] * 100,
                'compare_at_price' => $row['compare'] ? $row['compare'] * 100 : null,
                'sku' => 'SBF-'.str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                'status' => 'active',
                'is_featured' => $row['featured'],
                'is_bestseller' => $i % 3 === 0,
                'is_new_arrival' => $row['new'],
                'material' => 'Premium blend',
                'care_instructions' => 'Hand wash cold. Hang to dry.',
            ]);

            // Media: 2 picsum images + optional sample video
            if ($product->media()->count() === 0) {
                $product->media()->create([
                    'type' => 'image', 'source' => 'url',
                    'path' => "https://picsum.photos/seed/sbf-product-{$i}a/800/1067",
                    'alt' => $product->name, 'sort_order' => 0, 'is_primary' => true,
                ]);
                $product->media()->create([
                    'type' => 'image', 'source' => 'url',
                    'path' => "https://picsum.photos/seed/sbf-product-{$i}b/800/1067",
                    'alt' => $product->name.' — detail', 'sort_order' => 1,
                ]);
                if ($row['video']) {
                    $product->media()->create([
                        'type' => 'video', 'source' => 'url',
                        'path' => 'https://storage.googleapis.com/gtv-videos-bucket/sample/ForBiggerEscapes.mp4',
                        'alt' => $product->name.' — video', 'sort_order' => 2,
                    ]);
                }
            }

            // Variants: Size × Colour
            if ($product->variants()->count() === 0) {
                foreach ($sizes as $si => $size) {
                    foreach ($colours as $ci => $colour) {
                        $product->variants()->create([
                            'options' => ['Size' => $size, 'Colour' => $colour],
                            'sku' => $product->sku.'-'.str_replace(' ', '', $size).'-'.strtoupper(substr($colour, 0, 3)),
                            'stock_quantity' => max(0, 8 - $si - $ci + ($i % 4)),
                            'low_stock_threshold' => 3,
                            'is_active' => true,
                        ]);
                    }
                }
            }

            $collection->products()->syncWithoutDetaching([$product->id]);
        }

        // Category images
        foreach ($categories as $i => $cat) {
            if (! $cat->image) {
                $cat->update(['image' => "https://picsum.photos/seed/sbf-cat-{$i}/640/800"]);
            }
        }

        Review::updateOrCreate(['product_id' => Product::first()?->id, 'customer_name' => 'Adaeze O.'], [
            'rating' => 5, 'body' => 'The fit is perfect and the fabric feels expensive. Got so many compliments!',
            'is_approved' => true,
        ]);
    }
}

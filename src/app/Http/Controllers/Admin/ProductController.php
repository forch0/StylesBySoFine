<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Category;
use App\Models\Collection;
use App\Models\Product;
use App\Support\Money;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::with('category', 'media', 'variants')
            ->when($request->q, fn ($q) => $q->where('name', 'like', '%'.$request->q.'%'))
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->latest()->paginate(20)->withQueryString();

        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        return view('admin.products.form', [
            'product' => new Product(),
            'categories' => Category::orderBy('name')->get(),
            'collections' => Collection::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $product = DB::transaction(fn () => $this->saveProduct($request, new Product()));

        AuditLog::record('product.created', $product);

        return redirect()->route('admin.products.edit', $product)->with('success', 'Product created.');
    }

    public function edit(Product $product)
    {
        $product->load('media', 'options.values', 'variants', 'collections');

        return view('admin.products.form', [
            'product' => $product,
            'categories' => Category::orderBy('name')->get(),
            'collections' => Collection::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Product $product)
    {
        DB::transaction(fn () => $this->saveProduct($request, $product));

        AuditLog::record('product.updated', $product);

        return back()->with('success', 'Product saved.');
    }

    public function destroy(Product $product)
    {
        AuditLog::record('product.deleted', $product, ['name' => $product->name]);
        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Product deleted.');
    }

    protected function saveProduct(Request $request, Product $product): Product
    {
        $data = $request->validate([
            'name' => 'required|string|max:200',
            'category_id' => 'nullable|exists:categories,id',
            'short_description' => 'nullable|string|max:500',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'compare_at_price' => 'nullable|numeric|min:0',
            'sku' => 'nullable|string|max:80',
            'status' => 'required|in:draft,active,archived',
            'is_featured' => 'nullable|boolean',
            'is_bestseller' => 'nullable|boolean',
            'is_new_arrival' => 'nullable|boolean',
            'allow_preorder' => 'nullable|boolean',
            'preorder_note' => 'nullable|string|max:200',
            'meta_title' => 'nullable|string|max:200',
            'meta_description' => 'nullable|string|max:300',
            'care_instructions' => 'nullable|string|max:2000',
            'material' => 'nullable|string|max:200',
            'collections' => 'nullable|array',
            'collections.*' => 'exists:collections,id',
            // variants
            'variants' => 'required|array|min:1',
            'variants.*.id' => 'nullable|integer',
            'variants.*.options' => 'nullable|array',
            'variants.*.sku' => 'nullable|string|max:80',
            'variants.*.price' => 'nullable|numeric|min:0',
            'variants.*.stock_quantity' => 'required|integer',
            'variants.*.low_stock_threshold' => 'nullable|integer|min:0',
            'variants.*.is_active' => 'nullable|boolean',
            // media
            'media' => 'nullable|array',
            'media.*.id' => 'nullable|integer',
            'media.*.type' => 'required|in:image,video',
            'media.*.source' => 'required|in:upload,url',
            'media.*.url' => 'nullable|string|max:1000',
            'media.*.file' => 'nullable|file|mimetypes:image/jpeg,image/png,image/webp,video/mp4,video/webm|max:51200',
            'media.*.alt' => 'nullable|string|max:200',
            'media.*.is_primary' => 'nullable|boolean',
            'media.*.delete' => 'nullable|boolean',
        ]);

        // Slug is auto-generated from the name on create and stays stable
        // afterwards — renaming a product must not silently change its URL.
        $slug = $product->exists
            ? $product->slug
            : $this->uniqueSlug($data['slug'] ?? Str::slug($data['name']));

        $product->fill([
            'name' => $data['name'],
            'slug' => $slug,
            'category_id' => $data['category_id'] ?? null,
            'short_description' => $data['short_description'] ?? null,
            'description' => $data['description'] ?? null,
            'price' => Money::toKobo($data['price']),
            'compare_at_price' => isset($data['compare_at_price']) && $data['compare_at_price'] !== ''
                ? Money::toKobo($data['compare_at_price']) : null,
            'sku' => $data['sku'] ?? null,
            'status' => $data['status'],
            'is_featured' => $request->boolean('is_featured'),
            'is_bestseller' => $request->boolean('is_bestseller'),
            'is_new_arrival' => $request->boolean('is_new_arrival'),
            'allow_preorder' => $request->boolean('allow_preorder'),
            'preorder_note' => $data['preorder_note'] ?? null,
            'meta_title' => $data['meta_title'] ?? null,
            'meta_description' => $data['meta_description'] ?? null,
            'care_instructions' => $data['care_instructions'] ?? null,
            'material' => $data['material'] ?? null,
        ]);
        $product->save();

        $product->collections()->sync($data['collections'] ?? []);

        $this->saveVariants($product, $data['variants']);
        $this->saveMedia($request, $product, $data['media'] ?? []);

        return $product;
    }

    protected function saveVariants(Product $product, array $variants): void
    {
        $kept = [];

        foreach ($variants as $i => $row) {
            // Clean empty option keys
            $options = collect($row['options'] ?? [])->filter()->toArray();

            $attrs = [
                'options' => $options,
                'sku' => $row['sku'] ?? null,
                'price' => isset($row['price']) && $row['price'] !== '' ? Money::toKobo($row['price']) : null,
                'stock_quantity' => (int) $row['stock_quantity'],
                'low_stock_threshold' => $row['low_stock_threshold'] ?? null,
                'is_active' => ! empty($row['is_active']),
            ];

            if (! empty($row['id']) && $variant = $product->variants()->find($row['id'])) {
                $variant->update($attrs);
                $kept[] = $variant->id;
            } else {
                $kept[] = $product->variants()->create($attrs)->id;
            }
        }

        // Delete removed variants that aren't referenced by order items
        $product->variants()->whereNotIn('id', $kept)
            ->whereDoesntHave('movements')
            ->delete();
    }

    protected function saveMedia(Request $request, Product $product, array $media): void
    {
        $kept = [];

        foreach ($media as $i => $row) {
            $existing = ! empty($row['id']) ? $product->media()->find($row['id']) : null;

            if ($existing && ! empty($row['delete'])) {
                if ($existing->source === 'upload') {
                    Storage::disk('public')->delete($existing->path);
                }
                $existing->delete();
                continue;
            }

            $file = $request->file("media.{$i}.file");
            $path = $existing?->path;

            if ($file) {
                if ($existing && $existing->source === 'upload') {
                    Storage::disk('public')->delete($existing->path);
                }
                $path = $file->store('products', 'public');
            } elseif ($row['source'] === 'url' && ! empty($row['url'])) {
                $path = $row['url'];
            }

            if (! $path) {
                continue; // nothing to save for this row
            }

            $attrs = [
                'type' => $row['type'],
                'source' => Str::startsWith($path, ['http://', 'https://']) ? 'url' : 'upload',
                'path' => $path,
                'alt' => $row['alt'] ?? $product->name,
                'sort_order' => $i,
                'is_primary' => ! empty($row['is_primary']),
            ];

            if ($existing) {
                $existing->update($attrs);
                $kept[] = $existing->id;
            } else {
                $kept[] = $product->media()->create($attrs)->id;
            }
        }

        // Ensure exactly one primary
        $product->media()->whereNotIn('id', $kept)->delete();
        $mediaRows = $product->media()->orderBy('sort_order')->get();
        if ($mediaRows->isNotEmpty() && $mediaRows->where('is_primary', true)->count() !== 1) {
            $mediaRows->each(fn ($m) => $m->update(['is_primary' => false]));
            $mediaRows->first()->update(['is_primary' => true]);
        }
    }

    protected function uniqueSlug(string $slug, ?int $ignoreId = null): string
    {
        $base = $slug;
        $i = 2;
        while (Product::where('slug', $slug)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }
}

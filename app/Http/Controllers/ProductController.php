<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\ProductSerial;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category');

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%");
        }

        if ($request->has('category') && $request->category !== 'all') {
            $cat = Category::find($request->category);
            if ($cat) {
                $query->whereIn('category_id', $cat->getDescendantIds());
            }
        }

        if ($request->has('stock_filter')) {
            if ($request->stock_filter === 'low') {
                $query->where('stock', '<', 5)->where('stock', '>', 0);
            } elseif ($request->stock_filter === 'out') {
                $query->where('stock', 0);
            }
        }

        $stats = [
            'total_count' => (clone $query)->count(),
            'total_stock_value' => (clone $query)->get()->sum(fn($p) => $p->stock * $p->price),
            'low_stock_count' => (clone $query)->where('stock', '<', 5)->where('stock', '>', 0)->count(),
            'out_of_stock_count' => (clone $query)->where('stock', 0)->count(),
        ];

        $products = $query->latest()->paginate(10);
        $categories = Category::getNestedCategories();

        return view('products.index', compact('products', 'categories', 'stats'));
    }

    public function create()
    {
        $categories = Category::getNestedCategories();
        return view('products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'sku' => 'required|string|unique:products,sku',
            'barcode' => 'nullable|string|max:255',
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'brand' => 'required|string|max:255',
            'buying_price' => 'required|numeric|min:0',
            'price' => 'required|numeric|min:0',
            'wholesale_price' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'warranty_months' => 'required|integer|min:0',
            'expire_date' => 'nullable|date',
            'description' => 'nullable|string',
            'specifications' => 'nullable|array',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_visible' => 'nullable|boolean',
        ]);

        $data = $request->except('image');
        $data['slug'] = Str::slug($request->name) . '-' . Str::random(5);
        $data['is_visible'] = $request->has('is_visible');

        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('images/products'), $imageName);
            $data['image_path'] = 'images/products/' . $imageName;
        } else {
            $data['image_path'] = 'images/products/default.jpg'; // default placeholder
        }

        $product = Product::create($data);

        // Generate serials automatically for the initial stock
        if ($product->stock > 0) {
            for ($i = 1; $i <= $product->stock; $i++) {
                ProductSerial::create([
                    'product_id' => $product->id,
                    'serial_number' => $product->sku . '-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                    'status' => 'in_stock',
                ]);
            }
        }

        return redirect()->route('products.index')->with('success', "Product {$product->name} created and inventory initialized.");
    }

    public function edit(Product $product)
    {
        $categories = Category::getNestedCategories();
        return view('products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'sku' => 'required|string|unique:products,sku,' . $product->id,
            'barcode' => 'nullable|string|max:255',
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'brand' => 'required|string|max:255',
            'buying_price' => 'required|numeric|min:0',
            'price' => 'required|numeric|min:0',
            'wholesale_price' => 'nullable|numeric|min:0',
            'warranty_months' => 'required|integer|min:0',
            'expire_date' => 'nullable|date',
            'description' => 'nullable|string',
            'specifications' => 'nullable|array',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_visible' => 'nullable|boolean',
        ]);

        $data = $request->except('image');
        $data['is_visible'] = $request->has('is_visible');
        
        if ($request->hasFile('image')) {
            // Delete old image if it's not the default
            if ($product->image_path && $product->image_path !== 'images/products/default.jpg' && file_exists(public_path($product->image_path))) {
                @unlink(public_path($product->image_path));
            }

            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('images/products'), $imageName);
            $data['image_path'] = 'images/products/' . $imageName;
        }

        $product->update($data);

        return redirect()->route('products.index')->with('success', "Product {$product->name} updated successfully.");
    }

    public function destroy(Product $product)
    {
        // Delete old image
        if ($product->image_path && $product->image_path !== 'images/products/default.jpg' && file_exists(public_path($product->image_path))) {
            @unlink(public_path($product->image_path));
        }

        $product->delete();
        return redirect()->route('products.index')->with('success', "Product deleted successfully.");
    }

    public function toggleVisibility(Product $product)
    {
        $product->is_visible = !$product->is_visible;
        $product->save();

        return redirect()->back()->with('success', "Product '{$product->name}' visibility status updated successfully.");
    }

    public function serials(Request $request)
    {
        $query = ProductSerial::with('product');

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where('serial_number', 'like', "%{$search}%")
                  ->orWhereHas('product', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%");
                  });
        }

        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $stats = [
            'total_count' => (clone $query)->count(),
            'in_stock_count' => (clone $query)->where('status', 'in_stock')->count(),
            'sold_count' => (clone $query)->where('status', 'sold')->count(),
            'returned_count' => (clone $query)->where('status', 'returned')->count(),
        ];

        $serials = $query->latest()->paginate(15);
        return view('products.serials', compact('serials', 'stats'));
    }
}

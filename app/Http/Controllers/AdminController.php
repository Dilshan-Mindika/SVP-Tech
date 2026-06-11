<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    /**
     * Display the Admin Dashboard.
     */
    public function dashboard()
    {
        $totalSales = Order::where('status', 'completed')->sum('total');
        $pendingOrders = Order::where('status', 'pending')->count();
        $totalUsers = User::count();
        $outOfStockCount = Product::where('stock', 0)->count();
        
        $recentOrders = Order::orderBy('created_at', 'desc')->take(5)->get();
        $lowStockProducts = Product::where('stock', '<', 5)->orderBy('stock', 'asc')->take(5)->get();
        
        // Count products per category
        $categoriesCount = Category::withCount('products')->get();

        return view('admin.dashboard', compact(
            'totalSales',
            'pendingOrders',
            'totalUsers',
            'outOfStockCount',
            'recentOrders',
            'lowStockProducts',
            'categoriesCount'
        ));
    }

    /**
     * List all products for CRUD.
     */
    public function products()
    {
        $products = Product::with('category')->orderBy('created_at', 'desc')->paginate(10);
        return view('admin.products.index', compact('products'));
    }

    /**
     * Show form to create a product.
     */
    public function createProduct()
    {
        $categories = Category::getNestedCategories();
        return view('admin.products.create', compact('categories'));
    }

    /**
     * Store a newly created product.
     */
    public function storeProduct(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:products,name',
            'brand' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'description' => 'required|string',
            'specs_keys' => 'nullable|array',
            'specs_values' => 'nullable|array',
            'is_featured' => 'boolean',
            'is_visible' => 'boolean',
        ]);

        // Process specifications
        $specifications = [];
        if ($request->has('specs_keys') && $request->has('specs_values')) {
            $keys = $request->specs_keys;
            $values = $request->specs_values;
            foreach ($keys as $index => $key) {
                if (!empty($key) && isset($values[$index])) {
                    $specifications[$key] = $values[$index];
                }
            }
        }

        // Handle Image Upload
        $imagePath = null;
        if ($request->hasFile('image')) {
            // Store publically in storage/app/public/products
            $imagePath = $request->file('image')->store('products', 'public');
        }

        Product::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'brand' => $request->brand,
            'category_id' => $request->category_id,
            'price' => $request->price,
            'stock' => $request->stock,
            'image_path' => $imagePath,
            'description' => $request->description,
            'specifications' => $specifications,
            'is_featured' => $request->has('is_featured') ? true : false,
            'is_visible' => $request->has('is_visible') ? true : false,
        ]);

        return redirect()->route('admin.products')->with('success', 'Product created successfully!');
    }

    /**
     * Show form to edit a product.
     */
    public function editProduct($id)
    {
        $product = Product::findOrFail($id);
        $categories = Category::getNestedCategories();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    /**
     * Update product details.
     */
    public function updateProduct(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:products,name,' . $product->id,
            'brand' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'description' => 'required|string',
            'specs_keys' => 'nullable|array',
            'specs_values' => 'nullable|array',
            'is_featured' => 'boolean',
            'is_visible' => 'boolean',
        ]);

        // Process specifications
        $specifications = [];
        if ($request->has('specs_keys') && $request->has('specs_values')) {
            $keys = $request->specs_keys;
            $values = $request->specs_values;
            foreach ($keys as $index => $key) {
                if (!empty($key) && isset($values[$index])) {
                    $specifications[$key] = $values[$index];
                }
            }
        }

        // Handle Image Upload
        $imagePath = $product->image_path;
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
            $imagePath = $request->file('image')->store('products', 'public');
        }

        $product->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'brand' => $request->brand,
            'category_id' => $request->category_id,
            'price' => $request->price,
            'stock' => $request->stock,
            'image_path' => $imagePath,
            'description' => $request->description,
            'specifications' => $specifications,
            'is_featured' => $request->has('is_featured') ? true : false,
            'is_visible' => $request->has('is_visible') ? true : false,
        ]);

        return redirect()->route('admin.products')->with('success', 'Product updated successfully!');
    }

    /**
     * Delete a product.
     */
    public function destroyProduct($id)
    {
        $product = Product::findOrFail($id);
        
        // Delete image if exists
        if ($product->image_path && Storage::disk('public')->exists($product->image_path)) {
            Storage::disk('public')->delete($product->image_path);
        }

        $product->delete();

        return redirect()->route('admin.products')->with('success', 'Product deleted successfully!');
    }

    /**
     * Toggle visibility of a product on storefront.
     */
    public function toggleVisibility($id)
    {
        $product = Product::findOrFail($id);
        $product->is_visible = !$product->is_visible;
        $product->save();

        return redirect()->back()->with('success', "Product '{$product->name}' visibility status updated successfully.");
    }

    /**
     * List all orders.
     */
    public function orders()
    {
        $orders = Order::orderBy('created_at', 'desc')->paginate(10);
        return view('admin.orders.index', compact('orders'));
    }

    /**
     * View details of an order.
     */
    public function viewOrder($id)
    {
        $order = Order::with('items.product')->findOrFail($id);
        return view('admin.orders.show', compact('order'));
    }

    /**
     * Update status of an order.
     */
    public function updateOrderStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        
        $request->validate([
            'status' => 'required|string|in:pending,processing,shipped,completed,cancelled',
        ]);

        $order->update([
            'status' => $request->status,
        ]);

        return redirect()->back()->with('success', 'Order status updated successfully!');
    }
}

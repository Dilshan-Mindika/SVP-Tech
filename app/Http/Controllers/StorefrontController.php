<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class StorefrontController extends Controller
{
    /**
     * Display the storefront landing page.
     */
    public function home()
    {
        $featuredProducts = Product::where('is_visible', true)
            ->where('is_featured', true)
            ->with('category')
            ->take(6)
            ->get();

        $categories = Category::whereNull('parent_id')->get();

        return view('home', compact('featuredProducts', 'categories'));
    }

    /**
     * Display the storefront product catalog with filtering and sorting.
     */
    public function catalog(Request $request)
    {
        $query = Product::where('is_visible', true)->with('category');

        // Apply Search Filter
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Apply Category Filter (by slug)
        if ($request->has('category') && !empty($request->category)) {
            $categorySlug = $request->category;
            $category = Category::where('slug', $categorySlug)->first();
            if ($category) {
                $query->whereIn('category_id', $category->getDescendantIds());
            }
        }

        // Apply Brand Filter
        if ($request->has('brand') && !empty($request->brand)) {
            $query->where('brand', $request->brand);
        }

        // Apply Price Filter Range
        if ($request->has('min_price') && is_numeric($request->min_price)) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->has('max_price') && is_numeric($request->max_price)) {
            $query->where('price', '<=', $request->max_price);
        }

        // Apply Sorting
        $sort = $request->input('sort', 'featured');
        if ($sort === 'price_asc') {
            $query->orderBy('price', 'asc');
        } elseif ($sort === 'price_desc') {
            $query->orderBy('price', 'desc');
        } elseif ($sort === 'newest') {
            $query->orderBy('created_at', 'desc');
        } else {
            // default: featured
            $query->orderBy('is_featured', 'desc')->orderBy('created_at', 'desc');
        }

        // Paginate products
        $products = $query->paginate(9)->withQueryString();
        
        $categories = Category::getNestedCategories();
        $brands = Product::where('is_visible', true)->distinct()->pluck('brand');

        return view('products.catalog', compact('products', 'categories', 'brands'));
    }

    /**
     * Display the storefront product details page.
     */
    public function show($slug)
    {
        $product = Product::where('slug', $slug)
            ->where('is_visible', true)
            ->with('category')
            ->firstOrFail();

        // Load related products in the same category
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_visible', true)
            ->take(4)
            ->get();

        return view('products.detail', compact('product', 'relatedProducts'));
    }
}

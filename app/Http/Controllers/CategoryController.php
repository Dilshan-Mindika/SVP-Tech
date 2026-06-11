<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    /**
     * Display a listing of categories.
     */
    public function index(Request $request)
    {
        $query = Category::with(['parent', 'children'])->withCount('products');

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
        }

        $stats = [
            'total_count' => Category::count(),
            'top_level_count' => Category::whereNull('parent_id')->count(),
            'subcategories_count' => Category::whereNotNull('parent_id')->count(),
            'orphaned_products_check' => \App\Models\Product::whereNotExists(function ($q) {
                $q->select(\DB::raw(1))
                  ->from('categories')
                  ->whereColumn('categories.id', 'products.category_id');
            })->count(),
        ];

        // Paginate flat table representation
        $flatCategories = (clone $query)->latest()->paginate(10);

        // Fetch hierarchical tree (Top-level categories with their children)
        $categoryTree = Category::whereNull('parent_id')
            ->with(['children' => function($q) {
                $q->with('children'); // Supports up to 3 levels visually
            }])
            ->withCount('products')
            ->get();

        return view('categories.index', compact('flatCategories', 'categoryTree', 'stats'));
    }

    /**
     * Show the form for creating a new category.
     */
    public function create(Request $request)
    {
        $categories = Category::getNestedCategories();
        $preselectedParentId = $request->query('parent_id');
        return view('categories.create', compact('categories', 'preselectedParentId'));
    }

    /**
     * Store a newly created category in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'parent_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:50',
        ]);

        $slug = Str::slug($request->name);
        
        // Ensure slug uniqueness
        $originalSlug = $slug;
        $counter = 1;
        while (Category::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        $category = Category::create([
            'name' => $request->name,
            'slug' => $slug,
            'parent_id' => $request->parent_id,
            'description' => $request->description,
            'icon' => $request->icon ?: 'fa-tag',
        ]);

        return redirect()->route('categories.index')->with('success', "Category '{$category->name}' created successfully.");
    }

    /**
     * Display the specified category profile and its products.
     */
    public function show(Category $category)
    {
        $category->load(['parent', 'children']);
        
        // Get all products in this category and its subcategories
        $allCategoryIds = $category->getDescendantIds();
        $products = \App\Models\Product::whereIn('category_id', $allCategoryIds)
            ->with('category')
            ->latest()
            ->paginate(10);

        return view('categories.show', compact('category', 'products'));
    }

    /**
     * Show the form for editing the specified category.
     */
    public function edit(Category $category)
    {
        // Exclude current category and its descendants from parent selection list
        $descendants = $category->getDescendantIds();
        $categories = Category::getNestedCategories()->reject(function ($cat) use ($descendants) {
            return in_array($cat->id, $descendants);
        });

        return view('categories.edit', compact('category', 'categories'));
    }

    /**
     * Update the specified category in storage.
     */
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('categories', 'name')->ignore($category->id)],
            'parent_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:50',
        ]);

        // Circular reference checks
        if ($request->parent_id) {
            if ($request->parent_id == $category->id) {
                return back()->withErrors(['parent_id' => 'A category cannot be its own parent.'])->withInput();
            }

            $parentCategory = Category::find($request->parent_id);
            if ($parentCategory && $parentCategory->isDescendantOf($category)) {
                return back()->withErrors(['parent_id' => 'Circular relationship detected: the selected parent is a subcategory of this category.'])->withInput();
            }
        }

        $slug = Str::slug($request->name);
        
        // Ensure slug uniqueness
        $originalSlug = $slug;
        $counter = 1;
        while (Category::where('slug', $slug)->where('id', '!=', $category->id)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        $category->update([
            'name' => $request->name,
            'slug' => $slug,
            'parent_id' => $request->parent_id,
            'description' => $request->description,
            'icon' => $request->icon ?: 'fa-tag',
        ]);

        return redirect()->route('categories.index')->with('success', "Category '{$category->name}' updated successfully.");
    }

    /**
     * Remove the specified category from storage.
     */
    public function destroy(Category $category)
    {
        // Safety check: check if any products are directly attached to this category
        if ($category->products()->exists()) {
            return back()->withErrors("Cannot delete category '{$category->name}' because it contains active products. Please reassign the products first.");
        }

        // Orphans children categories (set parent_id = null)
        $category->children()->update(['parent_id' => null]);

        $category->delete();

        return redirect()->route('categories.index')->with('success', "Category '{$category->name}' deleted successfully.");
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name', 'slug', 'description', 'icon', 'parent_id'];

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Check recursively if this category is a descendant of the given category.
     */
    public function isDescendantOf(Category $category): bool
    {
        $parent = $this->parent;
        while ($parent) {
            if ($parent->id === $category->id) {
                return true;
            }
            $parent = $parent->parent;
        }
        return false;
    }

    /**
     * Recursively get all descendant category IDs (including this category's ID).
     */
    public function getDescendantIds(): array
    {
        $ids = [$this->id];
        foreach ($this->children as $child) {
            $ids = array_merge($ids, $child->getDescendantIds());
        }
        return $ids;
    }

    /**
     * Sum the product counts recursively for this category and all its children.
     */
    public function getTotalProductsCountAttribute(): int
    {
        $ids = $this->getDescendantIds();
        return Product::whereIn('category_id', $ids)->where('is_visible', true)->count();
    }

    /**
     * Get a flattened list of categories with nesting indentation for select dropdowns.
     */
    public static function getNestedCategories()
    {
        $categories = self::whereNull('parent_id')->with('children')->get();
        $list = [];
        
        $traverse = function ($cats, $prefix = '') use (&$traverse, &$list) {
            foreach ($cats as $cat) {
                $cat->nested_name = $prefix . $cat->name;
                $list[] = $cat;
                if ($cat->children && $cat->children->isNotEmpty()) {
                    $traverse($cat->children, $prefix . '— ');
                }
            }
        };
        
        $traverse($categories);
        return collect($list);
    }
}

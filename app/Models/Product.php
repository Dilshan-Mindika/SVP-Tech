<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'product';

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'brand',
        'sku',
        'barcode',
        'buying_price',
        'price',
        'wholesale_price',
        'stock',
        'warranty_months',
        'expire_date',
        'image_path',
        'description',
        'specifications',
        'is_featured',
        'is_visible'
    ];

    protected $casts = [
        'specifications' => 'array',
        'price' => 'float',
        'buying_price' => 'float',
        'wholesale_price' => 'float',
        'stock' => 'integer',
        'warranty_months' => 'integer',
        'expire_date' => 'date',
        'is_featured' => 'boolean',
        'is_visible' => 'boolean'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function getCategoryAttribute()
    {
        $relation = $this->getRelationValue('category');
        if ($relation) {
            return $relation;
        }

        // Graceful fallback to raw category string column if the relation is missing/null
        $fallback = new \stdClass();
        $fallback->name = $this->attributes['category'] ?? 'Uncategorized';
        $fallback->slug = \Illuminate\Support\Str::slug($fallback->name);
        return $fallback;
    }

    public function serials()
    {
        return $this->hasMany(ProductSerial::class);
    }

    public function grnItems()
    {
        return $this->hasMany(GrnItem::class);
    }

    public function invoiceItems()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function repairItems()
    {
        return $this->hasMany(RepairItem::class);
    }
}

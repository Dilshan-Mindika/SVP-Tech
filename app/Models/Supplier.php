<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'contact_info',
        'part_supply_rate',

        // Additional fields
        'company_name',
        'phone',
        'email',
        'address',
        'tax_number'
    ];

    public function partsUsed()
    {
        return $this->hasMany(PartsUsed::class);
    }

    public function grns()
    {
        return $this->hasMany(Grn::class);
    }

    public function returns()
    {
        return $this->hasMany(ProductReturn::class);
    }
}

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
    ];

    public function partsUsed()
    {
        return $this->hasMany(PartsUsed::class); // Note: Model name might be PartUsed or PartsUsed, I'll use PartsUsed to match table
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MiscellaneousProductDetail extends Model
{
    protected $fillable = [
        'product_id', 'type', 'size', 'brand', 'model',
        'season', 'color', 'warranty',
        'material', 'special_characteristics', 'accessories',
        'age_group', 'year_of_manufacture', 'max_endurance',
        'compatible_vehicles', 'group_type',
    ];

    protected $casts = [
        //  'accessories' => 'array',
        //  'year_of_manufacture' => 'date',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}

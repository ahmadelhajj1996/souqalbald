<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ElectronicsProductDetail extends Model
{
    protected $fillable = [
        'product_id', 'type', 'brand', 'model', 'year_of_manufacture',
        'size_or_weight', 'color', 'warranty', 'accessories',
        'main_specification', 'dimensions', 'state_specification', 'made_from', 'group_type',
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

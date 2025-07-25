<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DevicesProductDetail extends Model
{
    protected $fillable = [
        'product_id',
        'device_type',
        'type',
        'brand',
        'model',
        'made_in',
        'year_of_manufacture',
        'screen_size',

        'warranty',
        'accessories',
        'camera',
        'storage',
        'color',
        'supports_sim',       // Add this field here
        'operation_system',
        'screen_card',
        'ram',
        'processor',
    ];

    protected $casts = [
        // 'accessories' => 'array',
        // 'year_of_manufacture' => 'date',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarProductDetail extends Model
{
    protected $fillable = [
        'product_id',
        'type',
        'brand',
        'model',
        'year',
        'kilometers',
        'fuel_type',
        'dipstick',
        'engine_capacity',
        'num_of_doors',
        'topology_status',
        'size',
        'color',
        'group_type',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}

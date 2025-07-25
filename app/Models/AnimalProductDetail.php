<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnimalProductDetail extends Model
{
    protected $fillable = [
        'product_id',
        'group_type',
        'type',
        'brand',
        'age',
        'gender',
        'specialization',
        'service_provider_name',
        'work_time',
        'vaccinations',
        'price',
        'price_type',
        'state',
        'model_or_size',
        'color',
        'appropriate_to',
        'accessories',
        'services_price',
        'service_type',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}

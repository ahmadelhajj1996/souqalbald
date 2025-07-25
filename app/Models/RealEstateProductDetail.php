<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RealEstateProductDetail extends Model
{
    protected $fillable = [
        'product_id', 'type', 'ownership', 'contract_type',
        'num_of_room', 'num_of_bathroom', 'num_of_balconies',
        'area', 'floor', 'furnished', 'age_of_construction',
        'readiness', 'facade', 'nature_of_land', 'street_width', 'group_type',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}

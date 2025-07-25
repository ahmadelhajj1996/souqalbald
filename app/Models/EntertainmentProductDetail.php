<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EntertainmentProductDetail extends Model
{
    protected $fillable = [
        'product_id',
        'type',
        'model',
        'storage',
        'attached_games',
        'num_of_accessories_supplied',
        'warranty',
        'date_of_purchase',
        'color',
        'brand',
        'accessories',
        'title_of_book',
        'language',
        'number_of_copies',
        'author',
        'publishing_house_and_year',
        'name',
        'version',
        'online_availability', 'group_type',
        'edition',
    ];

    protected $casts = [
        'accessories' => 'array',
        'attached_games' => 'boolean',
        'online_availability' => 'boolean',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}

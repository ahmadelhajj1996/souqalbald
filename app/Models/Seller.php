<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seller extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'store_owner_name',
        'store_name',
        'address',
        'logo',
        'cover_image',
        'description',
        'status',
        'phone',
        'is_featured',

    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'added_by', 'user_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubCategory extends Model
{
    use SoftDeletes;

    protected $fillable = ['category_id', 'name', 'image'];

    protected $casts = ['name' => 'array'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function getNameAttribute($value)
    {
        $locale = app()->getLocale();

        return $this->attributes['name'] = $this->castAttribute('name', $value)[$locale] ?? null;
    }

    public function offers()
    {
        return $this->morphMany(\App\Models\Offer::class, 'offerable');
    }
}

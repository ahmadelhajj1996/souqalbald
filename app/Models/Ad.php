<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Ad extends Model
{
    protected $fillable = [
        'ad_type',
        'title',
        'description',
        'phone',
        'email',
        'image',
        'category_id',
        'sub_category_id',
        'price',
        'condition',
        'job_title',
        'job_type',
        'location',
        'salary',
        'experience',
        'education',
        'expected_salary',
        'skills',
        'age',
        'service_title',
        'service_type',
        'city',
        'unit_price',
    ];

    protected $casts = [
        'title' => 'array',
        'description' => 'array',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class);
    }

    public function images()
    {
        return $this->hasMany(AdImage::class);
    }

    public function scopeLocation(Builder $query, $latitude, $longitude, $distanceMeters = 200)
    {
        $data = $this->getBoundingBox($latitude, $longitude, $distanceMeters);

        return $this->search($query, $latitude, $longitude, $data);
    }
}

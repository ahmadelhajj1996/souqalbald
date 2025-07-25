<?php

namespace App\Models;

use App\Traits\SearchByLocationTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory,SearchByLocationTrait;

    protected $appends = ['final_price'];

    protected $fillable = [
        'sub_category_id',
        'title',
        'governorate',
        'address_details',
        'long',
        'lat',
        'description',
        'phone_number',
        'email',
        'category_id',
        'price',
        'price_type',
        'state',
        'added_by',
        'views',
        'favorites_number',
        'is_featured',
        'is_active',
    ];

    public function seller()
    {
        return $this->belongsTo(User::class, 'added_by');
    }

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
        return $this->hasMany(ProductImage::class);
    }

    public function animalProductDetails()
    {
        return $this->hasOne(AnimalProductDetail::class);
    }

    public function deviceDetails()
    {
        return $this->hasOne(DevicesProductDetail::class);
    }

    public function carProductDetails()
    {
        return $this->hasOne(CarProductDetail::class);
    }

    public function realEstateProductDetails()
    {
        return $this->hasOne(RealEstateProductDetail::class);
    }

    public function entertainmentProductDetails()
    {
        return $this->hasOne(EntertainmentProductDetail::class);
    }

    public function miscellaneousProductDetails()
    {
        return $this->hasOne(MiscellaneousProductDetail::class);
    }

    public function electronicsProductDetails()
    {
        return $this->hasOne(ElectronicsProductDetail::class);
    }

    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'favorites')
            ->withTimestamps();
    }

    public function offers()
    {
        return $this->morphMany(\App\Models\Offer::class, 'offerable');
    }

    public function getFinalPriceAttribute()
    {
        $apply = function ($offer) {
            return round($this->price * (1 - $offer->discount_percentage / 100), 2);
        };
        $prodOffer = $this->offers()
            ->where('type', 'discount')
            ->latest()->first();
        if ($prodOffer) {
            return $apply($prodOffer);
        }
        if ($this->subCategory) {
            $subOffer = $this->subCategory
                ->offers()
                ->where('type', 'discount')
                ->latest()
                ->first();

            if ($subOffer) {
                return $apply($subOffer);
            }
        }

        if ($this->category) {
            $catOffer = $this->category
                ->offers()
                ->where('type', 'discount')
                ->latest()
                ->first();

            if ($catOffer) {
                return $apply($catOffer);
            }
        }

        return $this->price;
    }

    public function reviews()
    {
        return $this->hasMany(\App\Models\Review::class);
    }

    public function violations()
    {
        return $this->hasMany(\App\Models\Violation::class);
    }
}

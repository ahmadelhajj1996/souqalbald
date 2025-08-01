<?php

// app/Models/Offer.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    protected $fillable = [
        'offerable_type',
        'offerable_id',
        'type',
        'description',
        'discount_percentage',
        'image',
    ];

    public function offerable()
    {
        return $this->morphTo();
    }
}

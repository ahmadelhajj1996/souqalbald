<?php

// app/Models/Violation.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Violation extends Model
{
    protected $fillable = [
        'product_id',
        'type',
        'notes',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}

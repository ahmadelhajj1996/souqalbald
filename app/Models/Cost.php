<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cost extends Model
{
    public $guarded =[];

    protected $hidden = [
        'costable_id',
        'costable_type',
        'created_at',
        'updated_at',
    ];

}

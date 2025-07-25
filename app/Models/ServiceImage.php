<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceImage extends Model
{
    protected $table = 'service_images';

    protected $fillable = ['service_id', 'image'];

    /**
     * The service that this image belongs to.
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobAdImage extends Model
{
    protected $table = 'job_ad_images';

    protected $fillable = ['job_ad_id', 'image'];

    /**
     * The job ad that this image belongs to.
     */
    public function jobAd(): BelongsTo
    {
        return $this->belongsTo(JobAd::class);
    }
}

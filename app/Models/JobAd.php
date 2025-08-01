<?php

namespace App\Models;

use App\Traits\CurrencyRatesHandler;
use App\Traits\SearchByLocationHandler;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JobAd extends Model
{
    use CurrencyRatesHandler;
    use HasFactory;
    use SearchByLocationHandler;

    protected $table = 'job_ads';

    protected $fillable = [
        'added_by',
        'title',
        'job_type',
        'governorate',
        'location',
        'long',
        'lat',
        'salary',
        'education',
        'experience',
        'skills',
        'description',
        'work_hours',
        'start_date',
        'phone_number',
        'email',
        'job_title',
        'type',
        'is_active',
    ];

    protected function getFieldToCalculateRatesFor(): string
    {
        return 'salary';
    }

    /**
     * The user who added this job ad
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    /**
     * Job ad images (if you created a job_ad_images table)
     */
    public function images(): HasMany
    {
        return $this->hasMany(JobAdImage::class);
    }
}

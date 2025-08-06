<?php

// app/Models/Service.php

namespace App\Models;

use App\Traits\CurrencyRatesHandler;
use App\Traits\SearchByLocationHandler;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    use CurrencyRatesHandler;
    use HasFactory;
    use SearchByLocationHandler;

    protected $fillable = [
        'added_by',
        'title',
        'type',
        'description',
        'price',
        'governorate',
        'location',
        'long',
        'lat',
        'days_hours',
        'phone_number',
        'email',
        'is_active',
    ];

    protected $appends = [
        'seller_phone',
    ];

    /**
     * The user who added this service
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    /**
     * Service images (if you created a service_images table)
     */
    public function images(): HasMany
    {
        return $this->hasMany(ServiceImage::class);
    }

    public function getSellerPhoneAttribute()
    {
        $user = User::where('id',$this->added_by)->first();
        if($user === null){
            return '';
        }
        return $user->seller !== null ?
            $user->seller?->phone :
            $user->phone;
    }
}

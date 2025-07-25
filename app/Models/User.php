<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    use HasRoles;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'age',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function favoriteProducts()
    {
        return $this->belongsToMany(Product::class, 'favorites')
            ->withTimestamps();
    }

    public function reviews()
    {
        return $this->hasMany(\App\Models\Review::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function chats()
    {
        return Chat::where(function ($query) {
            $query->where('user_one_id', $this->id)
                ->orWhere('user_two_id', $this->id);
        });
    }

    /*public function getChatsAttribute()
{
    return Chat::where('user_one_id', $this->id)
               ->orWhere('user_two_id', $this->id)
               ->with(['userOne', 'userTwo', 'latestMessage'])
               ->get();
}
    */
    public function messages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }
}

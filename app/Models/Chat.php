<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    protected $fillable = ['user_one_id', 'user_two_id'];

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function userOne()
    {
        return $this->belongsTo(User::class, 'user_one_id');
    }

    public function userTwo()
    {
        return $this->belongsTo(User::class, 'user_two_id');
    }

    public function otherUser($userId)
    {
        if ($this->user_one_id == $userId) {
            return User::find($this->user_two_id);
        }

        return User::find($this->user_one_id);
    }

    public function latestMessage()
    {
        return $this->hasOne(Message::class)->latestOfMany();
    }
}

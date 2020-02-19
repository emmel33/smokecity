<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserFriend extends Model
{
    //
    public $incrementing = true;
    public $timestamps = false;
    protected $table="user_friends";
    protected $fillable=[
        'id',
        'status',
        'friendid',
        'userid'
    ];
}

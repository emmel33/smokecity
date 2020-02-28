<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserFriend extends Model
{
    //
    public $incrementing = true;
    public $timestamps = false;
	protected $primaryKey = 'friendship_id';
    protected $table="user_friends";
    protected $fillable=[
        'friendship_id',
        'status',
        'first_id',
        'second_id'
    ];
}

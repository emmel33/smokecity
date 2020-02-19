<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class UserLocation extends Model
{
    public $timestamps = false;
    protected $table="user_location";
    protected $fillable=[
        'id',
        'date',
        'lat',
        'long1',
        'userid'
    ];
}

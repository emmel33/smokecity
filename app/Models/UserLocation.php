<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class UserLocation extends Model
{
    public $timestamps = false;
    protected $table="user_location";
	protected $primaryKey = 'userid';
    protected $fillable=[
		'userid',
        'date',
		'long1',
        'lat',
		'active'
        
    ];
}

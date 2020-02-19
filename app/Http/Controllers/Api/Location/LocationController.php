<?php

namespace App\Http\Controllers\Api\Location;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\UserLocation;
use App\Models\ResponseModel;

class LocationController extends Controller
{
    //
    public function setUserLocation(Request $request){
        $loginUser= $this->getAuthUser($request);
        $userLocation=new UserLocation();
        $userLocation->date=now();

        $userLocation->userid = $loginUser->id;
        $userLocation->lat = $request->lat;
        $userLocation->long1 = $request->long1;

        $userLocation->save();

        $obj=new ResponseModel("Successfully updated.",$userLocation,1,null);
        return response()->json($obj);

    }

    public function getAuthUser(Request $request)
    {
        return auth('api')->user();
    }
}

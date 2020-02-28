<?php

namespace App\Http\Controllers\Api\Location;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\UserLocation;
use App\Models\ResponseModel;
use Illuminate\Support\Facades\DB;

class LocationController extends Controller
{
    //
    public function setUserLocation(Request $request){
        $loginUser= $this->getAuthUser($request);
		$userid= $loginUser->userid;
		$userLocation = DB::select('select * from user_location where userid = ?', [$userid]);
		//if ($userLocation == 0) {
		$obj=new ResponseModel("No entrys.",$userLocation,1,null);
        return response()->json($obj);	
		//}
		/*if ($userLocation != 0) {
        $userLocation->date=now();
		$userLocation->lat = $request->lat;
        $userLocation->long1 = $request->long1;
		$userLocation->active = 1;
        $userLocation->save();

        $obj=new ResponseModel("Successfully updated.",$userLocation,1,null);
        return response()->json($obj);
		}*/
    }

    public function getAuthUser(Request $request)
    {
        return auth('api')->user();
    }
}

<?php

namespace App\Http\Controllers\Api\Location;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\UserLocation;
use App\Models\ResponseModel;
use Illuminate\Support\Facades\DB;

use sngrl\PhpFirebaseCloudMessaging\Client;
use sngrl\PhpFirebaseCloudMessaging\Message;
use sngrl\PhpFirebaseCloudMessaging\Recipient\Device;
use sngrl\PhpFirebaseCloudMessaging\Notification;

use App\Http\Controllers\Firebase\NotificationController

class LocationController extends Controller
{
    //
    public function setUserLocation(Request $request){
        $loginUser= $this->getAuthUser($request);
		
		$userid= $loginUser->id;
		$lat= $request->lat;
		$long= $request->long1;
		$timeactive = 3600;//muss noch implementiert werden
		
		$userLocation = DB::select('select * from user_location where userid = ?', [$userid]);
		
		if ($userLocation == null) {
		$temp = DB::select('INSERT INTO user_location VALUES (?, ?, ?, ?, ?)', [$userid,now(),$long,$lat,$timeactive]);
		
		$obj=new ResponseModel("Userid is $userid",$userLocation,1,null);
        return response()->json($obj);
		}		
		if ($userLocation != null) {

	    $temp = DB::select('UPDATE user_location SET date=?, long1=?, lat=?, timeactive=? WHERE userid=?', [now(),$long,$lat,$timeactive,$userid]);
		
		
		
		
		
		
		$obj=new ResponseModel("Successfully updated.",$userLocation,1,null);
        return response()->json($obj);
		}
    }

    public function getAuthUser(Request $request)
    {
        return auth('api')->user();
    }
}

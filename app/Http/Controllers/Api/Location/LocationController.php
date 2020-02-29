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

//use App\Http\Controllers\Firebase\NotificationController;

class LocationController extends Controller
{
    
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
		//---------------------LocationUpdate-----------------------
		$server_key = 'AAAAgR6vc9U:APA91bFkqVhnQDMX9MyZXuqrJoNcjwl8e0qFWlcQZWVjwW52NFXm7KDRKGdZN_Hv0HyjocpzZEtrE7rOkbDQSkSsEaideEry3PfygwyHNR7zWAvmGbSwDYh8LEVzaspsLuq9GI0peDSt';
		$client = new Client();
		$client->setApiKey($server_key);
		$client->injectGuzzleHttpClient(new \GuzzleHttp\Client());
		
		$message = new Message();
		$message->setPriority('high');
		$message->addRecipient(new Device('dIkEmJb0Hgw:APA91bFDGltZh5fDetlKOpZp4quZP9YNlmpyj2LHQeFEnzZ1nQtHc5HTcvhh5rxY8mRptGCbfsutUf1QBV0rSP_GTpQwaG8zK9SxV2rhJUMxLVRpgzHmtNgoioV65h_0rGLxcZdN1Stu'));
		$message->addRecipient(new Device('drzGN8eN5oY:APA91bE6jOVYu4--i6QE_QRK4Rfbmf05q4n-zohHVwDAVa9S4_ONbXGqZSp_1OAC82aZWmYdkkQvkoXG3NINZjLaDiYSBuJOd1NA9QGJirKRdclp7Y3DANqYMGrAxnuBmjDUKSsnbMWi'));
		//$message->addRecipient(new Device('_YOUR_DEVICE_TOKEN_3_'));
		$message
			->setNotification(new Notification('Hallo', 'Nachricht'))
			->setData(['key' => 'value']);
			
		$response = $client->send($message);
		//var_dump($response->getStatusCode());
		//var_dump($response->getBody()->getContents());
		
		
		$obj=new ResponseModel("Successfully updated.",$userLocation,1,null);
        return response()->json($obj);
		}
    }

    public function getAuthUser(Request $request)
    {
        return auth('api')->user();
    }
}

<?php

namespace App\Http\Controllers\Api\Location;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\UserLocation;
use App\Models\ResponseModel;
use Illuminate\Support\Facades\DB;
use App\Models\UserFriend;
use App\User;

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
		$message->setPriority('normal');
		//----------------Freunde finden--------------------
		$existingRequest=UserFriend::where('second_id', $loginUser->id)
                                    ->OrWhere('first_id', $loginUser->id)
                                    ->where('status', 'Active')
                                    ->get();
        $userList=array();
        $uniqueUser=[];
        $uniqueUser[$loginUser->id]=true;
        for ($x = 0; $x < count($existingRequest); $x++) {
            if(!array_key_exists($existingRequest[$x]->first_id,$uniqueUser)){
                array_push($userList,$existingRequest[$x]->first_id);
                $uniqueUser[$existingRequest[$x]->first_id]=true;
            }

            if(!array_key_exists($existingRequest[$x]->second_id,$uniqueUser)){
                array_push($userList,$existingRequest[$x]->second_id);
                $uniqueUser[$existingRequest[$x]->second_id]=true;
            }
            
        }
		$wholeuserList = User::find($userList);//villeicht auch unnotig
		for ($x = 0; $x < count($wholeuserList); $x++) {
		
		$message->addRecipient(new Device($userList[$x]->app_token));
		//$message->addRecipient(new Device('dEc7UCC_9MA:APA91bHJrEg1GoCvRDrIH2AeLRaSVjfKazqkwZrXq23ROtd9REJzUf1MIuHSPAiCpMTtS3285BAvNL8GxArh1hM2FQrBSqk6EFCFNN0A5BEW2ArsryWvH7HtHQOSjTRA2pWV52-0rUTV'));

		
			
		}
		
		
		
		
		
		$message->addRecipient(new Device('dIkEmJb0Hgw:APA91bFDGltZh5fDetlKOpZp4quZP9YNlmpyj2LHQeFEnzZ1nQtHc5HTcvhh5rxY8mRptGCbfsutUf1QBV0rSP_GTpQwaG8zK9SxV2rhJUMxLVRpgzHmtNgoioV65h_0rGLxcZdN1Stu'));
		$message->addRecipient(new Device('dEc7UCC_9MA:APA91bHJrEg1GoCvRDrIH2AeLRaSVjfKazqkwZrXq23ROtd9REJzUf1MIuHSPAiCpMTtS3285BAvNL8GxArh1hM2FQrBSqk6EFCFNN0A5BEW2ArsryWvH7HtHQOSjTRA2pWV52-0rUTV'));
		//$message->addRecipient(new Device('_YOUR_DEVICE_TOKEN_3_'));
		$message
			->setNotification(new Notification('Hallo', 'Nachricht'))
			->setData(['key' => 'value']);
	
		$messagetimeout = "1800"; //zeit wie lange benachrichtung gespeichert wird
		$message->setJsonKey("apns", ["headers" => ["apns-expiration" => time() + $messagetimeout]]);
		$message->setJsonKey("android", ["ttl" => $messagetimeout . "s"]);
		$message->setJsonKey("webpush", ["headers" => ["TTL" => $messagetimeout . ""]]);
		$response = $client->send($message);
		
		
		$obj=new ResponseModel("Successfully updated.",$userLocation,1,null);
        return response()->json($obj);
		}
    }

    public function getAuthUser(Request $request)
    {
        return auth('api')->user();
    }
}

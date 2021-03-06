<?php

namespace App\Http\Controllers\Api\Friends;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\UserFriend;
use App\Models\ResponseModel;
use App\User;
use App\Models\UserLocation;

class FriendController extends Controller
{
    public function addFriend(Request $request){
        $loginUser= $this->getAuthUser($request);
        $existingRequest=UserFriend::where('first_id', $loginUser->id)
                                    ->where('second_id', $request->userid)
                                    ->count();
		
        //userid ist die angefragte Freundesid
		//id die eigene ID	
        if($existingRequest>0){
            $obj=new ResponseModel("You have already a request pending.",null,0,null);
            return response()->json($obj);
        }
		
		$existingRequest=UserFriend::where('first_id', $request->userid)
									->where('second_id', $loginUser->id)
                                    ->count();
									
		if($existingRequest>0){
            $obj=new ResponseModel("You have a request from your friend or are already in a friendship.",null,0,null);
            return response()->json($obj);
        }
       
        $uf=new UserFriend();
		$uf->first_id=$loginUser->id;
        $uf->second_id=$request->userid;
		$uf->status="Pending";
        $uf->save();

        $obj=new ResponseModel("Your friend request successfully sent.",$uf,1,null);
        return response()->json($obj);

    }

    public function acceptFriend(Request $request){
        $loginUser= $this->getAuthUser($request);
        $existingRequest=UserFriend::where('second_id', $loginUser->id)
                                    ->where('first_id', $request->userid);
        if($existingRequest->count()==0){
            $obj=new ResponseModel("No Pending request found.",null,1,null);
            return response()->json($obj);
        }
       
        $uf=$existingRequest->first();
	
        $uf->status="Active";
        $uf->save();

        $obj=new ResponseModel("Your friend request successfully accepted.",$uf,1,null);
        return response()->json($obj);

    }

    public function rejectFriend(Request $request){
        $loginUser= $this->getAuthUser($request);
        $existingRequest=UserFriend::where('second_id', $loginUser->id)
                                    ->where('first_id', $request->userid)
                                    ->delete();;
        
        $obj=new ResponseModel("Your friend request successfully rejected.",null,1,null);
        return response()->json($obj);
        // $obj=$this->getUserListWithDetails($loginUser->id,[1,2,3,4,5,6,7,8,10]);
        // return response()->json($obj);
    }

    public function userDetails(Request $request){
        $loginUser= $this->getAuthUser($request);
        $userList=array();
        array_push($userList,$request->userId);

        //return response()->json($userList);
        $users=$this->getUserListWithDetails($loginUser->id,$userList);
        $obj=new ResponseModel("",$users[0],1,null);

        return response()->json($obj);
    }
	
	
	    public function removeFriend(Request $request){
        $loginUser= $this->getAuthUser($request);
        $existingRequest=UserFriend::where('second_id', $loginUser->id)
                                    ->where('first_id', $request->userid)
                                    ->delete();;
		$existingRequest=UserFriend::where('second_id', $request->userid)
                                    ->where('first_id', $loginUser->id)
                                    ->delete();;
        
        $obj=new ResponseModel("Your friend is successfully removed.",null,1,null);
        return response()->json($obj);
       
    }

    public function searchUser(Request $request){
        $loginUser= $this->getAuthUser($request);
		$userid= $loginUser->id;
		
        $existingUser=User::where("name", 'like', '%'.$request->keyword.'%')
                                    ->orWhere("email", 'like', '%'.$request->keyword.'%')
									->orWhere("full_name", 'like', '%'.$request->keyword.'%')//geändert, hinzugefügt
                                    ->get();

        
		
        $userList=array();
        for ($x = 0; $x < count($existingUser); $x++) {
			if($existingUser[$x]->id != $userid){
            array_push($userList,$existingUser[$x]->id);
			}
        }
        //return response()->json($userList);
        $users=$this->getUserListWithDetails($loginUser->id,$userList);
        $obj=new ResponseModel("",$users,1,null);

        return response()->json($obj);
    }
    public function getPendingFriendList(Request $request){
        $loginUser= $this->getAuthUser($request);
        $existingRequest=UserFriend::where('second_id', $loginUser->id)
                                    ->where('status', 'Pending')
                                    ->get();
        //$obj=["loginUser"=>$loginUser,"existingRequest"=>$existingRequest];
        //return response()->json($obj);

        $userList=array();
        for ($x = 0; $x < count($existingRequest); $x++) {
            array_push($userList,$existingRequest[$x]->first_id);
        }
        
        $users=$this->getUserListWithDetails($loginUser->id,$userList);
       
        $obj=new ResponseModel("",$users,1,null);

        return response()->json($obj);
    }

    public function getActiveFriendList(Request $request){
        $loginUser= $this->getAuthUser($request);

		$userList = $this->getFriendIDs($loginUser->id);
        
        $users=$this->getUserListWithDetails($loginUser->id,$userList);
       
        $obj=new ResponseModel("",$users,1,null);

        return response()->json($obj);
    }
//unten muss noch bearbeitet werden
    public function getFriendListOnMap(Request $request){
        $loginUser= $this->getAuthUser($request);
		
		$userList = $this->getFriendIDs($loginUser->id);

        $users=$this->getUserListForMaps($loginUser->id,$userList);
		

        $own=[
            "email"=>null,
            "name"=>null,
            "auth"=>null,
            "userses"=>$users
        ];
        $obj=new ResponseModel("",$own,1,null);

        return response()->json($obj);
    }

    public function getUserListWithDetails($currentUserId,$listUserId){
		
        $userList = User::find($listUserId);
        $frindStatusList = UserFriend::where('first_id', $currentUserId)
                                       ->OrWhere('second_id',$currentUserId )
                                       ->get();
        $locationList = UserLocation::whereIn("userid",$listUserId)
									//->whereNotNull("lat")
		                             //->whereNotIn("lat","NULL")//nur wichtig für orts-liste
                                    //->orderBy('date', 'desc')
                                    ->get();

        $user=[];
        for ($x = 0; $x < count($userList); $x++) {
            $obj=[
                "id"=>$userList[$x]->id,
				"name"=>$userList[$x]->name,
                "email"=>$userList[$x]->email,
                "status"=>null,
                "lat"=>null,
                "long1"=>null,
                "ufid"=>null
            ];
            $user[$userList[$x]->id]=$obj;
        }

        //return ["fl"=>$frindStatusList,"ul"=>$userList,"li"=>$listUserId];
        for ($x = 0; $x < count($frindStatusList); $x++) {
            if(array_key_exists($frindStatusList[$x]->second_id,$user)){
                $obj=$user[$frindStatusList[$x]->second_id];
                
                $obj["status"]=$frindStatusList[$x]->status;
                //$obj["ufid"]=$frindStatusList[$x]->id;
                $obj["ufid"]=$frindStatusList[$x]->first_id;

                $user[$frindStatusList[$x]->second_id]=$obj;
            }else if(array_key_exists($frindStatusList[$x]->first_id,$user)){
                //$obj=$user[$frindStatusList[$x]->friendid];
                $obj=$user[$frindStatusList[$x]->first_id];
                
                $obj["status"]=$frindStatusList[$x]->status;
                //$obj["ufid"]=$frindStatusList[$x]->id;
                $obj["ufid"]=$frindStatusList[$x]->first_id;
                $user[$frindStatusList[$x]->first_id]=$obj;
            }
            
        }

        //return ["locationList"=>$locationList,"user"=>$user];
        for ($x = 0; $x < count($locationList); $x++) {
            if(array_key_exists($locationList[$x]->userid,$user)){
                $obj=$user[$locationList[$x]->userid];
                $obj["lat"]=$locationList[$x]->lat;
                $obj["long1"]=$locationList[$x]->long1;
                $user[$locationList[$x]->userid]=$obj;
            }
            
        }
        
        $arr=array();
        for ($x = 0; $x < count($userList); $x++) {
            array_push($arr,$user[$userList[$x]->id]);
        }
        return $arr;
    }
	
	
	public function getUserListForMaps($currentUserId,$listUserId){
		
        $userList = User::find($listUserId);
        $frindStatusList = UserFriend::where('first_id', $currentUserId)
                                       ->OrWhere('second_id',$currentUserId )
                                       ->get();
        $locationList = UserLocation::whereIn("userid",$listUserId)
                                    ->get();

        $user=[];
	
		
			for ($x = 0; $x < count($userList); $x++) {
				$obj=[
					"id"=>$userList[$x]->id,
					"name"=>$userList[$x]->name,
					"email"=>$userList[$x]->email,
					"status"=>null,
					"lat"=>null,
					"long1"=>null,
					"ufid"=>null
				];
				$user[$userList[$x]->id]=$obj;
			}

        
			for ($x = 0; $x < count($frindStatusList); $x++) {
				if(array_key_exists($frindStatusList[$x]->second_id,$user)){
					$obj=$user[$frindStatusList[$x]->second_id];
					$obj["status"]=$frindStatusList[$x]->status;
					$obj["ufid"]=$frindStatusList[$x]->first_id;
					$user[$frindStatusList[$x]->second_id]=$obj;
				
				
				}else if(array_key_exists($frindStatusList[$x]->first_id,$user)){
					$obj=$user[$frindStatusList[$x]->first_id];
					$obj["status"]=$frindStatusList[$x]->status;
					$obj["ufid"]=$frindStatusList[$x]->first_id;
					$user[$frindStatusList[$x]->first_id]=$obj;
				}
            
			}

			for ($x = 0; $x < count($locationList); $x++) {
				if(array_key_exists($locationList[$x]->userid,$user)){
					$obj=$user[$locationList[$x]->userid];
					$timestamp=$locationList[$x]->date;
					$oldtime=strtotime($timestamp);
					$timeactive=$locationList[$x]->timeactive;
					
					if((time()-$oldtime) > $timeactive){ //3600 Sekunden is man aktiv(=1h)
						$obj["lat"]=null;
						$obj["long1"]=null;
					}else{
						$obj["lat"]=$locationList[$x]->lat;
						$obj["long1"]=$locationList[$x]->long1;
					}
					$user[$locationList[$x]->userid]=$obj;
				}
            
			}
        
        $arr=array();
        for ($x = 0; $x < count($userList); $x++) {
            array_push($arr,$user[$userList[$x]->id]);
        }
		
        return $arr;
    }
	
    public function getAuthUser(Request $request)
    {
        return auth('api')->user();
    }
	
	public function getFriendIDs($userid)
	{
        $existingRequest=UserFriend::where('second_id', $userid)
                                    ->OrWhere('first_id', $userid)
                                    ->get();		
		
		$userList=array();
        $uniqueUser=[];
        $uniqueUser[$userid]=true;
        for ($x = 0; $x < count($existingRequest); $x++) {
            if($existingRequest[$x]->status!='Active'){
                continue;
            }
            if(!array_key_exists($existingRequest[$x]->first_id,$uniqueUser)){
                array_push($userList,$existingRequest[$x]->first_id);
                $uniqueUser[$existingRequest[$x]->first_id]=true;
            }

            if(!array_key_exists($existingRequest[$x]->second_id,$uniqueUser)){
                array_push($userList,$existingRequest[$x]->second_id);
                $uniqueUser[$existingRequest[$x]->second_id]=true;
            }
            
        }
        
		return $userList;
	}
}

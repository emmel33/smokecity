<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use App\Models\ResponseModel;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
  
    public $loginAfterSignUp = true;

    public function register(Request $request)
    {
      $existingUser=User::where("email",$request->email)->count();
      if($existingUser>0){
        $obj=new ResponseModel("",null,0,["You have already an account"]);
        return response()->json($obj);
      }
      $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => bcrypt("teamaos123arif"),
      ]);

      $token = auth('api')->login($user);

      return $this->respondWithToken($request->email,$token);
    }

     public function loginUser(Request $request)
    {
      $credentials = $request->only(['email', 'password']);

      if (!$token = auth('api')->attempt($credentials)) {
        $obj=new ResponseModel("",null,0,["Login failed.User name or password is incorrect."]);
        return response()->json($obj,401);
      }
      return $this->respondWithToken($request->email,$token);
    }
	
	public function loginSocial(Request $request)
    {
      //$credentials = $request->only(['email', 'password']);
      //return response()->json($loginUser);
     

      $credentials=[
        "email" => $request->email
      ];

      if (!$token = auth('api')->attempt($credentials)) {
        $obj=new ResponseModel("",null,0,["Login failed.User name or password is incorrect."]);
        return response()->json($obj,401);
      }
      return $this->respondWithToken($request->email,$token);
    }

    public function registerUser(Request $request)
    {

      $existingUser=User::where("email",$request->email)->count();
      if($existingUser>0){
        $obj=new ResponseModel("",null,0,["You have already an account"]);
        return response()->json($obj);
      }

      $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => bcrypt($request->password),
      ]);

      $token = auth('api')->login($user);

      return $this->respondWithToken($request->email,$token);
    }


    public function setAppToken(Request $request){
      $loginUser=$this->getAuthUser($request);
      //return response()->json($loginUser);
      $user=User::find($loginUser->original->id);
      $user->app_token=$request->app_token;
      $user->save();
      $obj=new ResponseModel("Successfully set token","SET_TOKEN",1,null);
      return response()->json($obj);
    }

    public function setUserFullname(Request $request){
      $loginUser=$this->getAuthUser($request);
      
      $user=User::find($loginUser->original->id);
      $user->full_name=$request->full_name;
      $user->save();
      $obj=new ResponseModel("Successfully set name","SET_NAME",1,null);
      return response()->json($obj);
    }
	    
	public function checkUserFullname(Request $request){
      $loginUser=$this->getAuthUser($request);
	  $user=User::find($loginUser->original->id);
	  if($request->full_name == $user->full_name){
		$existingUser=User::where("full_name",$request->full_name)->count();
		if($existingUser>0){
			$obj=new ResponseModel("",null,0,["This username is already taken"]);
			return response()->json($obj);
		}
	  }
	  $obj=new ResponseModel($user,"FREE_NAME",1,null);
	  //$obj=new ResponseModel("Still free","FREE_NAME",1,null);
      return response()->json($obj);
    }
	
	public function getUserFullname(Request $request){
      $loginUser=$this->getAuthUser($request);
	  //$user=User::find($request->id)
	  //oder
	  $user=User::find($loginUser->original->id);
	  
	  $userfullname=$user->full_name;
	  $obj=new ResponseModel("",$userfullname,1,null);
      return response()->json($obj);
    }

    
    public function getAuthUser(Request $request)
    {
        return response()->json(auth('api')->user());
		
	}
	
	public function getAuthPassword(Request $request)
    {
        return response()->json(auth('api')->user());
	}
   
   public function logout()
    {
        auth('api')->logout();
        return response()->json(['message'=>'Successfully logged out']);
    }


    protected function getUserByEmail($email){
      $user = DB::select('select * from users where  email = ?', [$email]);
      // return [
      //   "email"=> $user->email,
      //   "name"=>  $user->name
      // ];

      return $user;
    }
    protected function respondWithToken($email,$token)
    {
      $user=$this->getUserByEmail($email);
      $data=[
        "email" =>  $email,
        "name" => $user[0]->name,
        "id" => $user[0]->id,
		"full_name" => $user[0]->full_name,
        "auth" => 'bearer '.$token,
        "userses" => null
      ];
      $obj=new ResponseModel("Successfull",$data,1,null);
      return response()->json($obj);
      
    }

    

}
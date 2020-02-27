<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use App\Models\ResponseModel;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
	public function notify{
	$data = json_decode( \request()->getContent() );

	$sender    = $data->sender_user;
	$receiver  = $data->receiver_user;
	$notification_payload   = $data->payload;
	$notification_title     = $data->title;
	$notification_message   = $data->message;
	$notification_push_type = $data->push_type;
		
	}
	
}
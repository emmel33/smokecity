<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
//Route::get('user-location','Location\UserLocationController@locationList');

Route::post('Register', 'Api\AuthController@register');
Route::post('Login', 'Api\AuthController@login');
Route::post('RegisterUser', 'Api\AuthController@registerUser');
Route::post('LoginUser', 'Api\AuthController@loginUser');
Route::get('Logout', 'Api\AuthController@logout');
Route::get('User', 'Api\AuthController@getAuthUser');
Route::get('GetName', 'Api\AuthController@getUserFullname');
Route::post('SetToken', 'Api\AuthController@setAppToken');
Route::post('SetName', 'Api\AuthController@setUserFullname');
Route::post('CheckName', 'Api\AuthController@checkUserFullname');

Route::post('Friends/Add', 'Api\Friends\FriendController@addFriend');
Route::post('Friends/Accept', 'Api\Friends\FriendController@acceptFriend');
Route::post('Friends/Reject', 'Api\Friends\FriendController@rejectFriend');
Route::post('Friends/Remove', 'Api\Friends\FriendController@removeFriend');
Route::get('Friends/getFriendsOnMap', 'Api\Friends\FriendController@getFriendListOnMap');
Route::get('Friends', 'Api\Friends\FriendController@getPendingFriendList');
Route::get('MyFriends', 'Api\Friends\FriendController@getActiveFriendList');
Route::post('Friends/Search', 'Api\Friends\FriendController@searchUser');
Route::post('UserDetails', 'Api\Friends\FriendController@userDetails');

Route::post('Location', 'Api\Location\LocationController@setUserLocation');

Route::post( 'notify', 'Firebase\NotificationController@notify' );
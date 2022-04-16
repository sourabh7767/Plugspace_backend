<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::post('sendOTP', 'Api\UserAPIController@sendOTP');
Route::post('verifyOTP', 'Api\UserAPIController@verifyOTP');
Route::get('check', 'Api\UserAPIController@check');
Route::post('testPush','Api\UserAPIController@testPush');
Route::get('testMail','Api\UserAPIController@testMail');
Route::post('signUp', 'Api\UserAPIController@signUp');
Route::post('updateProfile', 'Api\UserAPIController@updateProfile');
Route::post('getUserProfile', 'Api\UserAPIController@getUserProfile');
Route::post('deleteAccount', 'Api\UserAPIController@deleteAccount');
Route::post('logOut', 'Api\UserAPIController@logOut');
Route::post('login', 'Api\UserAPIController@login');
Route::post('isRegister', 'Api\UserAPIController@isRegister');
Route::post('contactUs', 'Api\UserAPIController@contactUs');
Route::post('ageCalculator','Api\UserAPIController@ageCalculator');
Route::post('getRankPerson','Api\UserAPIController@getRankPerson');
Route::post('previewUpdatePro','Api\UserAPIController@previewUpdatePro');

Route::post('profileLikeDislike','Api\HomeAPIController@profileLikeDislike');
Route::post('getHomeDetails','Api\HomeAPIController@getHomeDetails');
Route::post('createStory','Api\HomeAPIController@createStory');
Route::post('deleteStory','Api\HomeAPIController@deleteStory');
Route::post('getChatList','Api\HomeAPIController@getChatList');
Route::post('getNotificationList','Api\HomeAPIController@getNotificationList');

Route::post('getMyViewStory','Api\HomeAPIController@getMyViewStory');
Route::post('getStoryDetails','Api\HomeAPIController@getStoryDetails');
Route::post('createViewStory','Api\HomeAPIController@createViewStory');
Route::post('createViewProfile','Api\HomeAPIController@createViewProfile');

Route::post('isPrivateScore','Api\MyScoreAPIController@isPrivateScore');
Route::post('getMyScore','Api\MyScoreAPIController@getMyScore');
Route::post('getFriendsScore','Api\MyScoreAPIController@getFriendsScore');

Route::post('createFeed','Api\HomeAPIController@createFeed');

Route::post('storyComment','Api\HomeAPIController@storyComment');
Route::get('storyDeleteCron','Api\HomeAPIController@storyDeleteCron');

Route::post('paywithStripe', 'Api\PaymentAPIController@paywithStripe');
Route::post('Subscription', 'Api\UserAPIController@Subscription');
Route::get('getMusic','Api\HomeAPIController@getMusic');
Route::get('getMessages','Api\HomeAPIController@getMessages');
Route::post('getMusicList','Api\HomeAPIController@getMusicList');
Route::post('getFavoriteMusic','Api\HomeAPIController@getFavoriteMusic');
Route::post('musicLikeDislike','Api\HomeAPIController@musicLikeDislike');

Route::post('profileReportByUser','Api\HomeAPIController@profileReportByUser');


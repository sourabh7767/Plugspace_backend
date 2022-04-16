<?php

namespace App\Http\Controllers\Api;

use App\Providers\RouteServiceProvider;
use App\Http\Controllers\AppBaseController;
use App\Http\Controllers\CommonController;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\User_Master;
use App\Models\Key_Master;
use App\Models\Key_Token_Master;
use App\Models\Story_Master;
use App\Models\Story_Media_Master;
use App\Models\Notification_Master;
use App\Models\Likes_Master;
use App\Models\User_Media_Master;
use App\Models\ViewProfile;
use App\Models\ViewStory;
use App\Models\Music_Master;
use App\Models\Music_Likes_Master;
use App\Models\Feed_Master;
use App\Models\Story_Comment_Master;
use App\Models\Report_Master;
use App\Models\SampleMessage;
use File;
use DB;
use DateTime;


class HomeAPIController extends AppBaseController
{
    public function localeSetting($lang = 'en')
    {
        $a = \App::setlocale($lang);
    }

    public function getHomeDetails(Request $request)
    {
        try {
            extract($request->all());
            $key = $request->header('key');

            $token = $request->header('token');
            $valid = AppBaseController::requiredValidation([
                'key' => @$key,
                'token' => @$token,
            ]);

            if ($valid != '') {
                $msg = trans('words.please_enter') . $valid;
                return $this->responseError(0, $msg);
            }

            $devicetype = empty($_SERVER["HTTP_DEVICETYPE"]) ? "" : $_SERVER["HTTP_DEVICETYPE"];
            $versioncode = empty($_SERVER["HTTP_VERSIONCODE"]) ? "" : $_SERVER["HTTP_VERSIONCODE"];
            if (CommonController::versionCheck($devicetype, $versioncode)) {
                return AppBaseController::responseError(26, trans('words.update_app_msg'));
            }
            if (CommonController::checkKeyExist($key) == 0) {
                return AppBaseController::responseError(0, trans('words.incorrect_key'));
            } elseif (CommonController::checkKeyTokenExist($key, $token) == 0) {
                return AppBaseController::responseError(0, trans('words.incorrect_token'));
            }

            $valid = AppBaseController::requiredValidation([
                'user_id' => @$user_id,

            ]);
            if ($valid != '') {
                $msg = trans('words.please_enter') . $valid;
                return $this->responseError(0, $msg);
            }

            $chkUserExist = User_Master::where('user_id', $user_id)->first();
            if (empty($chkUserExist)) {
                return AppBaseController::responseError(0, trans('words.invalid_user_id'));
            }
            if ($chkUserExist->status == '1') {
                return AppBaseController::responseError(5, trans('words.account_deactive'));
            }

            $likeDtl = Likes_Master::where('user_id', $user_id)->where('like_id', 274)->pluck('like_user_id')->toArray();
            //TODO: if match than remove from $likeDTL, if request not accepted than remove for 3 weeks
            $newLikeDtl = [];
            foreach($likeDtl as $likeUserId){
                $checkMatch = Likes_Master::where('user_id', $likeUserId)->where('like_user_id', $user_id)->first();
                if(!empty($checkMatch)){
                    $newLikeDtl[] = $likeUserId;
                }else{
                    $checkRequestSent = Likes_Master::where('user_id', $user_id)->where('like_user_id', $likeUserId)->first();
                    $updateDate = date('Y-m-d H:i:s', strtotime($checkRequestSent->updated_at.'+21 days'));
                    $currentDate = date('Y-m-d H:i:s');
                    if($updateDate < $currentDate){
                        $newLikeDtl[] = $likeUserId;
                    }
                }
            }
            $userDtl = User_Master::where('user_id', '!=', $user_id)->whereNotIn('user_id', $newLikeDtl)->get();

            $ageRange = explode('-',$chkUserExist->age_range_marriage);
           
            $ageRanges = [];
            foreach ($userDtl as $key => $value) {
                    $dob = $value->dob;
                    $condate = date("Y-m-d");
                    $birthdate = new DateTime(date("Y-m-d", strtotime(implode('-', array_reverse(explode('/', $dob))))));
                    $today = new DateTime(date("Y-m-d", strtotime(implode('-', array_reverse(explode('/', $condate))))));
                    $age = $birthdate->diff($today)->y;
                                
                    if($ageRange[0] >= $age  && $age <= $ageRange[1]){
                        $ageRanges[] = $value->user_id;  
                    }
            }  

            $userDtls = User_Master::inRandomOrder()->where('user_id', '!=', $user_id)->whereNotIn('user_id', $ageRanges)->limit(1)->get();

            if(isset($search_text) && $search_text != '') {
                $userDtls = User_Master::inRandomOrder()->where('name', 'LIKE', '%' . $search_text . '%')->where('user_id', '!=', $user_id)->limit(1)->get();
            }
            foreach ($userDtls as $user) {
                $user['about_you'] = (string)json_decode($user->about_you);


                $checkProfile =  ViewProfile::where(['user_id' => $user_id,'view_user_id' => $user->user_id])->first();
                if(empty($checkProfile)){
                    ViewProfile::create(['user_id' => $user_id,'view_user_id' => $user->user_id]);
                    Notification_Master::create(['user_id'=>$user->user_id,'other_id'=>$user_id,'message'=>'your profile view']);
                    if($user->device_token != ''){
                        CommonController::sendPush($user->name ?? '',"your profile view",$user->device_token);
                    }
                }

                $dob = $user->dob;
                $condate = date("Y-m-d");
                $birthdate = new DateTime(date("Y-m-d", strtotime(implode('-', array_reverse(explode('/', $dob))))));
                $today = new DateTime(date("Y-m-d", strtotime(implode('-', array_reverse(explode('/', $condate))))));
                $age = $birthdate->diff($today)->y;
                $user['age'] = (string)$age;

                $user['is_like'] = Likes_Master::where('user_id', $user_id)->where('like_user_id', $user->user_id)->pluck('like_type')->first() ?? "0";
                $mediaDtl = User_Media_Master::where('user_id', $user->user_id)->get();
                foreach ($mediaDtl as $key => $value) {
                    $value->description = '';

                }
                $feedDtl = Feed_Master::where('user_id', $user->user_id)->get();
                foreach ($feedDtl as $key => $value) {
                    $value->type = 'feed';
                }

                $collection = collect($mediaDtl);
                $merged = $collection->merge($feedDtl);
                $userMediaDtl = $merged->all();
                $user['media_detail'] = $userMediaDtl;
                $checkRequest = Likes_Master::where('user_id', $user->user_id)->where('like_user_id', $user_id)->first();
                if($checkRequest){
                    $user['request_sent'] = 1;
                }else{
                    $user['request_sent'] = 0;
                }
            }

            $notificationDtl = Notification_Master::orderBy('noti_id', 'DESC')->where('user_id', $user_id)->get();

            foreach ($notificationDtl as $key => $value) {
                $userDtl = User_Master::where('user_id', $value->other_id)->first();
                $mediaUserDtl = User_Media_Master::where('user_id', $value->other_id)->first();

                $value['date_time'] = $this->getTime($value->created_at);
                $value['name'] = $userDtl->name ?? '';
                if (!empty($mediaUserDtl)) {
                    $value['profile'] = $mediaUserDtl->profile;
                } else {
                    $value['profile'] = '';
                }

                $value['date_time'] = (string)$this->getTime($value->created_at);
                $value['created_date'] = (string)$value->created_at;
                $value['message'] = utf8_decode($value->message);
                $value['time'] = strtotime($value->created_at) * 1000;


            }

            $storyDtl = Story_Master::join('story_media_master as smm', function ($join) {
                $join->on('story_master.story_id', 'smm.story_id');
            })->leftJoin('view_story as vs', function ($join) {
                $join->on('story_master.user_id', 'vs.view_user_id');
            })->select(['story_master.*', DB::Raw("Case when vs.user_id = '" . $user_id . "' then 1 else 0 end  as story_view_count")])->where('story_master.user_id', '!=', $user_id)->orderBy('story_view_count', 'asc')->orderBy('smm.created_at', 'desc')->groupBy('smm.story_id')->get();
            foreach ($storyDtl as $value) {
//                unset($value->story_view_count);
                $userDtl = User_Master::where('user_id', $value->user_id)->first();
                $mediaUserDtl = User_Media_Master::where('user_id', $value->user_id)->first();

                $value['name'] = $userDtl->name ?? '';

                $checkViewStory = ViewStory::where(['user_id' => $user_id, 'view_user_id' => $value->user_id])->first();

                if (!empty($checkViewStory)) {
                    $value['is_show_story'] = '1';
                } else {
                    $value['is_show_story'] = '0';
                }
                if (!empty($mediaUserDtl)) {
                    $value['profile'] = $mediaUserDtl->profile;
                } else {
                    $value['profile'] = '';
                }
            }

            $getStoryDtl = [];
            $getOwnstoryDtl = Story_Master::orderBy('story_id', 'DESC')->where('user_id', $user_id)->first();
            if (!empty($getOwnstoryDtl)) {
                $userDtl = User_Master::where('user_id', $getOwnstoryDtl->user_id)->first();
                $mediaUserDtl = User_Media_Master::where('user_id', $getOwnstoryDtl->user_id)->first();


                $checkViewStory = ViewStory::where(['user_id' => $user_id, 'view_user_id' => $getOwnstoryDtl->user_id])->first();

                $getStoryDtl[0]['story_id'] = $getOwnstoryDtl->story_id;
                $getStoryDtl[0]['user_id'] = $user_id;
                $getStoryDtl[0]['name'] = $userDtl->name ?? '';

                if (!empty($checkViewStory)) {
                    $getStoryDtl[0]['is_show_story'] = '1';
                } else {
                    $getStoryDtl[0]['is_show_story'] = '0';
                }
                if (!empty($mediaUserDtl)) {
                    $getStoryDtl[0]['profile'] = $mediaUserDtl->profile;
                } else {
                    $getStoryDtl[0]['profile'] = '';
                }

                $getStoryDtl[0]['is_story'] = '1';
            } else {
                // $userDtl = User_Master::where('user_id',$user_id)->first();
                // $mediaUserDtl = User_Media_Master::where('user_id',$user_id)->first();

                // $getStoryDtl[0]['story_id'] = '';
                // $getStoryDtl[0]['user_id'] = $user_id;
                // $getStoryDtl[0]['name'] = $userDtl->name;

                // $getStoryDtl[0]['is_show_story']='';
                // if(!empty($mediaUserDtl)){
                //     $getStoryDtl[0]['profile'] = $mediaUserDtl->profile;
                // }else{
                //     $getStoryDtl[0]['profile'] = '';
                // }
                // $getStoryDtl[0]['is_story']='0';
            }

            foreach ($storyDtl as $key => $value) {
                $value->is_story = '';
                $getStoryDtl[] = $value;
            }
            $data['data'] = $userDtls;
            $data['storyDtl'] = $getStoryDtl;
            $data['notificationDtl'] = $notificationDtl;

            return AppBaseController::successResponse($data, 1, trans('words.list_success'), "True");
        } catch (\Exception $e) {
            return $this->responseError(0, $e->getMessage());
        }
    }

    public function getMusicList(Request $request)
    {
        try {
            extract($request->all());
            $key = $request->header('key');

            $token = $request->header('token');
            $valid = AppBaseController::requiredValidation([
                'key' => @$key,
                'token' => @$token,
            ]);

            if ($valid != '') {
                $msg = trans('words.please_enter') . $valid;
                return $this->responseError(0, $msg);
            }

            $devicetype = empty($_SERVER["HTTP_DEVICETYPE"]) ? "" : $_SERVER["HTTP_DEVICETYPE"];
            $versioncode = empty($_SERVER["HTTP_VERSIONCODE"]) ? "" : $_SERVER["HTTP_VERSIONCODE"];
            if (CommonController::versionCheck($devicetype, $versioncode)) {
                return AppBaseController::responseError(26, trans('words.update_app_msg'));
            }
            if (CommonController::checkKeyExist($key) == 0) {
                return AppBaseController::responseError(0, trans('words.incorrect_key'));
            } elseif (CommonController::checkKeyTokenExist($key, $token) == 0) {
                return AppBaseController::responseError(0, trans('words.incorrect_token'));
            }

            $valid = AppBaseController::requiredValidation([
                'user_id' => @$user_id,

            ]);
            if ($valid != '') {
                $msg = trans('words.please_enter') . $valid;
                return $this->responseError(0, $msg);
            }

            $chkUserExist = User_Master::where('user_id', $user_id)->first();
            if (empty($chkUserExist)) {
                return AppBaseController::responseError(0, trans('words.invalid_user_id'));
            }
            if ($chkUserExist->status == '1') {
                return AppBaseController::responseError(5, trans('words.account_deactive'));
            }

            $getMusicList = Music_Master::where('media_url','!=','')->get();

            if (isset($search_text) && $search_text != '') {
                $getMusicList = Music_Master::where('media_url','!=','')->where('subtitle', 'LIKE', '%' . $search_text . '%')->get();
            }

            foreach ($getMusicList as $value) {
                $getMusicDtl = Music_Likes_Master::where('music_id', $value->music_id)->where('user_id', $user_id)->first();
                if (!empty($getMusicDtl)) {
                    $value->is_favorite = '1';
                } else {
                    $value->is_favorite = '0';
                }
            }

            $data['data'] = $getMusicList;

            return AppBaseController::successResponse($data, 1, trans('words.list_success'), "True");
        } catch (\Exception $e) {
            return $this->responseError(0, $e->getMessage());
        }
    }

    public function createStory(Request $request)
    {
        try {
            extract($request->all());
            $key = $request->header('key');

            $token = $request->header('token');
            $valid = AppBaseController::requiredValidation([
                'key' => @$key,
                'token' => @$token,
            ]);

            if ($valid != '') {
                $msg = trans('words.please_enter') . $valid;
                return $this->responseError(0, $msg);
            }

            $devicetype = empty($_SERVER["HTTP_DEVICETYPE"]) ? "" : $_SERVER["HTTP_DEVICETYPE"];
            $versioncode = empty($_SERVER["HTTP_VERSIONCODE"]) ? "" : $_SERVER["HTTP_VERSIONCODE"];
            if (CommonController::versionCheck($devicetype, $versioncode)) {
                return AppBaseController::responseError(26, trans('words.update_app_msg'));
            }
            if (CommonController::checkKeyExist($key) == 0) {
                return AppBaseController::responseError(0, trans('words.incorrect_key'));
            } elseif (CommonController::checkKeyTokenExist($key, $token) == 0) {
                return AppBaseController::responseError(0, trans('words.incorrect_token'));
            }

            $valid = AppBaseController::requiredValidation([
                'user_id' => @$user_id,
                'media' => @$media,
            ]);

            if ($valid != '') {
                $msg = trans('words.please_enter') . $valid;
                return $this->responseError(0, $msg);
            }

            $chkUserExist = User_Master::where('user_id', $user_id)->first();
            
            if (empty($chkUserExist)) {
                return AppBaseController::responseError(0, trans('words.invalid_user_id'));
            }
            if ($chkUserExist->status == '1') {
                return AppBaseController::responseError(5, trans('words.account_deactive'));
            }
             

             if (isset($request['media'])) {
                 $checkStory = Story_Master::where(['user_id' => $user_id])->first();

                if (empty($checkStory)) {
                    $storyId = Story_Master::create(['user_id' => $user_id]);
                    $story_id = $storyId->id;
                } else {
                    $story_id = $checkStory->story_id;
                }

                foreach ($request['media'] as $k => $media_file) {
                    $time = time() . uniqid();
                    $newname = $time . '.' . $media_file->getClientOriginalExtension();
                    $media_type = explode("/", $media_file->getMimeType())[0];
                    $media_file->move(public_path('story'), $newname);

                    $insertMediaObj = [
                        'story_id' => $story_id,
                        'media' => $newname,
                        'media_type' => $media_type,
                    ];

                    $createStory = Story_Media_Master::create($insertMediaObj);
                }

                $mediaUserDtl = User_Media_Master::where('user_id', $user_id)->first();

                $checkStory = Story_Master::where(['user_id' => $user_id])->first();
                $userDtls = [];
                $userDtls['user_id'] = $user_id;
                if (!empty($mediaUserDtl)) {
                    $userDtls['profile'] = $mediaUserDtl->profile;
                } else {
                    $userDtls['profile'] = '';
                }
                $userDtls['date_time'] = $this->getTime($checkStory->created_at);

                $storyDtls = Story_Media_Master::where(['story_id' => $checkStory->story_id])->get();
                foreach ($storyDtls as $value) {
                    $value['date_time'] = $this->getTime($checkStory->created_at);
                }

                $userDtls['story_media'] = $storyDtls;


                $data['data'] = $userDtls;

                return AppBaseController::successResponse($data, 1, trans('words.create_story'), "True");
             }

        } catch (\Exception $e) {
            return $this->responseError(0, $e->getMessage());
        }
    }

    public function createFeed(Request $request)
    {
        try {
            extract($request->all());
            $key = $request->header('key');

            $token = $request->header('token');
            $valid = AppBaseController::requiredValidation([
                'key' => @$key,
                'token' => @$token,
            ]);
            if ($valid != '') {
                $msg = trans('words.please_enter') . $valid;
                return $this->responseError(0, $msg);
            }

            $devicetype = empty($_SERVER["HTTP_DEVICETYPE"]) ? "" : $_SERVER["HTTP_DEVICETYPE"];
            $versioncode = empty($_SERVER["HTTP_VERSIONCODE"]) ? "" : $_SERVER["HTTP_VERSIONCODE"];
            if (CommonController::versionCheck($devicetype, $versioncode)) {
                return AppBaseController::responseError(26, trans('words.update_app_msg'));
            }
            if (CommonController::checkKeyExist($key) == 0) {
                return AppBaseController::responseError(0, trans('words.incorrect_key'));
            } elseif (CommonController::checkKeyTokenExist($key, $token) == 0) {
                return AppBaseController::responseError(0, trans('words.incorrect_token'));
            }

            $valid = AppBaseController::requiredValidation([
                'user_id' => @$user_id,
               // 'description' => @$description,
                'feed_image' => @$feed_image,
            ]);

            if ($valid != '') {
                $msg = trans('words.please_enter') . $valid;
                return $this->responseError(0, $msg);
            }

            $chkUserExist = User_Master::where('user_id', $user_id)->first();
            if (empty($chkUserExist)) {
                return AppBaseController::responseError(0, trans('words.invalid_user_id'));
            }
            if ($chkUserExist->status == '1') {
                return AppBaseController::responseError(5, trans('words.account_deactive'));
            }

            $media_file = $request['feed_image'];
            $time = time() . uniqid();
            $newname = $time . '.' . $media_file->getClientOriginalExtension();
            $media_type = explode("/", $media_file->getMimeType())[0];

            $media_file->move(public_path('story'), $newname);

            $insertMediaObj = [
                'user_id' => @$user_id,
                'description' => @$description ?? '',
                'feed_image' => @$newname,
                'media_type' => @$media_type,
            ];

            Feed_Master::create($insertMediaObj);

            return AppBaseController::responseError(1, trans('words.feed_create'), "True");


        } catch (\Exception $e) {
            return $this->responseError(0, $e->getMessage());
        }
    }

  
    public function deleteStory(Request $request)
    {
        try {
            extract($request->all());
            $key = $request->header('key');

            $token = $request->header('token');
            $valid = AppBaseController::requiredValidation([
                'key' => @$key,
                'token' => @$token,
            ]);

            if ($valid != '') {
                $msg = trans('words.please_enter') . $valid;
                return $this->responseError(0, $msg);
            }

            $devicetype = empty($_SERVER["HTTP_DEVICETYPE"]) ? "" : $_SERVER["HTTP_DEVICETYPE"];
            $versioncode = empty($_SERVER["HTTP_VERSIONCODE"]) ? "" : $_SERVER["HTTP_VERSIONCODE"];
            if (CommonController::versionCheck($devicetype, $versioncode)) {
                return AppBaseController::responseError(26, trans('words.update_app_msg'));
            }
            if (CommonController::checkKeyExist($key) == 0) {
                return AppBaseController::responseError(0, trans('words.incorrect_key'));
            } elseif (CommonController::checkKeyTokenExist($key, $token) == 0) {
                return AppBaseController::responseError(0, trans('words.incorrect_token'));
            }

            $valid = AppBaseController::requiredValidation([
                'user_id' => @$user_id,
                'story_id' => @$story_id,
                'story_media_id' => @$story_media_id,

            ]);
            if ($valid != '') {
                $msg = trans('words.please_enter') . $valid;
                return $this->responseError(0, $msg);
            }


            $userExist = User_Master::where('user_id', $user_id)->first();
            if (empty($userExist)) {
                return AppBaseController::responseError(0, trans('words.invalid_user_id'));
            }
            if ($userExist->status == '1') {
                return AppBaseController::responseError(5, trans('words.account_deactive'));
            }

            $storyExist = Story_Master::where('story_id', $story_id)->first();
            if (empty($storyExist)) {
                return AppBaseController::responseError(0, trans('words.story_not_exist'));
            }
            
            $storyMediaExist = Story_Media_Master::where('story_media_id', $story_media_id)->first();
            if (empty($storyMediaExist)) {
                return AppBaseController::responseError(0, trans('words.story_media_not_exist'));
            }

            $rootPath = str_replace('\\', '/', __DIR__);
            $homePage = explode("/app", $rootPath)[0] . '/public/story/';
            $mediaName = basename($storyMediaExist->media);
            if (file_exists($homePage . $mediaName)) {
                unlink($homePage . $mediaName);
            }
            

            Story_Media_Master::where('story_media_id', $story_media_id)->delete();
            $checkStory = Story_Media_Master::where('story_id', $story_id)->get();
            if(count($checkStory) < 1){
               Story_Master::where('story_id', $story_id)->delete();
            }

            return AppBaseController::responseSuccess(1, trans('words.delete_story'), "True");

        } catch (\Exception $e) {
            return $this->responseError(0, $e->getMessage());
        }
    }

    public function musicLikeDislike(Request $request)
    {
        try {
            extract($request->all());
            $key = $request->header('key');

            $token = $request->header('token');
            $valid = AppBaseController::requiredValidation([
                'key' => @$key,
                'token' => @$token,
            ]);

            if ($valid != '') {
                $msg = trans('words.please_enter') . $valid;
                return $this->responseError(0, $msg);
            }

            $devicetype = empty($_SERVER["HTTP_DEVICETYPE"]) ? "" : $_SERVER["HTTP_DEVICETYPE"];
            $versioncode = empty($_SERVER["HTTP_VERSIONCODE"]) ? "" : $_SERVER["HTTP_VERSIONCODE"];
            if (CommonController::versionCheck($devicetype, $versioncode)) {
                return AppBaseController::responseError(26, trans('words.update_app_msg'));
            }
            if (CommonController::checkKeyExist($key) == 0) {
                return AppBaseController::responseError(0, trans('words.incorrect_key'));
            } elseif (CommonController::checkKeyTokenExist($key, $token) == 0) {
                return AppBaseController::responseError(0, trans('words.incorrect_token'));
            }

            $valid = AppBaseController::requiredValidation([
                'user_id' => @$user_id,
                'music_id' => @$music_id,

            ]);

            if ($valid != '') {
                $msg = trans('words.please_enter') . $valid;
                return $this->responseError(0, $msg);
            }

            $chkUserExist = User_Master::where('user_id', $user_id)->first();
            if (empty($chkUserExist)) {
                return AppBaseController::responseError(0, trans('words.invalid_user_id'));
            }
            if ($chkUserExist->status == '1') {
                return AppBaseController::responseError(5, trans('words.account_deactive'));
            }

            $likeCheck = Music_Likes_Master::where(['user_id' => $user_id, 'music_id' => $music_id])->first();
            if (!empty($likeCheck)) {
                Music_Likes_Master::where(['user_id' => $user_id, 'music_id' => $music_id])->delete();
                return AppBaseController::responseSuccess(1, trans('words.unlike_success'), "True");
            }
            if ($music_type != '') {
                Music_Likes_Master::create(['user_id' => $user_id, 'music_id' => $music_id, 'music_type' => $music_type]);
            }


            return AppBaseController::responseSuccess(1, trans('words.fav_like_success'), "True");
        } catch (\Exception $e) {
            return $this->responseError(0, $e->getMessage());
        }
    }

    public function getFavoriteMusic(Request $request)
    {
        try {
            extract($request->all());
            $key = $request->header('key');

            $token = $request->header('token');
            $valid = AppBaseController::requiredValidation([
                'key' => @$key,
                'token' => @$token,
            ]);

            if ($valid != '') {
                $msg = trans('words.please_enter') . $valid;
                return $this->responseError(0, $msg);
            }

            $devicetype = empty($_SERVER["HTTP_DEVICETYPE"]) ? "" : $_SERVER["HTTP_DEVICETYPE"];
            $versioncode = empty($_SERVER["HTTP_VERSIONCODE"]) ? "" : $_SERVER["HTTP_VERSIONCODE"];
            if (CommonController::versionCheck($devicetype, $versioncode)) {
                return AppBaseController::responseError(26, trans('words.update_app_msg'));
            }
            if (CommonController::checkKeyExist($key) == 0) {
                return AppBaseController::responseError(0, trans('words.incorrect_key'));
            } elseif (CommonController::checkKeyTokenExist($key, $token) == 0) {
                return AppBaseController::responseError(0, trans('words.incorrect_token'));
            }

            $valid = AppBaseController::requiredValidation([
                'user_id' => @$user_id,
                'music_type' => @$music_type,

            ]);
            if ($valid != '') {
                $msg = trans('words.please_enter') . $valid;
                return $this->responseError(0, $msg);
            }

            $chkUserExist = User_Master::where('user_id', $user_id)->first();
            if (empty($chkUserExist)) {
                return AppBaseController::responseError(0, trans('words.invalid_user_id'));
            }
            if ($chkUserExist->status == '1') {
                return AppBaseController::responseError(5, trans('words.account_deactive'));
            }

            $musicArr = Music_Likes_Master::where('user_id', $user_id)->where('music_type', $music_type)->pluck('music_id')->toArray();
            $getMusicList = Music_Master::whereIn('music_id', $musicArr);
            if (isset($search_text) && $search_text != '') {
                $getMusicList = $getMusicList->where('title', 'LIKE', '%' . $search_text . '%');
            }
            $getMusicList = $getMusicList->get();

            $data['data'] = $getMusicList;

            return AppBaseController::successResponse($data, 1, trans('words.list_success'), "True");
        } catch (\Exception $e) {
            return $this->responseError(0, $e->getMessage());
        }

    }

    public function profileLikeDislike(Request $request)
    {
        try {
            extract($request->all());
            $key = $request->header('key');

            $token = $request->header('token');
            $valid = AppBaseController::requiredValidation([
                'key' => @$key,
                'token' => @$token,
            ]);

            if ($valid != '') {
                $msg = trans('words.please_enter') . $valid;
                return $this->responseError(0, $msg);
            }

            $devicetype = empty($_SERVER["HTTP_DEVICETYPE"]) ? "" : $_SERVER["HTTP_DEVICETYPE"];
            $versioncode = empty($_SERVER["HTTP_VERSIONCODE"]) ? "" : $_SERVER["HTTP_VERSIONCODE"];
            if (CommonController::versionCheck($devicetype, $versioncode)) {
                return AppBaseController::responseError(26, trans('words.update_app_msg'));
            }
            if (CommonController::checkKeyExist($key) == 0) {
                return AppBaseController::responseError(0, trans('words.incorrect_key'));
            } elseif (CommonController::checkKeyTokenExist($key, $token) == 0) {
                return AppBaseController::responseError(0, trans('words.incorrect_token'));
            }

            $valid = AppBaseController::requiredValidation([
                'user_id' => @$user_id,
                'like_user_id' => @$like_user_id,
                'like_type' => @$like_type,
            ]);

            if ($valid != '') {
                $msg = trans('words.please_enter') . $valid;
                return $this->responseError(0, $msg);
            }

            if (@$like_type != '1' && @$like_type != '2') {
                return AppBaseController::responseError(0, trans('words.invalid_like_type'));
            }

            $chkUserExist = User_Master::where('user_id', $user_id)->first();
            if (empty($chkUserExist)) {
                return AppBaseController::responseError(0, trans('words.invalid_user_id'));
            }
            if ($chkUserExist->status == '1') {
                return AppBaseController::responseError(5, trans('words.account_deactive'));
            }

            if ($user_id == $like_user_id) {
                return AppBaseController::responseError(0, trans('words.own_user_id'));
            }

            $likeUserDtl = User_Master::where('user_id', $like_user_id)->first();
            if (empty($likeUserDtl)) {
                return AppBaseController::responseError(0, trans('words.invalid_user_id'));
            }

            // Likes_Master::updateOrCreate([
            //     'user_id' => $user_id,
            //     'like_user_id' => $like_user_id
            // ], [
            //     'like_type' => $like_type
            // ]);

            $comment = $request->comment ?? "";
            $message = $request->message ?? "";
            
            $checkLike = Likes_Master::where(['user_id' => $user_id,'like_user_id' => $like_user_id])->first();
            if(empty($checkLike)){
                $checkMatch = Likes_Master::where(['user_id' => $like_user_id,'like_user_id' => $user_id])->first();
                if(empty($checkMatch)){
                    //TODO: send notification
                    Likes_Master::create(['user_id' => $user_id,'like_user_id' => $like_user_id,'like_type' => $like_type,'comment' => $comment]);
                }else{
                    //TODO: send message with message param
                    Likes_Master::create(['user_id' => $user_id,'like_user_id' => $like_user_id,'like_type' => $like_type]);
                }
            }else{
                Likes_Master::where(['user_id' => $user_id,'like_user_id' => $like_user_id])->update(['like_type' => $like_type]);
            }


            if ($like_type == '1') {
                if ($likeUserDtl->device_token != '') {
                    if($chkUserExist && $chkUserExist->name){
                        $top = $chkUserExist->name." "."Like your profile";
                    }else{
                        $top = " "."Like your profile";
                    }

                     if($comment){
                        CommonController::sendPush($top, $comment, $likeUserDtl->device_token);
                     }else{
                        CommonController::sendPush($chkUserExist->name ?? '', "Like your profile", $likeUserDtl->device_token);
                     }
                    
                }
                
                Notification_Master::create(['user_id' => $like_user_id, 'other_id' => $user_id, 'message' => 'liked your profile']);
                $likeChatCheck = Likes_Master::where(['user_id' => $user_id])->where('like_user_id', $like_user_id)->where('like_type', '1')->first();
                $friendLikeCheck = Likes_Master::where(['user_id' => $like_user_id])->where('like_user_id', $user_id)->where('like_type', '1')->first();

                if (!empty($likeChatCheck) && !empty($friendLikeCheck)) {

                    $userDtls = User_Master::select('user_id', 'name', 'dob', 'created_at', 'device_token', 'device_type')->where('user_id', $like_user_id)->first();
                    $userMediaDtls = User_Media_Master::where('user_id', $like_user_id)->first();
                    AppBaseController::createChatFirebase($user_id, $like_user_id, $userMediaDtls->profile, $userDtls->name, $userDtls->device_token, $userDtls->device_type,$message);
                    Notification_Master::create(['user_id' => $like_user_id, 'other_id' => $user_id, 'message' => 'Profile matched successfully']); 

                    

                    $userChatDtls = User_Master::select('user_id', 'name', 'dob', 'created_at', 'device_token', 'device_type')->where('user_id', $user_id)->first();
                    //echo "<pre>";print($userChatDtls);die;
                    $userProfileDtl = User_Media_Master::where('user_id', $user_id)->first();
                    AppBaseController::createChatFirebase($like_user_id, $user_id, $userProfileDtl->profile, $userChatDtls->name, $userChatDtls->device_token, $userChatDtls->device_type, $message);
                    Notification_Master::create(['user_id' => $user_id, 'other_id' => $like_user_id, 'message' => 'Profile matched successfully']);
                    if($message){

                        AppBaseController::createOneToOneChatFirebase($like_user_id, $user_id, $userProfileDtl->profile, $userChatDtls->name, $userChatDtls->device_token, $userChatDtls->device_type, $message);    
                    }
                    

                    $userDetails = User_Master::select('user_id', 'name', 'dob', 'created_at', 'device_token', 'device_type')->where('user_id', $user_id)->first();
                    $likeUserDetails = User_Master::select('user_id', 'name', 'dob', 'created_at', 'device_token', 'device_type')->where('user_id', $like_user_id)->first();
                    if ($userDetails->device_token != '') {
                        CommonController::sendPush($likeUserDetails->name ?? '', "Profile matched successfully", $userDetails->device_token,'1');
                    }    
                    if ($likeUserDetails->device_token != '') {
                        CommonController::sendPush($userDetails->name ?? '', "Profile matched successfully", $likeUserDetails->device_token,'1');
                    }
                   
                }

            } else {
                $likeChatCheck = Likes_Master::where(['user_id' => $user_id])->where('like_user_id', $like_user_id)->where('like_type', '2')->first();
                $friendLikeCheck = Likes_Master::where(['user_id' => $like_user_id])->where('like_user_id', $user_id)->where('like_type', '2')->first();
                if ($likeChatCheck || $friendLikeCheck) {
                    AppBaseController::removeChatFirebase($user_id, $like_user_id);
                    AppBaseController::removeChatFirebase($like_user_id, $user_id);
                }
            }
            if ($like_type == '2') {
                return AppBaseController::responseSuccess(1, trans('words.dislike_success'), "True");
            } else {
                return AppBaseController::responseSuccess(1, trans('words.like_success'), "True");
            }
        } catch (\Exception $e) {
            return $this->responseError(0, $e->getMessage());
        }
    }

    public function getMyViewStory(Request $request)
    {
        try {
            extract($request->all());
            $key = $request->header('key');

            $token = $request->header('token');
            $valid = AppBaseController::requiredValidation([
                'key' => @$key,
                'token' => @$token,
            ]);

            if ($valid != '') {
                $msg = trans('words.please_enter') . $valid;
                return $this->responseError(0, $msg);
            }

            $devicetype = empty($_SERVER["HTTP_DEVICETYPE"]) ? "" : $_SERVER["HTTP_DEVICETYPE"];
            $versioncode = empty($_SERVER["HTTP_VERSIONCODE"]) ? "" : $_SERVER["HTTP_VERSIONCODE"];
            if (CommonController::versionCheck($devicetype, $versioncode)) {
                return AppBaseController::responseError(26, trans('words.update_app_msg'));
            }
            if (CommonController::checkKeyExist($key) == 0) {
                return AppBaseController::responseError(0, trans('words.incorrect_key'));
            } elseif (CommonController::checkKeyTokenExist($key, $token) == 0) {
                return AppBaseController::responseError(0, trans('words.incorrect_token'));
            }

            $valid = AppBaseController::requiredValidation([
                'user_id' => @$user_id,
            ]);

            if ($valid != '') {
                $msg = trans('words.please_enter') . $valid;
                return $this->responseError(0, $msg);
            }

            $chkUserExist = User_Master::where('user_id', $user_id)->first();
            if (empty($chkUserExist)) {
                return AppBaseController::responseError(0, trans('words.invalid_user_id'));
            }
            if ($chkUserExist->status == '1') {
                return AppBaseController::responseError(5, trans('words.account_deactive'));
            }

            $viewStory = ViewStory::where(['view_user_id' => $user_id])->where('user_id', '!=', $user_id)->get();

            foreach ($viewStory as $key => $viewUserDtls) {
                // dd($viewUserDtls->user_id);
                $userDtl = User_Master::where('user_id', $viewUserDtls->user_id)->first();
                $mediaUserDtl = User_Media_Master::where('user_id', $viewUserDtls->user_id)->first();

                $viewUserDtls['date_time'] = $this->getTime($viewUserDtls->created_at);
                $viewUserDtls['name'] = $userDtl->name ?? '';
                $viewUserDtls['job_title'] = $userDtl->job_title ?? '';
                $viewUserDtls['view_user_id'] = $viewUserDtls->user_id;
                $viewUserDtls['user_id'] = $viewUserDtls->view_user_id;

                $dob = $userDtl->dob ?? '';
                $condate = date("Y-m-d");
                $birthdate = new DateTime(date("Y-m-d", strtotime(implode('-', array_reverse(explode('/', $dob))))));
                $today = new DateTime(date("Y-m-d", strtotime(implode('-', array_reverse(explode('/', $condate))))));
                $age = $birthdate->diff($today)->y;
                $viewUserDtls['age'] = (string)$age;
                if (!empty($mediaUserDtl)) {
                    $viewUserDtls['profile'] = $mediaUserDtl->profile;
                } else {
                    $viewUserDtls['profile'] = '';
                }
            }

            $viewStoryCount = ViewStory::where(['view_user_id' => $user_id])->count();
            $data['data'] = $viewStory;
            $data['count'] = $viewStoryCount;

            return AppBaseController::successResponse($data, 1, trans('words.list_success'), "True");
        } catch (\Exception $e) {
            return $this->responseError(0, $e->getMessage());
        }
    }

    public function getStoryDetails(Request $request)
    {
        try {
            extract($request->all());
            $key = $request->header('key');

            $token = $request->header('token');
            $valid = AppBaseController::requiredValidation([
                'key' => @$key,
                'token' => @$token,
            ]);

            if ($valid != '') {
                $msg = trans('words.please_enter') . $valid;
                return $this->responseError(0, $msg);
            }

            $devicetype = empty($_SERVER["HTTP_DEVICETYPE"]) ? "" : $_SERVER["HTTP_DEVICETYPE"];
            $versioncode = empty($_SERVER["HTTP_VERSIONCODE"]) ? "" : $_SERVER["HTTP_VERSIONCODE"];
            if (CommonController::versionCheck($devicetype, $versioncode)) {
                return AppBaseController::responseError(26, trans('words.update_app_msg'));
            }
            if (CommonController::checkKeyExist($key) == 0) {
                return AppBaseController::responseError(0, trans('words.incorrect_key'));
            } elseif (CommonController::checkKeyTokenExist($key, $token) == 0) {
                return AppBaseController::responseError(0, trans('words.incorrect_token'));
            }

            $valid = AppBaseController::requiredValidation([
                'user_id' => @$user_id,
                'view_user_id' => @$view_user_id,
            ]);

            if ($valid != '') {
                $msg = trans('words.please_enter') . $valid;
                return $this->responseError(0, $msg);
            }

            $chkUserExist = User_Master::where('user_id', $user_id)->first();
            if (empty($chkUserExist)) {
                return AppBaseController::responseError(0, trans('words.invalid_user_id'));
            }
            if ($chkUserExist->status == '1') {
                return AppBaseController::responseError(5, trans('words.account_deactive'));
            }

            $chkUserExists = User_Master::where('user_id', $view_user_id)->first();
            if (empty($chkUserExists)) {
                return AppBaseController::responseError(0, trans('words.invalid_user_id'));
            }

            if ($user_id != $view_user_id) {
                $checkStory = ViewStory::where(['user_id' => $user_id, 'view_user_id' => $view_user_id])->first();
                if (empty($checkStory)) {
                    ViewStory::create(['user_id' => $user_id, 'view_user_id' => $view_user_id]);
                }
            }

            $storyDtl = Story_Master::with('story_media_detail')->where('user_id', $view_user_id)->get();

            foreach ($storyDtl as $value) {
                if($view_user_id == $user_id){
                    $viewStoryCount = ViewStory::where(['view_user_id' => $user_id])->count();
                }else{
                    $viewStoryCount = ViewStory::where(['view_user_id' => $view_user_id])->count();
                }
                $value->count = $viewStoryCount;
                foreach ($value->story_media_detail as $storyDtls) {
                    $storyDtls['date_time'] = $this->getTime($storyDtls->created_at);
                }
            }

            $data['data'] = $storyDtl;
            return AppBaseController::successResponse($data, 1, trans('words.list_success'), "True");
        } catch (\Exception $e) {
            return $this->responseError(0, $e->getMessage());
        }
    }

    public function createViewProfile(Request $request)
    {
        try {
            extract($request->all());
            $key = $request->header('key');

            $token = $request->header('token');
            $valid = AppBaseController::requiredValidation([
                'key' => @$key,
                'token' => @$token,
            ]);

            if ($valid != '') {
                $msg = trans('words.please_enter') . $valid;
                return $this->responseError(0, $msg);
            }

            $devicetype = empty($_SERVER["HTTP_DEVICETYPE"]) ? "" : $_SERVER["HTTP_DEVICETYPE"];
            $versioncode = empty($_SERVER["HTTP_VERSIONCODE"]) ? "" : $_SERVER["HTTP_VERSIONCODE"];
            if (CommonController::versionCheck($devicetype, $versioncode)) {
                return AppBaseController::responseError(26, trans('words.update_app_msg'));
            }
            if (CommonController::checkKeyExist($key) == 0) {
                return AppBaseController::responseError(0, trans('words.incorrect_key'));
            } elseif (CommonController::checkKeyTokenExist($key, $token) == 0) {
                return AppBaseController::responseError(0, trans('words.incorrect_token'));
            }

            $valid = AppBaseController::requiredValidation([
                'user_id' => @$user_id,
                'view_user_id' => @$view_user_id,
            ]);

            if ($valid != '') {
                $msg = trans('words.please_enter') . $valid;
                return $this->responseError(0, $msg);
            }

            $chkUserExist = User_Master::where('user_id', $user_id)->first();
            if (empty($chkUserExist)) {
                return AppBaseController::responseError(0, trans('words.invalid_user_id'));
            }
            if ($chkUserExist->status == '1') {
                return AppBaseController::responseError(5, trans('words.account_deactive'));
            }


            $chkUserExists = User_Master::where('user_id', $view_user_id)->first();
            if (empty($chkUserExists)) {
                return AppBaseController::responseError(0, trans('words.invalid_user_id'));
            }
            if ($user_id == $view_user_id) {
                return AppBaseController::responseError(0, trans('words.own_story_user_id'));
            }
            $checkProfile = ViewProfile::where(['user_id' => $user_id, 'view_user_id' => $view_user_id])->first();
            if (!empty($checkProfile)) {
                return AppBaseController::responseError(0, trans('words.already_view_profile'));
            }
            ViewProfile::create(['user_id' => $user_id, 'view_user_id' => $view_user_id]);
            Notification_Master::create(['user_id' => $view_user_id, 'other_id' => $user_id, 'message' => 'your profile view']);
            if ($chkUserExists->device_token != '') {
                CommonController::sendPush($chkUserExists->name ?? '', "your profile view", $chkUserExists->device_token);
            }
            return AppBaseController::responseSuccess(1, trans('words.view_profile'), "True");
        } catch (\Exception $e) {
            return $this->responseError(0, $e->getMessage());
        }
    }

    public function getChatList(Request $request)
    {
        try {
            extract($request->all());
            $key = $request->header('key');

            $token = $request->header('token');
            $valid = AppBaseController::requiredValidation([
                'key' => @$key,
                'token' => @$token,
            ]);

            if ($valid != '') {
                $msg = trans('words.please_enter') . $valid;
                return $this->responseError(0, $msg);
            }

            $devicetype = empty($_SERVER["HTTP_DEVICETYPE"]) ? "" : $_SERVER["HTTP_DEVICETYPE"];
            $versioncode = empty($_SERVER["HTTP_VERSIONCODE"]) ? "" : $_SERVER["HTTP_VERSIONCODE"];
            if (CommonController::versionCheck($devicetype, $versioncode)) {
                return AppBaseController::responseError(26, trans('words.update_app_msg'));
            }
            if (CommonController::checkKeyExist($key) == 0) {
                return AppBaseController::responseError(0, trans('words.incorrect_key'));
            } elseif (CommonController::checkKeyTokenExist($key, $token) == 0) {
                return AppBaseController::responseError(0, trans('words.incorrect_token'));
            }

            $valid = AppBaseController::requiredValidation([
                'user_id' => @$user_id,
            ]);

            if ($valid != '') {
                $msg = trans('words.please_enter') . $valid;
                return $this->responseError(0, $msg);
            }

            $chkUserExist = User_Master::where('user_id', $user_id)->first();
            if (empty($chkUserExist)) {
                return AppBaseController::responseError(0, trans('words.invalid_user_id'));
            }
            if ($chkUserExist->status == '1') {
                return AppBaseController::responseError(5, trans('words.account_deactive'));
            }

            $likeCheck = Likes_Master::where(['user_id' => $user_id])->where('like_type', '1')->pluck('like_user_id')->toArray();

            $likeCheckArr = Likes_Master::whereIn('user_id', $likeCheck)->where(['like_user_id' => $user_id])->where('like_type', '1')->pluck('user_id')->toArray();

            if (isset($search_text) && $search_text != '') {
                $userDtls = User_Master::select('user_id', 'name', 'dob', 'created_at')->whereIn('user_id', $likeCheckArr)->where('name', 'LIKE', "%$search_text%")->get();
            } else {
                $userDtls = User_Master::select('user_id', 'name', 'dob', 'created_at')->whereIn('user_id', $likeCheckArr)->get();
            }

            foreach ($userDtls as $key => $userDtl) {
                $mediaUserDtl = User_Media_Master::where('user_id', $userDtl->user_id)->first();

                $dob = $userDtl->dob;
                $condate = date("Y-m-d");
                $birthdate = new DateTime(date("Y-m-d", strtotime(implode('-', array_reverse(explode('/', $dob))))));
                $today = new DateTime(date("Y-m-d", strtotime(implode('-', array_reverse(explode('/', $condate))))));
                $age = $birthdate->diff($today)->y;
                $userDtl['age'] = (string)$age;

                if (!empty($mediaUserDtl)) {
                    $userDtl['profile'] = $mediaUserDtl->profile;
                } else {
                    $userDtl['profile'] = '';
                }
                $userDtl['date_time'] = $this->getTime($userDtl->created_at);
            }


            if (isset($search_text) && $search_text != '') {
                $likeUserArr = Likes_Master::where('user_id', $user_id)->where('like_type', '1')->pluck('like_user_id')->toArray();

                $likeUserDtlArr = User_Master::whereIn('user_id', $likeUserArr)->where('name', 'LIKE', "%$search_text%")->pluck('user_id')->toArray();

                $likeDetails = Likes_Master::whereIn('user_id', $likeUserDtlArr)->where('like_user_id', $user_id)->where('like_type', '1')->get();
            } else {
                $likeDetails = Likes_Master::where('like_user_id', $user_id)->where('like_type', '1')->get();
            }


            foreach ($likeDetails as $key => $value) {
                $userDtl = User_Master::where('user_id', $value->user_id)->first();
                $mediaUserDtl = User_Media_Master::where('user_id', $value->user_id)->first();

                $value['date_time'] = $this->getTime($value->created_at);
                $value['name'] = $userDtl->name;
                $value['job_title'] = $userDtl->job_title;
                $value['like_user_id'] = $value->user_id;
                $value['user_id'] = $value->like_user_id;

                $dob = $userDtl->dob;
                $condate = date("Y-m-d");
                $birthdate = new DateTime(date("Y-m-d", strtotime(implode('-', array_reverse(explode('/', $dob))))));
                $today = new DateTime(date("Y-m-d", strtotime(implode('-', array_reverse(explode('/', $condate))))));
                $age = $birthdate->diff($today)->y;
                $value['age'] = (string)$age;
                if (!empty($mediaUserDtl)) {
                    $value['profile'] = $mediaUserDtl->profile;
                } else {
                    $value['profile'] = '';
                }
            }


            if (isset($search_text) && $search_text != '') {
                $viewUserArr = ViewProfile::where('view_user_id', $user_id)->pluck('user_id')->toArray();
                $getUserDtlArr = User_Master::whereIn('user_id', $viewUserArr)->where('name', 'LIKE', "%$search_text%")->pluck('user_id')->toArray();
                $viewProfile = ViewProfile::where('view_user_id', $user_id)->whereIn('user_id', $getUserDtlArr)->get();
            } else {
                $viewProfile = ViewProfile::where('view_user_id', $user_id)->get();
            }

            foreach ($viewProfile as $key => $viewUserDtls) {
                $userDtl = User_Master::where('user_id', $viewUserDtls->user_id)->first();
                $mediaUserDtl = User_Media_Master::where('user_id', $viewUserDtls->user_id)->first();

                $viewUserDtls['date_time'] = $this->getTime($viewUserDtls->created_at);
                $viewUserDtls['name'] = $userDtl->name;
                $viewUserDtls['job_title'] = $userDtl->job_title;
                $viewUserDtls['view_user_id'] = $viewUserDtls->user_id;
                $viewUserDtls['user_id'] = $viewUserDtls->view_user_id;

                $dob = $userDtl->dob;
                $condate = date("Y-m-d");
                $birthdate = new DateTime(date("Y-m-d", strtotime(implode('-', array_reverse(explode('/', $dob))))));
                $today = new DateTime(date("Y-m-d", strtotime(implode('-', array_reverse(explode('/', $condate))))));
                $age = $birthdate->diff($today)->y;
                $viewUserDtls['age'] = (string)$age;
                if (!empty($mediaUserDtl)) {
                    $viewUserDtls['profile'] = $mediaUserDtl->profile;
                } else {
                    $viewUserDtls['profile'] = '';
                }
            }

            $data['data'] = $userDtls;
            $data['view_profile'] = $viewProfile;
            $data['like_details'] = $likeDetails;

            return AppBaseController::successResponse($data, 1, trans('words.list_success'), "True");
        } catch (\Exception $e) {
            return $this->responseError(0, $e->getMessage());
        }
    }


    public function getNotificationList(Request $request)
    {
        try {
            extract($request->all());
            $key = $request->header('key');

            $token = $request->header('token');
            $valid = AppBaseController::requiredValidation([
                'key' => @$key,
                'token' => @$token,
            ]);

            if ($valid != '') {
                $msg = trans('words.please_enter') . $valid;
                return $this->responseError(0, $msg);
            }

            $devicetype = empty($_SERVER["HTTP_DEVICETYPE"]) ? "" : $_SERVER["HTTP_DEVICETYPE"];
            $versioncode = empty($_SERVER["HTTP_VERSIONCODE"]) ? "" : $_SERVER["HTTP_VERSIONCODE"];
            if (CommonController::versionCheck($devicetype, $versioncode)) {
                return AppBaseController::responseError(26, trans('words.update_app_msg'));
            }
            if (CommonController::checkKeyExist($key) == 0) {
                return AppBaseController::responseError(0, trans('words.incorrect_key'));
            } elseif (CommonController::checkKeyTokenExist($key, $token) == 0) {
                return AppBaseController::responseError(0, trans('words.incorrect_token'));
            }

            $valid = AppBaseController::requiredValidation([
                'user_id' => @$user_id,
            ]);

            if ($valid != '') {
                $msg = trans('words.please_enter') . $valid;
                return $this->responseError(0, $msg);
            }

            $chkUserExist = User_Master::where('user_id', $user_id)->first();
            if (empty($chkUserExist)) {
                return AppBaseController::responseError(0, trans('words.invalid_user_id'));
            }
            if ($chkUserExist->status == '1') {
                return AppBaseController::responseError(5, trans('words.account_deactive'));
            }

            $notificationDtls = Notification_Master::where('user_id', $user_id)->get();
            foreach ($notificationDtls as $key => $value) {
                $value['date_time'] = $this->getTime($value->created_at);
            }

            $data['data'] = $notificationDtls;

            return AppBaseController::successResponse($data, 1, trans('words.list_success'), "True");
        } catch (\Exception $e) {
            return $this->responseError(0, $e->getMessage());
        }
    }

    public function storyComment(Request $request)
    {
        try {
            extract($request->all());
            $key = $request->header('key');

            $token = $request->header('token');
            $valid = AppBaseController::requiredValidation([
                'key' => @$key,
                'token' => @$token,
            ]);

            if ($valid != '') {
                $msg = trans('words.please_enter') . $valid;
                return $this->responseError(0, $msg);
            }

            $devicetype = empty($_SERVER["HTTP_DEVICETYPE"]) ? "" : $_SERVER["HTTP_DEVICETYPE"];
            $versioncode = empty($_SERVER["HTTP_VERSIONCODE"]) ? "" : $_SERVER["HTTP_VERSIONCODE"];
            if (CommonController::versionCheck($devicetype, $versioncode)) {
                return AppBaseController::responseError(26, trans('words.update_app_msg'));
            }
            if (CommonController::checkKeyExist($key) == 0) {
                return AppBaseController::responseError(0, trans('words.incorrect_key'));
            } elseif (CommonController::checkKeyTokenExist($key, $token) == 0) {
                return AppBaseController::responseError(0, trans('words.incorrect_token'));
            }

            $valid = AppBaseController::requiredValidation([
                'user_id' => @$user_id,
                'friend_id' => @$friend_id,
                'comment' => @$comment,
            ]);

            if ($valid != '') {
                $msg = trans('words.please_enter') . $valid;
                return $this->responseError(0, $msg);
            }

            $chkUserExist = User_Master::where('user_id', $user_id)->first();
            if (empty($chkUserExist)) {
                return AppBaseController::responseError(0, trans('words.invalid_user_id'));
            }
            if ($chkUserExist->status == '1') {
                return AppBaseController::responseError(5, trans('words.account_deactive'));
            }

            $chkFriendUserExist = User_Master::where('user_id', $friend_id)->first();
            if (empty($chkFriendUserExist)) {
                return AppBaseController::responseError(0, trans('words.invalid_friend_id'));
            }

            Story_Comment_Master::create(['user_id' => $user_id, 'friend_id' => $friend_id, 'comment' => utf8_encode($comment)]);
            Notification_Master::create(['user_id' => $friend_id, 'other_id' => $user_id, 'message' => utf8_encode($comment)]);

            if ($chkFriendUserExist->device_token != '') {
                CommonController::sendPush($chkFriendUserExist->name ?? '', $comment, $chkFriendUserExist->device_token);
            }

            return AppBaseController::responseError(1, trans('words.comment_success'));
        } catch (\Exception $e) {
            return $this->responseError(0, $e->getMessage());
        }
    }

    public function storyDeleteCron(Request $request)
    {

        $getStoryDtl = Story_Master::get();

        foreach ($getStoryDtl as $key => $value) {
            $date_a = new DateTime();
            $date_b = new DateTime($value->created_at);

            $interval = date_diff($date_b, $date_a);

            $dateDiff = $interval->format('%h');
            $dateDayDiff = $interval->format('%d');

            if ($dateDiff > 20 || $dateDayDiff > 0) {
                ViewStory::where('view_user_id', $value->user_id)->delete();
                Story_Master::where('story_id', $value->story_id)->delete();
                Story_Media_Master::where('story_id', $value->story_id)->delete();
            }
        }
        exec('find /home/appki4nz/storyDeleteCron.* -delete');
    }

    /*public function getMusic(Request $request){

       // for($i=1;$i<51;$i++){
        //$url = "https://www.jiosaavn.com/api.php?p=".$i."&_format=json&_marker=0&api_version=4&ctx=web6dot0&n=40&__call=search.getResults&q=hindi";
        $url = "https://www.jiosaavn.com/api.php?p=1&_format=json&_marker=0&api_version=4&ctx=web6dot0&n=40&__call=search.getResults&q=hindi";
        $result = $this->getMusicURL($url);
            foreach($result->results as $value){
                $media_url = $value->perma_url;

               $musicDtl = Music_Master::where(['music_other_id' => $value->id])->first();

                if(empty($musicDtl)){
                    $media_result = $this->getMusicURL("https://jiosaavn-api.vercel.app/link?query=".$media_url);
                    //Music_Master::create(['music_other_id' => $value->id,'title' => $value->title,'image_url' => $value->image,'media_url' => $media_result->media_url ?? '','artists_name' => $value->title,'language' => $value->language]);
               }

            }
       // }

    }

        public function getMusicURL($url){
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $record = curl_exec($ch);
            curl_close ($ch);
            $result1 = json_decode($record);

            return  $result1;
        }*/
    public function getMusic()
    {

        for ($i = 1; $i < 100; $i++) {
            $url = "https://www.jiosaavn.com/api.php?p=1&_format=json&_marker=0&api_version=4&ctx=web6dot0&n=40&__call=search.getResults&q=jayz";
           
            //$url = "https://www.jiosaavn.com/api.php?p=1&_format=json&_marker=0&api_version=4&ctx=web6dot0&n=40&__call=search.getResults&q=hindi";
            $result = $this->getMusicURL($url);
            
           
            foreach ($result->results as $value) {
                $media_url = $value->perma_url;

                $musicDtl = Music_Master::where(['music_other_id' => $value->id])->first();

                //if (empty($musicDtl)) {
                    $media_result = $this->getMusicURL("https://jiosaavn-api.vercel.app/link?query=" . $media_url);
                    Music_Master::create(['music_other_id' => $value->id, 'title' => $value->title, 'image_url' => $value->image, 'media_url' => $media_result->media_url ?? '', 'artists_name' => $value->more_info->music, 'language' => $value->language]);
               // }

            }

       }

    }

    public function getMessages(){
        $messages = SampleMessage::orderBy('id','DESC')->get();
        return AppBaseController::sendSuccess($messages);
    }

    public function getMusicURL($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $record = curl_exec($ch);
        curl_close($ch);
        $result1 = json_decode($record);
        return $result1;
    }

    public function getTime($start_date)
    {
        $date1 = strtotime(date('Y-m-d H:i:s'));
        $date2 = strtotime($start_date);
        // Formulate the Difference between two dates
        $diff = abs($date2 - $date1);
        $years = floor($diff / (365 * 60 * 60 * 24));
        $date_str = "";

        $months = floor(($diff - $years * 365 * 60 * 60 * 24)
            / (30 * 60 * 60 * 24));

        $days = floor(($diff - $years * 365 * 60 * 60 * 24 -
                $months * 30 * 60 * 60 * 24) / (60 * 60 * 24));
        $hours = floor(($diff - $years * 365 * 60 * 60 * 24
                - $months * 30 * 60 * 60 * 24 - $days * 60 * 60 * 24)
            / (60 * 60));
        $minutes = floor(($diff - $years * 365 * 60 * 60 * 24
                - $months * 30 * 60 * 60 * 24 - $days * 60 * 60 * 24
                - $hours * 60 * 60) / 60);
        $seconds = floor(($diff - $years * 365 * 60 * 60 * 24
            - $months * 30 * 60 * 60 * 24 - $days * 60 * 60 * 24
            - $hours * 60 * 60 - $minutes * 60));

        if ($years != "" && $years != 0) {
            $date_str .= sprintf("%d" . " years", $years);
        }
        if ($years == 0 && $months != "" && $months != 0) {
            $date_str .= sprintf("%d" . " months", $months);
        }
        if ($months == 0 && $years == 0 && $days != "" && $days != 0) {
            $date_str .= sprintf("%d" . " days", $days);
        }
        if ($years == 0 && $months == 0 && $days == 0 && $hours != "" && $hours != 0) {
            $date_str .= sprintf("%d" . " hours", $hours);
        }
        if ($days == 0 && $months == 0 && $hours == 0 && $minutes != "" && $minutes != 0) {
            $date_str .= sprintf("%d" . " min", $minutes);
        }
        if ($minutes == 0 && $days == 0 && $seconds != "" && $seconds != 0) {
            $date_str .= sprintf("%d" . " sec", $seconds);
        }

        return $date_str;
    }

    public function profileReportByUser(Request $request)
    {
        try {
            extract($request->all());
            $key = $request->header('key');

            $token = $request->header('token');
            $valid = AppBaseController::requiredValidation([
                'key' => @$key,
                'token' => @$token,
            ]);

            if ($valid != '') {
                $msg = trans('words.please_enter') . $valid;
                return $this->responseError(0, $msg);
            }

            $devicetype = empty($_SERVER["HTTP_DEVICETYPE"]) ? "" : $_SERVER["HTTP_DEVICETYPE"];
            $versioncode = empty($_SERVER["HTTP_VERSIONCODE"]) ? "" : $_SERVER["HTTP_VERSIONCODE"];
            if (CommonController::versionCheck($devicetype, $versioncode)) {
                return AppBaseController::responseError(26, trans('words.update_app_msg'));
            }
            if (CommonController::checkKeyExist($key) == 0) {
                return AppBaseController::responseError(0, trans('words.incorrect_key'));
            } elseif (CommonController::checkKeyTokenExist($key, $token) == 0) {
                return AppBaseController::responseError(0, trans('words.incorrect_token'));
            }

            $valid = AppBaseController::requiredValidation([
                'user_id' => @$user_id,
                'friend_id' => @$friend_id,
                'message' => @$message,
                'type' => @$type,
            ]);

            if ($valid != '') {
                $msg = trans('words.please_enter') . $valid;
                return $this->responseError(0, $msg);
            }

            $chkUserExist = User_Master::where('user_id', $user_id)->first();
            if (empty($chkUserExist)) {
                return AppBaseController::responseError(0, trans('words.invalid_user_id'));
            }
            if ($chkUserExist->status == '1') {
                return AppBaseController::responseError(5, trans('words.account_deactive'));
            }

            $chkFriendUserExist = User_Master::where('user_id', $friend_id)->first();
            if (empty($chkFriendUserExist)) {
                return AppBaseController::responseError(0, trans('words.invalid_friend_id'));
            }
             

            $checkReport = Report_Master::where(['user_id' => $user_id, 'friend_id' => $friend_id,'extra_id' => $extra_id])->first();
            if(empty($checkReport)){
                Report_Master::create(['user_id' => $user_id, 'friend_id' => $friend_id,'type' => $type,'message' => $message,'extra_id' => $extra_id]);
                //CommonController::otherUserReportProfile('PlugSpace','Testing');
            }
            

            return AppBaseController::responseError(1, trans('words.report_success'));
        } catch (\Exception $e) {
            return $this->responseError(0, $e->getMessage());
        }
    }


}




<?php

namespace App\Http\Controllers\Api;

use App\Providers\RouteServiceProvider;
use App\Http\Controllers\AppBaseController;
use App\Http\Controllers\CommonController;
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
use App\Models\Rank_Text_Master;
use App\Models\ViewStory;
use File;
use DB;
use DateTime;


class MyScoreAPIController extends AppBaseController
{
    public function localeSetting($lang = 'en')
    {
        $a = \App::setlocale($lang);
    }

    public function isPrivateScore(Request $request)
    {
        try 
        {
            extract($request->all());
            $key = $request->header('key');
           
            $token = $request->header('token');
            $valid = AppBaseController::requiredValidation([
                'key' => @$key,
                'token' =>@$token,
            ]);
            if ($valid != '') 
            {
                $msg = trans('words.please_enter') . $valid;
                return $this->responseError(0, $msg);
            }

            $devicetype = empty($_SERVER["HTTP_DEVICETYPE"]) ? "" : $_SERVER["HTTP_DEVICETYPE"];
            $versioncode = empty($_SERVER["HTTP_VERSIONCODE"]) ? "" : $_SERVER["HTTP_VERSIONCODE"];
            if(CommonController::versionCheck($devicetype,$versioncode))
            {
              return AppBaseController::responseError(26, trans('words.update_app_msg'));
            }
            if (CommonController::checkKeyExist($key) == 0) 
            {
              return AppBaseController::responseError(0, trans('words.incorrect_key'));
            } 
            elseif(CommonController::checkKeyTokenExist($key,$token)==0)
            {
                return AppBaseController::responseError(0,trans('words.incorrect_token'));
            }

            $valid = AppBaseController::requiredValidation([
                'user_id'=>@$user_id,
            ]);

            if ($valid != '') 
            {
                $msg = trans('words.please_enter') . $valid;
                return $this->responseError(0, $msg);
            }

            $chkUserExist = User_Master::where('user_id',$user_id)->first();
            if(empty($chkUserExist))
            {
                return AppBaseController::responseError(0, trans('words.invalid_user_id'));
            }
             if($chkUserExist->status == '1'){
                return AppBaseController::responseError(5,  trans('words.account_deactive'));
            }
            
            if(!isset($is_private) && $is_private == '') {
                $is_private = '0';
            }
            User_Master::where('user_id',$user_id)->update(['is_private'=>$is_private]);
        
            return AppBaseController::responseSuccess(1, trans('words.scrore_private') , "True");

        }catch (\Exception $e){
            return $this->responseError(0, $e->getMessage());
        }
    }

    public function getMyScore(Request $request)
    {
        try 
        {
            extract($request->all());
            $key = $request->header('key');
           
            $token = $request->header('token');
            $valid = AppBaseController::requiredValidation([
                'key' => @$key,
                'token' =>@$token,
            ]);
            if ($valid != '') 
            {
                $msg = trans('words.please_enter') . $valid;
                return $this->responseError(0, $msg);
            }

            $devicetype = empty($_SERVER["HTTP_DEVICETYPE"]) ? "" : $_SERVER["HTTP_DEVICETYPE"];
            $versioncode = empty($_SERVER["HTTP_VERSIONCODE"]) ? "" : $_SERVER["HTTP_VERSIONCODE"];
            if(CommonController::versionCheck($devicetype,$versioncode))
            {
              return AppBaseController::responseError(26, trans('words.update_app_msg'));
            }
            if (CommonController::checkKeyExist($key) == 0) 
            {
              return AppBaseController::responseError(0, trans('words.incorrect_key'));
            } 
            elseif(CommonController::checkKeyTokenExist($key,$token)==0)
            {
                return AppBaseController::responseError(0,trans('words.incorrect_token'));
            }

            $valid = AppBaseController::requiredValidation([
                'user_id'=>@$user_id,
                
            ]);

            if ($valid != '') 
            {
                $msg = trans('words.please_enter') . $valid;
                return $this->responseError(0, $msg);
            }

            $chkUserExist = User_Master::where('user_id',$user_id)->first();
            if(empty($chkUserExist))
            {
                return AppBaseController::responseError(0, trans('words.invalid_user_id'));
            }
             if($chkUserExist->status == '1'){
                return AppBaseController::responseError(5,  trans('words.account_deactive'));
            }
            
            $userData = [];
            $userData['rank'] = $chkUserExist->rank;
            $userData['is_private'] = $chkUserExist->is_private;
            $userData['plugspace_rank'] = $chkUserExist->plugspace_rank ?? '0';
            $userData['characteristics'] = Rank_Text_Master::where('rank',$chkUserExist->rank)->get();

            $data['data'] = $userData;
            return AppBaseController::successResponse($data, 1,trans('words.list_success') , "True");

        }catch (\Exception $e){
            return $this->responseError(0, $e->getMessage());
        }
    }
    
    public function getFriendsScore(Request $request){
         try 
        {
            extract($request->all());
            $key = $request->header('key');
           
            $token = $request->header('token');
            $valid = AppBaseController::requiredValidation([
                'key' => @$key,
                'token' =>@$token,
            ]);
            if ($valid != '') 
            {
                $msg = trans('words.please_enter') . $valid;
                return $this->responseError(0, $msg);
            }

            $devicetype = empty($_SERVER["HTTP_DEVICETYPE"]) ? "" : $_SERVER["HTTP_DEVICETYPE"];
            $versioncode = empty($_SERVER["HTTP_VERSIONCODE"]) ? "" : $_SERVER["HTTP_VERSIONCODE"];
            if(CommonController::versionCheck($devicetype,$versioncode))
            {
              return AppBaseController::responseError(26, trans('words.update_app_msg'));
            }
            if (CommonController::checkKeyExist($key) == 0) 
            {
              return AppBaseController::responseError(0, trans('words.incorrect_key'));
            } 
            elseif(CommonController::checkKeyTokenExist($key,$token)==0)
            {
                return AppBaseController::responseError(0,trans('words.incorrect_token'));
            }

            $valid = AppBaseController::requiredValidation([
                'user_id'=>@$user_id,
                'friend_user_id'=>@$friend_user_id,
                
            ]);

            if ($valid != '') 
            {
                $msg = trans('words.please_enter') . $valid;
                return $this->responseError(0, $msg);
            }

            $chkUserExist = User_Master::where('user_id',$user_id)->first();
            if(empty($chkUserExist))
            {
                return AppBaseController::responseError(0, trans('words.invalid_user_id'));
            }
            if($chkUserExist->status == '1'){
                return AppBaseController::responseError(5,  trans('words.account_deactive'));
            }
            
            $rankArr = User_Master::inRandomOrder()->where('user_id','!=',$user_id)->first();
            $friendPrivate = User_Master::where('user_id',$friend_user_id)->first();

            $userData = [];
            $userData['rank'] = $friendPrivate->rank;
            $userData['is_private'] = $chkUserExist->is_private;
            $userData['plugspace_rank'] = $friendPrivate->plugspace_rank ?? '0';
            $userData['characteristics'] = Rank_Text_Master::where('rank',$chkUserExist->rank)->get();

            $data['data'] = $userData;
            return AppBaseController::successResponse($data, 1,trans('words.list_success') , "True");

        }catch (\Exception $e){
            return $this->responseError(0, $e->getMessage());
        }
    
    }

   
   public function getTime($start_date)
   {
    $date1 = strtotime(date('Y-m-d H:i:s')); 
    $date2 = strtotime($start_date);
    // Formulate the Difference between two dates
    $diff = abs($date2 - $date1); 
    $years = floor($diff / (365*60*60*24)); 
    $date_str= "";
    
    $months = floor(($diff - $years * 365*60*60*24)
                                   / (30*60*60*24)); 
                                   
    $days = floor(($diff - $years * 365*60*60*24 - 
                 $months*30*60*60*24)/ (60*60*24));
    $hours = floor(($diff - $years * 365*60*60*24 
           - $months*30*60*60*24 - $days*60*60*24)
                                       / (60*60)); 
    $minutes = floor(($diff - $years * 365*60*60*24 
             - $months*30*60*60*24 - $days*60*60*24 
                              - $hours*60*60)/ 60); 
    $seconds = floor(($diff - $years * 365*60*60*24 
             - $months*30*60*60*24 - $days*60*60*24
                    - $hours*60*60 - $minutes*60)); 
     
    if($years!="" && $years!=0){
        $date_str .= sprintf("%d"." years",$years);
    }
    if($years == 0 && $months!="" && $months!=0){
        $date_str .= sprintf("%d"." months",$months);
    }
    if($months == 0 && $years == 0 && $days!="" && $days!=0 ){
        $date_str .= sprintf("%d"." days",$days);
    }
    if($years == 0 && $months == 0 &&$days == 0  && $hours!="" && $hours!=0 ){
        $date_str .= sprintf("%d"." hours",$hours);
    }
    if($days == 0 && $months == 0 && $hours == 0 &&  $minutes!="" && $minutes!=0){
        $date_str .= sprintf("%d"." min",$minutes);
    }
    if($minutes==0 &&$days==0 && $seconds!="" && $seconds!=0){
        $date_str .= sprintf("%d"." sec",$seconds);
    }

    return $date_str;
   }

}     

    


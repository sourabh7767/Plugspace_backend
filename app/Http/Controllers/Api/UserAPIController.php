<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Models\Key_Master;
use App\Models\Key_Token_Master;
use App\Models\User_Master;
use App\Models\OTP_Master;
use App\Models\Apple_User_Master;
use App\Models\User_Media_Master;
use App\Models\Noti_Setting_Master;
use App\Models\Like_Dislike_Master;
use App\Models\Block_User_Master;
use App\Models\Report_User_Master;
use App\Models\Feed_Master;
use App\Models\User_Subscription_Master;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\CommonController;
use App\Models\PlugspaceUser;
use DateTime;
use stdClass;
use Log;

class UserAPIController extends AppBaseController
{
    public function localeSetting($lang = 'en')
    {
        $a = \App::setlocale($lang);
    }



    public function sendOTP(Request $request){
        try {

            extract($request->all());

            $key = $request->header('key');
            $valid = AppBaseController::requiredValidation([
                'key' => @$key,
            ]);

            if ($valid != '') {
                $msg = trans('words.please_enter') . $valid;
                return $this->responseError(0, $msg);
            }

            $devicetype = empty($_SERVER["HTTP_DEVICETYPE"]) ? "" : $_SERVER["HTTP_DEVICETYPE"];
            $versioncode = empty($_SERVER["HTTP_VERSIONCODE"]) ? "" : $_SERVER["HTTP_VERSIONCODE"];
            if(CommonController::versionCheck($devicetype,$versioncode)){
              return AppBaseController::responseError(26, trans('words.update_app_msg'));
            }

            if (CommonController::checkKeyExist($key) == 0) {
                return AppBaseController::responseError(0, trans('words.incorrect_key'));
            }

            $valid = AppBaseController::requiredValidation([
                'phone' => @$phone,
                'ccode' => @$ccode
            ]);
            if ($valid != '') {
                $msg = trans('words.please_enter') . $valid;
                return $this->responseError(0, $msg);
            }

            if(!isset($user_id) || $user_id==""){
                $user_id="";
            }


            if ($user_id != "" && $user_id != NULL) {
                $chkUserOwnNumber = User_Master::where(['phone'=>$phone,'ccode'=>$ccode,"user_id"=>$user_id])->first();

                if(!empty($chkUserOwnNumber)){
                    return $this->responseError(0, trans('words.update_mobile_with_own_number'));
                }

                $chkUserExist = User_Master::where(['phone'=>$phone,'ccode'=>$ccode])->where("user_id","!=",$user_id)->first();
            }
            else
            {
              $chkUserExist = User_Master::where(['phone'=>$phone,'ccode'=>$ccode])->first();
            }

            // if(!empty($chkUserExist)){
            //   return $this->responseError(0, trans('words.already_register'));
            // }

            $chkOTPExist = OTP_Master::where(['mobile'=>$phone,'ccode'=>$ccode])->first();
            $otpCode = substr(number_format(time() * rand(),0,'',''),0,4);
            $msg = "Your Plugspace OTP code is: ".$otpCode;

            // TODO:: sms send karvana thase ahiya thi
             $response=$this->otpSendToPhoneTwillo($ccode.$phone,$otpCode);
           //  if (!empty($response['sid'])) {
                if (!empty($chkOTPExist)) {
                    $updateObj = ["ccode"=>$ccode,"mobile"=>$phone,"otp_code"=>$otpCode,"is_verified"=>0];
                    OTP_Master::where(['otp_id'=>$chkOTPExist->otp_id])->update($updateObj);
                }
                else
                {
                    $insertObj = ["ccode"=>$ccode,"mobile"=>$phone,"otp_code"=>$otpCode,"is_verified"=>0];
                    OTP_Master::insert($insertObj);
                }
                $data['otpcode']=$otpCode;

              return AppBaseController::successResponse($data,1, trans('words.otp_send_success') , "True");
            // }
            // else
            // {
            //     return AppBaseController::responseSuccess(0, $response['message'] , "False");
            // }


        } catch (Exception $e) {
            return $this->responseError(0, $e->getMessage());
        }
    }

    public function otpSendToPhoneTwillo($phone="+919857431801", $sms_otp="1234")
    { //+917984666945
        $from_number="+17278557895";//"+18085175254";
        $number = $phone;
        $body = "Your Happly OTP code is: ".$sms_otp;

        $ID = env("TWILLIO_ACCOUNT_SID");
        $token = env("AUTH_TOKEN");
        $service = env("SERVICE_ID");

        $url = 'https://api.twilio.com/2010-04-01/Accounts/' . $ID . '/Messages.json';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION,true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($ch, CURLOPT_HTTPAUTH,CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD,$ID . ':' . $token);

        curl_setopt($ch, CURLOPT_POST,true);
        curl_setopt($ch, CURLOPT_POSTFIELDS,
            'To=' . rawurlencode($number) .
            '&MessagingServiceSid=' . $service .
            // '&From=' . rawurlencode($from_number) .
            '&Body=' . rawurlencode($body));

        $resp = curl_exec($ch);
        //echo "<pre>";print($resp);die;
        curl_close($ch);
        $response = json_decode($resp,true);
        return $response;

      }

    public function verifyOTP(Request $request){
        try {
            
            extract($request->all());

            $key = $request->header('key');
            $valid = AppBaseController::requiredValidation([
                'key' => @$key,
            ]);
            if ($valid != '') {
                $msg = trans('words.please_enter') . $valid;
                return $this->responseError(0, $msg);
            }

            $devicetype = empty($_SERVER["HTTP_DEVICETYPE"]) ? "" : $_SERVER["HTTP_DEVICETYPE"];
            $versioncode = empty($_SERVER["HTTP_VERSIONCODE"]) ? "" : $_SERVER["HTTP_VERSIONCODE"];

            if(!isset($device_token) || $device_token==""){
                $device_token="";
            }
            if (!isset($device_type) || $device_type == "") {
                $device_type = "";
            }

            if(CommonController::versionCheck($devicetype,$versioncode)){
              return AppBaseController::responseError(26, trans('words.update_app_msg'));
            }

            if (CommonController::checkKeyExist($key) == 0) {
                return AppBaseController::responseError(0, trans('words.incorrect_key'));
            }

            $valid = AppBaseController::requiredValidation([
                'phone' => @$phone,
                'ccode' => @$ccode,
                'otpcode'=> @$otpcode
            ]);
            if ($valid != '') {
                $msg = trans('words.please_enter') . $valid;
                return $this->responseError(0, $msg);
            }
            $chkOTPExist = OTP_Master::where(['mobile'=>$phone,'ccode'=>$ccode])->first();

            if (!empty($chkOTPExist)) {
                if ($chkOTPExist->otp_code == $otpcode) {
                    $updateObj = ["is_verified"=>1];
                    OTP_Master::where("otp_id",$chkOTPExist->otp_id)->update($updateObj);

                    $tokenDtl = Key_Token_Master::orderBy(DB::raw('RAND()'))->first();

                    $user_detail = User_Master::with("media_detail")->where(['phone'=>$phone,'ccode'=>$ccode])->first();
                    if (!empty($user_detail)) {
                        if($user_detail->status == '1'){
                            return AppBaseController::responseError(5,  trans('words.account_deactive'));
                        }
                        $data['is_login'] = "1";

                        if (!empty($user_detail) &&  $user_detail->is_fb == 1) {
                            return AppBaseController::responseError(4, $user_detail->phone.trans('words.is_fb_user'));
                        } elseif (!empty($user_detail) && $user_detail->is_google == 1) {
                            return AppBaseController::responseError(4, $user_detail->phone .trans('words.is_google_user'));
                        } elseif (!empty($user_detail) && $user_detail->is_fb == 0 && $user_detail->is_google == 0) {

                            $updateObj = ['device_type' => $device_type, 'device_token' => $device_token];
                            $update = User_Master::where('user_id',$user_detail->user_id)->update($updateObj);

                            $tokenDtl = Key_Token_Master::orderBy(DB::raw('RAND()'))->first();

                            $data['data'] = $this->getUserDtl($user_detail,$tokenDtl->token);

                        }

                         return AppBaseController::successResponse($data,1, trans('words.already_register'), "True");
                    }
                    else
                    {
                        $data['is_login'] = "0";
                        $data['data'] = new stdClass();
                        return AppBaseController::successResponse($data,1, trans('words.incorrect_phone'), "True");
                    }

                }
                else
                {
                    return AppBaseController::responseError(0, trans('words.otp_invalid'));
                }
            }
            else
            {
                return AppBaseController::responseError(0, trans('words.incorrect_phone'));
            }
        } catch (Exception $e) {
            return $this->responseError(0, $e->getMessage());
        }
    }

    public function signUp(Request $request)
    {
        $input = $request->all();
        Log::info($input);
        try {
            extract($request->all());
            //$profile=$_FILES['profile']['name'];
            $key = $request->header('key');
            $valid = AppBaseController::requiredValidation([
                'key' => @$key,
            ]);
            if ($valid != '') {
                $msg = trans('words.please_enter') . $valid;
                return $this->responseError(0, $msg);
            }

            $devicetype = empty($_SERVER["HTTP_DEVICETYPE"]) ? "" : $_SERVER["HTTP_DEVICETYPE"];
            $versioncode = empty($_SERVER["HTTP_VERSIONCODE"]) ? "" : $_SERVER["HTTP_VERSIONCODE"];
            if(CommonController::versionCheck($devicetype,$versioncode)){
              return AppBaseController::responseError(26, trans('words.update_app_msg'));
            }

            if (CommonController::checkKeyExist($key) == 0) {
                return AppBaseController::responseError(0, trans('words.incorrect_key'));
            }

            $valid = AppBaseController::requiredValidation([
                'dob' => @$dob,
                'name' => @$name,
                'gender' => @$gender,
                'height' => @$height,
                'weight' => @$weight,
                'education_status' => @$education_status,
                'dob' => @$dob,
                'want_childrens' => @$want_childrens,
                //'marring_race' => @$marring_race,
                //'relationship_status' => @$relationship_status,
                //'ethinicity' => @$ethinicity,
                //'job_title' => @$job_title,
                //'dress_size' => @$dress_size,
                //'age_range_marriage' => @$age_range_marriage,
                //'my_self_men' => @$my_self_men,
                //'about_you' => @$about_you,
                //'nice_meet' => @$nice_meet,
            ]);
            if ($valid != '') {
                $msg = trans('words.please_enter') . $valid;
                return $this->responseError(0, $msg);
            }

            $checkExistUser = User_Master::where(['phone'=>$phone,'ccode'=>$ccode])->first();


            if (!empty($checkExistUser) && $checkExistUser->is_apple == 1) {
                return AppBaseController::responseError(4, $checkExistUser->phone.trans('words.is_apple_user'));
            } elseif (!empty($checkExistUser) && $checkExistUser->is_insta == 1) {
                return AppBaseController::responseError(4, $checkExistUser->phone .trans('words.is_instagram_user'));
            }

            if(!isset($is_apple) || $is_apple==""){
                $is_apple=0;
            }

            if(!isset($apple_id) || $apple_id==""){
                $apple_id="";
            }

            if(!isset($is_insta) || $is_insta==""){
                $is_insta=0;
            }

            if(!isset($insta_id) || $insta_id==""){
                $insta_id="";
            }

            if(!isset($device_token) || $device_token==""){
                $device_token="";
            }
            if (!isset($device_type) || $device_type == "") {
                $device_type = "";
            }
            if (!isset($is_manual_email) || $is_manual_email == "") {
                $is_manual_email = 0;
            }


           if($is_apple==1){
                $valid = AppBaseController::requiredValidation([
                    'apple_id'=>@$apple_id
                ]);
                if ($valid != '') {
                    $msg = trans('words.please_enter') . $valid;
                    return $this->responseError(0, $msg);
                }

                $checkExistUser = User_Master::where(['apple_id'=>$apple_id])->first();
                if (!empty($checkExistUser) && $checkExistUser->is_apple == 1) {
                    return AppBaseController::responseError(0, $checkExistUser->phone.trans('words.already_register_apple'));
                }

            }

           if($is_insta==1){
                $valid = AppBaseController::requiredValidation([
                    'insta_id'=>@$insta_id
                ]);
                if ($valid != '') {
                    $msg = trans('words.please_enter') . $valid;
                    return $this->responseError(0, $msg);
                }

                $checkExistUser = User_Master::where(['insta_id'=>$insta_id])->first();
                if (!empty($checkExistUser) && $checkExistUser->is_apple == 1) {
                    return AppBaseController::responseError(0, $checkExistUser->phone.trans('words.already_register_insta'));
                }
            }

            $generate_token = CommonController::RandomStringGenerator();

            $respMsg = trans('words.register_complete') ;
            $respCode = 1;

            if(!isset($phone) && $phone == ''){
                $phone = '';
                $ccode = '';
            }
            
            if(!isset($about_you)){
                $about_you = "";
            }

            if(!empty($checkExistUser)){
                $user_id = $checkExistUser->user_id;
            }else{
                //fkjghkfg
                $insert = [
                    'name' => $name,
                    'ccode' => $ccode,
                    'phone' => $phone,
                    'gender' => $gender,
                    'rank' => $rank ?? '',
                    'is_geo_location' => $is_geo_location ?? '',
                    'height' => $height,
                    'weight' => $weight,
                    'education_status' => $education_status,
                    'dob' => $dob,
                    'location' => $location ?? '',
                    'children' => $children,
                    'want_childrens' => $want_childrens,
                    'marring_race' => $marring_race ?? '',
                    'relationship_status' => $relationship_status ?? '',
                    'ethinicity' => $ethinicity ?? '',
                    'company_name' => $company_name ?? '',
                    'job_title' => $job_title ?? '',
                    'make_over' => $make_over ?? '',
                    'dress_size' => $dress_size ?? '',
                    'signiat_bills' => $signiat_bills ?? '',
                    'times_of_engaged' => $times_of_engaged ?? '',
                    'your_body_tatto' => $your_body_tatto ?? '',
                    'age_range_marriage' => $age_range_marriage ?? '',
                    'my_self_men' => $my_self_men ?? '',
                    'about_you' => json_encode($about_you),
                    'nice_meet' => $nice_meet ?? '',
                    'is_manual_email' => $is_manual_email,
                    'is_apple' => $is_apple,
                    'apple_id' => $apple_id,
                    'is_insta' => $is_insta,
                    'insta_id' => $insta_id,
                    'device_type' => $device_type,
                    'device_token' => $device_token,
                    'is_private' => '1',
                ];
                $user_detail = User_Master::create($insert); 
                $user_id = $user_detail->id;
               
                if (isset($request['profile'])) {
                    foreach ($request['profile'] as $k=>$media_file) {

                        $time = time() .  uniqid();
                        $newname = $time . '.' . $media_file->getClientOriginalExtension();
                        $media_type = explode("/", $media_file->getMimeType())[0];

                        $media_file->move(public_path('profile'), $newname);

                        $insertMediaObj = [
                            'user_id' => $user_id,
                            'profile' => $newname,
                            'media_type' => $media_type,
                        ];
                        User_Media_Master::create($insertMediaObj);
                    }
                }
            }

           
            $ins = [
                'key' => $key,
                'token' => $generate_token
            ];
            Key_Token_Master::create($ins);

            $user_detail = User_Master::where('user_id',$user_id)->with("media_detail")->first();
            $dob = $user_detail->dob;
            $condate = date("Y-m-d");
            $birthdate = new DateTime(date("Y-m-d",  strtotime(implode('-', array_reverse(explode('/', $dob))))));
            $today= new DateTime(date("Y-m-d",  strtotime(implode('-', array_reverse(explode('/', $condate))))));
            $age = $birthdate->diff($today)->y;

            $user_detail['age'] = (string)$age;
            $data['data']=$this->getUserDtl($user_detail,$generate_token);

            return AppBaseController::successResponse($data, $respCode, $respMsg, "True");

        } catch (\Exception $e) {
            return $this->responseError(0, $e->getMessage());
        }
    }

    public function getUserDtl($user_detail,$generateToken)
    {

        $dob = $user_detail->dob;
        $condate = date("Y-m-d");
        $birthdate = new DateTime(date("Y-m-d",  strtotime(implode('-', array_reverse(explode('/', $dob))))));
        $today= new DateTime(date("Y-m-d",  strtotime(implode('-', array_reverse(explode('/', $condate))))));
        $age = $birthdate->diff($today)->y;
        $user_detail['age'] = (string)$age;
        $user_detail['about_you'] = (string)json_decode($user_detail->about_you);


        $mediaDtl = User_Media_Master::where('user_id',$user_detail->user_id)->get();
        foreach($mediaDtl as $key => $value){
            $value->description = '';
        }
        $feedDtl = Feed_Master::where('user_id',$user_detail->user_id)->get();
        foreach($feedDtl as $key => $value){
             $value->type = 'feed';
        }

        $collection = collect($mediaDtl);
        $merged     = $collection->merge($feedDtl);
        $userMediaDtl   = $merged->all();

        $user_detail->media_detail  = $userMediaDtl;

        $user_detail->token = $generateToken;
        if (isset($user_detail->id)) {
            $user_detail->user_id = $user_detail->id;
            unset($user_detail->id);
        }
        return $user_detail;
    }

      public function isRegister(Request $request)
    {
        try {
            extract($request->all());
            $key = $request->header('key');
            $valid = AppBaseController::requiredValidation([
                'key' => $key,
            ]);
            if ($valid != '') {
                $msg = trans('words.please_enter') . $valid;
                return $this->responseError(0, $msg);
            }

            $devicetype = empty($_SERVER["HTTP_DEVICETYPE"]) ? "" : $_SERVER["HTTP_DEVICETYPE"];
            $versioncode = empty($_SERVER["HTTP_VERSIONCODE"]) ? "" : $_SERVER["HTTP_VERSIONCODE"];
            if(CommonController::versionCheck($devicetype,$versioncode)){
              return AppBaseController::responseError(26, trans('words.update_app_msg'));
            }

            if (CommonController::checkKeyExist($key) == 0) {
                return AppBaseController::responseError(0, trans('words.incorrect_key'));
            }

            if (!isset($device_token) || $device_token == "" || $device_token == NULL) {
                $device_token = "";
            }

            if (!isset($device_type) || $device_type == "" || $device_type == NULL) {
                $device_type = "";
            }

            if (!isset($is_social_type) || $is_social_type == "" || $is_social_type == NULL) {
                $is_social_type = "";
            }

            if (!isset($is_insta) || $is_insta == "" || $is_insta == NULL) {
                $is_insta = "";
            }
            else if($is_insta == 1)
            {
                $is_social_type = "is_insta";
            }

            if (!isset($is_apple) || $is_apple == "" || $is_apple == NULL) {
                $is_apple = "";
            }
            else if($is_apple == 1)
            {
                $is_social_type = "is_apple";
            }


            if (!isset($phone) || $phone == "" || $phone == NULL) {
                $phone = "";
            }


            if (($is_insta == "" || $is_insta == NULL) && ($is_apple == "" || $is_apple == NULL) &&  ($phone == "" || $phone == NULL)) {
                return AppBaseController::responseError(0, trans('words.please_enter') ."is_insta or is_apple or phone.");
            } else if ($is_insta == 1) {
                $valid=AppBaseController::requiredValidation([
                    'insta_id' => $insta_id
                ]);
                if ($valid != '') {
                    $msg = trans('words.please_enter') . $valid;
                    return $this->responseError(0, $msg);
                }
            } else if ($is_apple == 1) {
                $valid=AppBaseController::requiredValidation([
                    'apple_id' => $apple_id
                ]);
                if ($valid != '') {
                    $msg = trans('words.please_enter') . $valid;
                    return $this->responseError(0, $msg);
                }
            }


            if ($phone != "") {
                $user_detail = User_Master::where(['phone'=> $phone])->first();
            } else {
                if ($is_insta == 1) {
                    $user_detail = User_Master::where(['insta_id'=> $insta_id])->first();
                }
                else if ($is_apple == 1) {
                    $user_detail = User_Master::where(['apple_id'=> $apple_id])->first();
                }
            }

            if (!empty($user_detail) && $user_detail->is_insta == 0 && $user_detail->is_apple == 0) {
                return AppBaseController::responseError(4, "$user_detail->phone".trans('words.is_normal_user'));
            }
            else if (!empty($user_detail) && ($user_detail->$is_social_type == 0) && $user_detail->is_insta == 1) {
                return AppBaseController::responseError(4, "$user_detail->phone" .trans('words.is_insta_user'));
            }
            else if (!empty($user_detail) && ($user_detail->$is_social_type == 0) && $user_detail->is_apple == 1) {
                return AppBaseController::responseError(4, "$user_detail->phone" .trans('words.is_apple_user'));
            }

            else if (!empty($user_detail) && ($user_detail->is_insta == $is_insta  || $user_detail->is_apple == $is_apple)) {

                $updateObj = ['device_type' => $device_type, 'device_token' => $device_token];
                $update = User_Master::where("user_id",$user_detail->user_id)->update($updateObj);
                $tokenDtl = Key_Token_Master::orderBy(DB::raw('RAND()'))->first();

                $dob = $user_detail->dob;
                $condate = date("Y-m-d");
                $birthdate = new DateTime(date("Y-m-d",  strtotime(implode('-', array_reverse(explode('/', $dob))))));
                $today= new DateTime(date("Y-m-d",  strtotime(implode('-', array_reverse(explode('/', $condate))))));
                $age = $birthdate->diff($today)->y;
                $user_detail['age'] = (string)$age;


                  $mediaDtl = User_Media_Master::where('user_id',$user_detail->user_id)->get();
                  foreach($mediaDtl as $key => $value){
                      $value->description = '';

                  }
                  $feedDtl = Feed_Master::where('user_id',$user_detail->user_id)->get();
                    foreach($feedDtl as $key => $value){
                      $value->type = 'feed';
                    }

                    $collection = collect($mediaDtl);
                    $merged     = $collection->merge($feedDtl);
                    $userMediaDtl   = $merged->all();

                  $user_detail['media_detail']  = $userMediaDtl;

                $user_detail['token'] = $tokenDtl->token;
                $data['data'] = $user_detail;

                return AppBaseController::successResponse($data, 1, trans('words.login_success'), "True");
            } else {
                return AppBaseController::responseError(3, trans('words.not_registered_user'));
            }

        } catch (\Exception $e) {
            return $this->responseError(0, $e->getMessage());
        }
    }

    public function login(Request $request)
    {
        try {
            extract($request->all());

            $key = $request->header('key');
            $valid = AppBaseController::requiredValidation([
                'key' => @$key,
                'phone' => @$phone,
                'ccode' => @$ccode,
            ]);
            if ($valid != '') {
                $msg = trans('words.please_enter') . $valid;
                return $this->responseError(0, $msg);
            }

            $devicetype = empty($_SERVER["HTTP_DEVICETYPE"]) ? "" : $_SERVER["HTTP_DEVICETYPE"];
            $versioncode = empty($_SERVER["HTTP_VERSIONCODE"]) ? "" : $_SERVER["HTTP_VERSIONCODE"];
            if(CommonController::versionCheck($devicetype,$versioncode)){
              return AppBaseController::responseError(26, trans('words.update_app_msg'));
            }

            if (CommonController::checkKeyExist($key) == 0) {
                return AppBaseController::responseError(0, trans('words.incorrect_key'));
            }


            if(!isset($device_token) || $device_token==""){
                $device_token="";
            }
            if (!isset($device_type) || $device_type == "") {
                $device_type = "";
            }

            $check_user = User_Master::where(['phone'=> $phone,'ccode'=> $ccode])->first();
            
            if(empty($check_user)){
                return AppBaseController::responseError(0, trans('words.incorrect_phone'));
            } elseif (!empty($check_user) &&  $check_user->is_fb == 1) {
                return AppBaseController::responseError(4, $check_user->phone.trans('words.is_fb_user'));
            } elseif (!empty($check_user) && $check_user->is_google == 1) {
                return AppBaseController::responseError(4, $check_user->phone .trans('words.is_google_user'));
            } elseif (!empty($check_user) && $check_user->is_fb == 0 && $check_user->is_google == 0) {

                $updateObj = ['device_type' => $device_type, 'device_token' => $device_token];
                $update = User_Master::where('user_id',$check_user->user_id)->update($updateObj);

                $tokenDtl = Key_Token_Master::orderBy(DB::raw('RAND()'))->first();

                $data['data'] = $this->getUserDtl($check_user,$tokenDtl->token);

                return AppBaseController::successResponse($data, 1, trans('words.login_success'), "True");

            }

        } catch (\Exception $e) {
            return $this->responseError(0, $e->getMessage());
        }
    }

    public function check(){
         $tokenDtl = Key_Token_Master::orderBy(DB::raw('RAND()'))->first();
                    $update = User_Master::where('user_id','1')->first();

                $data = $this->getUserDtl($update,$tokenDtl->token);

                dd($data);
    }

    public function updateProfile(Request $request)
    {
        try {

            extract($request->all());

            $key = $request->header('key');
            $token = $request->header('token');
            $valid = AppBaseController::requiredValidation([
                'key' => @$key,
                'token' => @$token,
                'user_id' => @$user_id,

            ]);
            if ($valid != '') {
                $msg = trans('words.please_enter') . $valid;
                return $this->responseError(0, $msg);
            }

            $devicetype = empty($_SERVER["HTTP_DEVICETYPE"]) ? "" : $_SERVER["HTTP_DEVICETYPE"];
            $versioncode = empty($_SERVER["HTTP_VERSIONCODE"]) ? "" : $_SERVER["HTTP_VERSIONCODE"];
            if(CommonController::versionCheck($devicetype,$versioncode)){
              return AppBaseController::responseError(26, trans('words.update_app_msg'));
            }

            if (CommonController::checkKeyExist($key) == 0) {
                return AppBaseController::responseError(0, trans('words.incorrect_key'));
            }else if (CommonController::checkKeyTokenExist($key, $token) == 0) {
                return AppBaseController::responseError(0, trans('words.incorrect_token'));
            }

                $userDtl = User_Master::where(['user_id'=>$user_id])->first();

                if (empty($userDtl)) {
                    return AppBaseController::responseError(0, trans('words.invalid_user_id'));
                }else {

                    /*if($phone == '' && $ccode == ''){
                        $phone = '';
                        $ccode = '';
                    }
                    $checkExistUser = User_Master::where(['phone'=> $phone,'ccode'=> $ccode])->where('user_id','!=',$user_id)->first();
                    if (!empty($checkExistUser) && $checkExistUser->is_apple == 1) {
                        return AppBaseController::responseError(4, $checkExistUser->phone.trans('words.is_apple_user'));
                    } elseif (!empty($checkExistUser) && $checkExistUser->is_insta == 1) {
                        return AppBaseController::responseError(4, $checkExistUser->phone .trans('words.is_instagram_user'));
                    }elseif (!empty($checkExistUser)) {
                        return AppBaseController::responseError(0, trans('words.already_register'));
                    }*/


                     if (isset($remove_media_id) && $remove_media_id != "") {
                        $removeMediaIds = explode(",", $remove_media_id);
                        foreach ($removeMediaIds as $key => $media_id) {

                            $getMedia = User_Media_Master::where("media_id",$media_id)->first();
                            if (!empty($getMedia)) {
                                $rootPath = str_replace('\\', '/', __DIR__);
                                $homePage = explode("/app", $rootPath)[0].'/public/profile/';
                                $mediaName = basename($getMedia->profile);
                                if (file_exists($homePage.$mediaName)) {
                                    unlink($homePage.$mediaName);
                                    User_Media_Master::where("media_id",$media_id)->delete();
                               }
                            }
                        }
                    }

                    if (isset($request['profile'])) {
                        foreach ($request['profile'] as $k=>$media_file) {

                            $time = time() .  uniqid();
                            $newname = $time . '.' . $media_file->getClientOriginalExtension();
                            $media_type = explode("/", $media_file->getMimeType())[0];

                            $media_file->move(public_path('profile'), $newname);

                            $insertMediaObj = [
                                'user_id' => $user_id,
                                'profile' => $newname,
                                'media_type' => $media_type,
                            ];
                            User_Media_Master::create($insertMediaObj);
                        }
                    }

                    CommonController::updateProfileFirebase($user_id);

                    if(!isset($about_you))
                    {
                        $about_you = "";
                    }

                    $upddata=[
                               // 'phone' => $phone,
                                'gender' => $gender,
                                'rank' => $rank ?? '',
                                'is_geo_location' => $is_geo_location,
                                'height' => $height,
                                'weight' => $weight,
                                'education_status' => $education_status,
                                'dob' => $dob,
                                'location' => $location,
                                'children' => $children,
                                'want_childrens' => $want_childrens,
                                'marring_race' => $marring_race ?? '',
                                'relationship_status' => $relationship_status ?? '',
                                'ethinicity' => $ethinicity ?? '',
                                'company_name' => $company_name ?? '',
                                'job_title' => $job_title ?? '',
                                'make_over' => $make_over ?? '',
                                'dress_size' => $dress_size ?? '',
                                'signiat_bills' => $signiat_bills ?? '',
                                'times_of_engaged' => $times_of_engaged ?? '',
                                'your_body_tatto' => $your_body_tatto ?? '',
                                'age_range_marriage' => $age_range_marriage ?? '',
                                'my_self_men' => $my_self_men ?? '',
                                'about_you' => json_encode($about_you),
                                'nice_meet' => $nice_meet ?? '',
                                'name' => $name,
                                "updated_at"=>date('Y-m-d H:i:s')
                            ];

                    User_Master::where('user_id', $user_id)->update($upddata);
                    $user_detail = User_Master::where('user_id',$user_id)->with("media_detail")->first();

                    $tokenDtl = Key_Token_Master::orderBy(DB::raw('RAND()'))->first();

                    $data['data'] = $this->getUserDtl($user_detail,$tokenDtl->token);

                    return AppBaseController::successResponse($data, 1, trans('words.profile_update'), "True");
                }

        } catch (\Exception $e) {
            return $this->responseError(0, $e->getMessage());
        }
    }


    public function logOut(Request $request)
    {
        try {
            extract($request->all());

            $key = $request->header('key');
            $token = $request->header('token');
            $valid = AppBaseController::requiredValidation([
                'key' => @$key,
                'token' => @$token,
                'user_id' => @$user_id
            ]);
            if ($valid != '') {
                $msg = trans('words.please_enter') . $valid;
                return $this->responseError(0, $msg);
            }

            $devicetype = empty($_SERVER["HTTP_DEVICETYPE"]) ? "" : $_SERVER["HTTP_DEVICETYPE"];
            $versioncode = empty($_SERVER["HTTP_VERSIONCODE"]) ? "" : $_SERVER["HTTP_VERSIONCODE"];
            if(CommonController::versionCheck($devicetype,$versioncode)){
              return AppBaseController::responseError(26, trans('words.update_app_msg'));
            }

            if (CommonController::checkKeyExist($key) == 0) {
                return AppBaseController::responseError(0, trans('words.incorrect_key'));
            }else if (CommonController::checkKeyTokenExist($key, $token) == 0) {
                return AppBaseController::responseError(0, trans('words.incorrect_token'));
            }
                $userDtl = User_Master::where(['user_id'=>$user_id])->first();

                if (empty($userDtl)) {
                    return AppBaseController::responseError(0, trans('words.invalid_user_id'));
                }

                $removeToken = User_Master::where('user_id',$user_id)->update(['device_token' => '']);
                $data['data'] = new stdClass;
                return AppBaseController::responseSuccess(1,"Log out successfully", "True");

        } catch (\Exception $e) {
            return $this->responseError(0, $e->getMessage());
        }
    }

    // public function contactUs(Request $request)
    // {
    //     try {
    //         extract($request->all());

    //         $key = $request->header('key');
    //         $token = $request->header('token');
    //         $valid = AppBaseController::requiredValidation([
    //             'key' => @$key,
    //             'token' => @$token,
    //             'email'=> @$email,
    //             'name'=> @$name,
    //             'subject'=> @$subject,
    //             'message'=> @$message
    //         ]);
    //         if ($valid != '') {
    //             $msg = trans('words.please_enter') . $valid;
    //             return $this->responseError(0, $msg);
    //         }

    //         $devicetype = empty($_SERVER["HTTP_DEVICETYPE"]) ? "" : $_SERVER["HTTP_DEVICETYPE"];
    //         $versioncode = empty($_SERVER["HTTP_VERSIONCODE"]) ? "" : $_SERVER["HTTP_VERSIONCODE"];
    //         if(CommonController::versionCheck($devicetype,$versioncode)){
    //           return AppBaseController::responseError(26, trans('words.update_app_msg'));
    //         }

    //         if (CommonController::checkKeyExist($key) == 0) {
    //             return AppBaseController::responseError(0, trans('words.incorrect_key'));
    //         }

    //         CommonController::mailContactUs($name,$email,$subject,$message);
    //         return AppBaseController::responseError(1, "Thank you for contacting ".env('APP_NAME') .". We will get back to you shortly.");


    //     } catch (\Exception $e) {
    //         return $this->responseError(0, $e->getMessage());
    //     }
    // }

    public function getUserProfile(Request $request)
    {
        try {

            extract($request->all());

            $key = $request->header('key');
            $token = $request->header('token');
            $valid = AppBaseController::requiredValidation([
                'key' => @$key,
                'token' => @$token,
                'user_id' => @$user_id,
                'to_user_id' => @$to_user_id,
            ]);
            if ($valid != '') {
                $msg = trans('words.please_enter') . $valid;
                return $this->responseError(0, $msg);
            }

            $devicetype = empty($_SERVER["HTTP_DEVICETYPE"]) ? "" : $_SERVER["HTTP_DEVICETYPE"];
            $versioncode = empty($_SERVER["HTTP_VERSIONCODE"]) ? "" : $_SERVER["HTTP_VERSIONCODE"];
            if(CommonController::versionCheck($devicetype,$versioncode)){
              return AppBaseController::responseError(26, trans('words.update_app_msg'));
            }

            if (CommonController::checkKeyExist($key) == 0) {
                return AppBaseController::responseError(0, trans('words.incorrect_key'));
            }else if (CommonController::checkKeyTokenExist($key, $token) == 0) {
                return AppBaseController::responseError(0, trans('words.incorrect_token'));
            }

                $userDtl = User_Master::where(['user_id'=>$user_id])->first();

                if (empty($userDtl)) {
                    return AppBaseController::responseError(0, trans('words.invalid_user_id'));
                }
                else {
                    $touserDtl = User_Master::where(['user_id'=>$to_user_id])->first();

                    if (empty($touserDtl)) {
                        return AppBaseController::responseError(0, trans('words.not_registered_user'));
                    }

                    $user_detail = User_Master::where('user_id',$user_id)->with("media_detail")->first();

                    $tokenDtl = Key_Token_Master::orderBy(DB::raw('RAND()'))->first();

                    $data['data'] = $this->getUserDtl($user_detail,$tokenDtl->token);

                    return AppBaseController::successResponse($data, 1, trans('words.get_profile_success'), "True");
                }

        } catch (\Exception $e) {
            return $this->responseError(0, $e->getMessage());
        }
    }

    public function ageCalculator(Request $request)
    {
        try {
            extract($request->all());

            $key = $request->header('key');
            $valid = AppBaseController::requiredValidation([
                'key' => @$key,
                'dob' => @$dob,
            ]);
            if ($valid != '') {
                $msg = trans('words.please_enter') . $valid;
                return $this->responseError(0, $msg);
            }

            $devicetype = empty($_SERVER["HTTP_DEVICETYPE"]) ? "" : $_SERVER["HTTP_DEVICETYPE"];
            $versioncode = empty($_SERVER["HTTP_VERSIONCODE"]) ? "" : $_SERVER["HTTP_VERSIONCODE"];
            if(CommonController::versionCheck($devicetype,$versioncode)){
                return AppBaseController::responseError(26, trans('words.update_app_msg'));
            }

            if (CommonController::checkKeyExist($key) == 0) {
                return AppBaseController::responseError(0, trans('words.incorrect_key'));
            }
                $dob = $request['dob'];
                $condate = date("Y-m-d");
                $birthdate = new DateTime(date("Y-m-d",  strtotime(implode('-', array_reverse(explode('/', $dob))))));
                $today= new DateTime(date("Y-m-d",  strtotime(implode('-', array_reverse(explode('/', $condate))))));
                $age = $birthdate->diff($today)->y;

                $data['data'] = $age ." years old";
              return AppBaseController::successResponse($data,1, trans('words.age_calculator'), "True");

        } catch (\Exception $e) {
            return $this->responseError(0, $e->getMessage());
        }
    }


    // public function deleteAccount(Request $request)
    // {
    //     try {

    //         extract($request->all());

    //         $key = $request->header('key');
    //         $token = $request->header('token');
    //         $valid = AppBaseController::requiredValidation([
    //             'key' => @$key,
    //             'token' => @$token,
    //             'user_id' => @$user_id,
    //             'delete_reason' => $delete_reason
    //         ]);
    //         if ($valid != '') {
    //             $msg = trans('words.please_enter') . $valid;
    //             return $this->responseError(0, $msg);
    //         }

    //         $devicetype = empty($_SERVER["HTTP_DEVICETYPE"]) ? "" : $_SERVER["HTTP_DEVICETYPE"];
    //         $versioncode = empty($_SERVER["HTTP_VERSIONCODE"]) ? "" : $_SERVER["HTTP_VERSIONCODE"];
    //         if(CommonController::versionCheck($devicetype,$versioncode)){
    //           return AppBaseController::responseError(26, trans('words.update_app_msg'));
    //         }

    //         if (CommonController::checkKeyExist($key) == 0) {
    //             return AppBaseController::responseError(0, trans('words.incorrect_key'));
    //         }else if (CommonController::checkKeyTokenExist($key, $token) == 0) {
    //             return AppBaseController::responseError(0, trans('words.incorrect_token'));
    //         }

    //             $userDtl = User_Master::where(['user_id'=>$user_id,'is_delete'=>0])->first();

    //             if (empty($userDtl)) {
    //                 return AppBaseController::responseError(0, trans('words.invalid_user_id'));
    //             }
    //             else {


    //                 $updateobj = ["is_delete"=>1,"delete_reason"=>$delete_reason];
    //                 User_Master::where("user_id",$user_id)->update($updateobj);


    //                 return AppBaseController::responseSuccess(1, trans('words.delete_account_success'), "True");
    //             }

    //     } catch (\Exception $e) {
    //         return $this->responseError(0, $e->getMessage());
    //     }
    // }

    public function getRankPerson(Request $request)
    {
        try {
            extract($request->all());

            $key = $request->header('key');
            $valid = AppBaseController::requiredValidation([
                'key' => @$key,
              //  'rank' => @$rank,
                'gender' => @$gender,
            ]);
            if ($valid != '') {
                $msg = trans('words.please_enter') . $valid;
                return $this->responseError(0, $msg);
            }

            $devicetype = empty($_SERVER["HTTP_DEVICETYPE"]) ? "" : $_SERVER["HTTP_DEVICETYPE"];
            $versioncode = empty($_SERVER["HTTP_VERSIONCODE"]) ? "" : $_SERVER["HTTP_VERSIONCODE"];
            if(CommonController::versionCheck($devicetype,$versioncode)){
              return AppBaseController::responseError(26, trans('words.update_app_msg'));
            }

            if (CommonController::checkKeyExist($key) == 0) {
                return AppBaseController::responseError(0, trans('words.incorrect_key'));
            }
            $rank = $request['rank'];
            if(!isset($rank) && $rank == ''){
                $rank = '';
            }
            $userDtl = PlugspaceUser::where('rank',$rank);
            if($gender == 'Biologically Female' || $gender == 'Female'){
                $userDtl = $userDtl->where('gender','female');
            }elseif($gender == 'Biologically Male' || $gender == 'Male'){
                $userDtl = $userDtl->where('gender','male');
            }elseif($gender == 'Other'){
                $userDtl = $userDtl->where('gender','other');
            }
            $userDtl = $userDtl->pluck('name')->toArray();

            $data['data'] = $userDtl;
            return AppBaseController::successResponse($data,1, trans('words.list_success'), "True");
        } catch (\Exception $e) {
            return $this->responseError(0, $e->getMessage());
        }
    }

     public function previewUpdatePro(Request $request)
    {
        try {

            extract($request->all());

            $key = $request->header('key');
            $token = $request->header('token');
            $valid = AppBaseController::requiredValidation([
                'key' => @$key,
                'token' => @$token,
                'user_id' => @$user_id,
               // 'type' => @$type,
            ]);
            if ($valid != '') {
                $msg = trans('words.please_enter') . $valid;
                return $this->responseError(0, $msg);
            }

            $devicetype = empty($_SERVER["HTTP_DEVICETYPE"]) ? "" : $_SERVER["HTTP_DEVICETYPE"];
            $versioncode = empty($_SERVER["HTTP_VERSIONCODE"]) ? "" : $_SERVER["HTTP_VERSIONCODE"];
            if(CommonController::versionCheck($devicetype,$versioncode)){
              return AppBaseController::responseError(26, trans('words.update_app_msg'));
            }

            if (CommonController::checkKeyExist($key) == 0) {
                return AppBaseController::responseError(0, trans('words.incorrect_key'));
            }else if (CommonController::checkKeyTokenExist($key, $token) == 0) {
                return AppBaseController::responseError(0, trans('words.incorrect_token'));
            }

                $userDtl = User_Master::where(['user_id'=>$user_id])->first();

                if (empty($userDtl)) {
                    return AppBaseController::responseError(0, trans('words.invalid_user_id'));
                }else {

                    if(!isset($type) && $type == ''){
                        $type = '';
                    }
                    if($type == 'profile'){
                         if (isset($remove_media_id) && $remove_media_id != "") {
                            $removeMediaIds = explode(",", $remove_media_id);
                            foreach ($removeMediaIds as $key => $media_id) {

                                $getMedia = User_Media_Master::where("media_id",$media_id)->first();
                                if (!empty($getMedia)) {
                                    $rootPath = str_replace('\\', '/', __DIR__);
                                    $homePage = explode("/app", $rootPath)[0].'/public/profile/';
                                    $mediaName = basename($getMedia->profile);
                                    if (file_exists($homePage.$mediaName)) {
                                        unlink($homePage.$mediaName);
                                        User_Media_Master::where("media_id",$media_id)->delete();
                                   }
                                }
                            }
                        } 

                        if (isset($request['profile'])) {

                                $time = time() .  uniqid();
                                $newname = $time . '.' . $request['profile']->getClientOriginalExtension();
                                $media_type = explode("/", $request['profile']->getMimeType());

                                $request['profile']->move(public_path('profile'), $newname);

                                $insertMediaObj = [
                                    'profile' => $newname,
                                    'media_type' => $media_type,
                                ];
                                User_Media_Master::where('media_id',$media_id)->update($insertMediaObj);
                        }

                        CommonController::updateProfileFirebase($user_id);

                    }else if($type == 'feed'){

                         if (isset($remove_media_id) && $remove_media_id != "") {
                           // $removeMediaIds = explode(",", $remove_media_id);
                          //  foreach ($removeMediaIds as $key => $media_id) {

                                $getMedia = Feed_Master::where('feed_id',$remove_media_id)->first();
                                if (!empty($getMedia)) {
                                    $rootPath = str_replace('\\', '/', __DIR__);
                                    $homePage = explode("/app", $rootPath)[0].'/public/story/';
                                    $mediaName = basename($getMedia->feed_image);
                                    if (file_exists($homePage.$mediaName)) {
                                        unlink($homePage.$mediaName);
                                        Feed_Master::where('feed_id',$remove_media_id)->delete();
                                   }
                                }
                           // }
                        } 
                        
                        if (isset($request['feed_image'])) {
                                $time = time() .  uniqid();
                                $newname = $time . '.' . $request['feed_image']->getClientOriginalExtension();

                                $request['feed_image']->move(public_path('story'), $newname);

                                $insertMediaObj = [
                                    'feed_image' => $newname,
                                ];
                                Feed_Master::where('feed_id',$feed_id)->update($insertMediaObj);
                        }
                    }


                    $user_detail = User_Master::where('user_id',$user_id)->first();

                    $tokenDtl = Key_Token_Master::orderBy(DB::raw('RAND()'))->first();

                    $data['data'] = $this->getUserDtl($user_detail,$tokenDtl->token);

                    return AppBaseController::successResponse($data, 1, trans('words.profile_update'), "True");
                }

        } catch (\Exception $e) {
            return $this->responseError(0, $e->getMessage());
        }
    }

    public function contactUs(Request $request)
    {
        try {
            extract($request->all());

            $key = $request->header('key');
            $token = $request->header('token');
            $valid = AppBaseController::requiredValidation([
                'key' => @$key,
                'token' => @$token,
                'email'=> @$email,
                'name'=> @$name,
                'subject'=> @$subject,
                'message'=> @$message
            ]);
            if ($valid != '') {
                $msg = trans('words.please_enter') . $valid;
                return $this->responseError(0, $msg);
            }

            $devicetype = empty($_SERVER["HTTP_DEVICETYPE"]) ? "" : $_SERVER["HTTP_DEVICETYPE"];
            $versioncode = empty($_SERVER["HTTP_VERSIONCODE"]) ? "" : $_SERVER["HTTP_VERSIONCODE"];
            if(CommonController::versionCheck($devicetype,$versioncode)){
              return AppBaseController::responseError(26, trans('words.update_app_msg'));
            }

            if (CommonController::checkKeyExist($key) == 0) {
                return AppBaseController::responseError(0, trans('words.incorrect_key'));
            }

            CommonController::mailContactUs($name,$email,$subject,$message);
            return AppBaseController::responseError(1, "Thank you for contacting ".env('APP_NAME') .". We will get back to you shortly.");


        } catch (\Exception $e) {
            return $this->responseError(0, $e->getMessage());
        }
    }

    public function Subscription(Request $request){
        try {
            extract($request->all());

            $key = $request->header('key');
            $token = $request->header('token');
            $valid = AppBaseController::requiredValidation([
                'key' => @$key,
                'token' => @$token,
                'user_id' => @$user_id,
                'validity' => @$validity,
                'plan_name' => @$plan_name,
                'transaction_id' => @$transaction_id,
                'amount' => @$amount
            ]);
            if ($valid != '') {
                $msg = trans('words.please_enter') . $valid;
                return $this->responseError(0, $msg);
            }

            $devicetype = empty($_SERVER["HTTP_DEVICETYPE"]) ? "" : $_SERVER["HTTP_DEVICETYPE"];
            $versioncode = empty($_SERVER["HTTP_VERSIONCODE"]) ? "" : $_SERVER["HTTP_VERSIONCODE"];
            if(CommonController::versionCheck($devicetype,$versioncode)){
              return AppBaseController::responseError(26, trans('words.update_app_msg'));
            }

            if (CommonController::checkKeyExist($key) == 0) {
                return AppBaseController::responseError(0, trans('words.incorrect_key'));
            }
            else if (CommonController::checkKeyTokenExist($key, $token) == 0) {
                return AppBaseController::responseError(0, trans('words.incorrect_token'));
            }


            $userDtl = User_Master::where(['user_id'=>$user_id])->first();

            if (empty($userDtl)) {
                return AppBaseController::responseError(0, trans('words.invalid_user_id'));
            }


            User_Master::where("user_id",$user_id)->update(["is_subscribe"=>1]);

            $curDate = date("Y-m-d H:i:s");
            $expiry = date("Y-m-d H:i:s",strtotime("$validity months", strtotime($curDate)));
            $insertDt = ["transaction_id"=>$transaction_id,"user_id"=>$user_id,"amount"=>$amount,"validity"=>$expiry,"plan_name"=>$plan_name];
            User_Subscription_Master::create($insertDt);

            $user_detail = User_Master::where("user_id",$user_id)->first();
            $tokenDtl = Key_Token_Master::orderBy(DB::raw('RAND()'))->first();

            $data['data'] = $this->getUserDtl($user_detail,$tokenDtl->token);

            return AppBaseController::successResponse($data,1,"Your order has been placed successfully.",'True');


        } catch (Exception $e) {
            return $this->responseError(0, $e->getMessage());
        }
    }

    public function testPush(Request $request)
    {
        return CommonController::sendPush("Virag Tadhani","View your profile",$request['token']);
    }

    public function testMail(Request $request)
    {
        $data = CommonController::otherUserReportProfile("Virag","Hello");
        dd($data);  
        return $data;
    }


}

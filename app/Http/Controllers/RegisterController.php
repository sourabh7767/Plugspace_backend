<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\User_Master;
use Illuminate\Http\Request;
use Socialite;
use Session;

class RegisterController extends Controller
{
    public function getVerifyMail(Request $request)
    {
        $input = $request->all();
        $email = base64_decode($input['email']);
        $name = '';

        $check_user = User_Master::where('email', $email)->first();
        if (!empty($check_user) || $check_user != '') {
                   $name = $check_user->first_name .' '. $check_user->last_name;
            $check_user1 = User_Master::where('email', $email)->where('is_confirm', 0)->first();
            if (!empty($check_user1) || $check_user1 != '') {
                $updateObj = User_Master::where('email', $email)->update(['is_confirm' => 1]);
                $success = "Your registration has been done successfully.<br/>Thank you.";
                $request->session()->put("success", $success);
                $code = 1;
            } else {
                $code = 0;
                $error = "Your email is already confirmed.<br/>Thank you.";
                $request->session()->put("error", $error);
            }
        } else {
            $code = 0;
            $error = 'Email Address Not Exist.';
            $request->session()->put("error", $error);
        }
        $data1 = array('code' => $code, 'name' => $name);
        return view('registers.verify_email', compact('request', 'data1'));
    }

    public function resetPwdMail()
    {
        $error = '';
        return view('registers.reset_pwd', compact('error'));
    }

    public function resetPwdMail1(Request $request)
    {
        $in = $request->all();
        $error = '';
        $email = ($in['email']);
        $user = User_Master::where('email', $email)->first();
        if (empty($user) || $user == null) {
            $error = 4;
            return $error;
        } else {
            if ($user->ucode != '') {
                $npass = $request->npass;
                $cpass = $request->cpass;
                if ($npass == $cpass) {
                    $newpass = md5($npass);
                    $update = User_Master::where('email', $email)->update(['ucode' => '', 'password' => $newpass]);
                    $error = 1;
                    return $error;
                } else {
                    $error = 2;
                    return $error;
                }
            } else {
                $error = 3;
                return $error;
            }
        }
    }

    public function resetPwdDone()
    {
        return view('registers.resetPasswordDone');
    }    
}

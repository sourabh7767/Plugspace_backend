<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Models\Key_Master;
use App\Models\Key_Token_Master;
use App\Models\User_Master;
use App\Models\User_Subscription_Master;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\CommonController;

class PaymentAPIController extends AppBaseController
{
    public function localeSetting($lang = 'en')
    {
        $a = \App::setlocale($lang);
    }


    public function paywithStripe(Request $request){
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
                'user_id' => @$user_id,
                'card_number' => @$card_number,
                'exp_month' => @$exp_month,
                'exp_year' => @$exp_year,
                'card_cvv' => @$card_cvv,
                'amount' => @$amount,
                'validity' => @$validity,
                'plan_name' => @$plan_name,
            ]);
            if ($valid != '') {
                $msg = trans('words.please_enter') . $valid;
                return $this->responseError(0, $msg);
            }

            $userDtl = User_Master::where(['user_id'=>$user_id])->first();

            if (empty($userDtl)) {
                return AppBaseController::responseError(0, trans('words.invalid_user_id'));
            }
             if($userDtl->status == '1'){
                return AppBaseController::responseError(5,  trans('words.account_deactive'));
            }

            $transaction_id = $this->stripePayment($amount,$card_number,$exp_month,$exp_year,$card_cvv,"order from happly");
            if ($transaction_id != "" && $transaction_id != NULL) {
              $data['transaction_id'] = $transaction_id;

                User_Master::where("user_id",$user_id)->update(["is_subscribe"=>1]);
                $curDate = date("Y-m-d H:i:s");
                $expiry = date("Y-m-d H:i:s",strtotime("$validity months", strtotime($curDate)));
                $insertDt = ["transaction_id"=>$transaction_id,"user_id"=>$user_id,"amount"=>$amount,"validity"=>$expiry,"plan_name"=>$plan_name];

              User_Subscription_Master::create($insertDt);
              
              return AppBaseController::successResponse($data,1,"Your order has been placed successfully.",'True');
            }
            else
            {
              return AppBaseController::responseSuccess(0, "Your payment is decline", "False");
            }
            
        } catch (Exception $e) {
            return $this->responseError(0, $e->getMessage());
        }
    }

    public function stripePayment($total_amount,$card_number,$exp_month,$exp_year,$card_cvv,$trndesc,$stripeToken="")
    {
        if ($stripeToken == "" || $stripeToken == null) {
            
            $card_field = [
                'card' => [
                    'number'    => $card_number,
                    'exp_month' => $exp_month,
                    'exp_year'  => $exp_year,
                    'cvc'       => $card_cvv,
                ],
            ];
            $fields_string = http_build_query($card_field);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://api.stripe.com/v1/tokens');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_USERPWD, env('STRIPE_SECRET') . ':' . '');
            $result = curl_exec($ch);
            $result = json_decode($result);
            
            if (curl_errno($ch)) {
                echo 'Error:' . curl_error($ch);
            }
            curl_close($ch);
            
            $stripeToken = $result->id ?? "";

            
        }
        if ($stripeToken == "" || $stripeToken == NULL) {
            return "";
        }
        else
        {
            $total_amount = number_format((float)$total_amount*100., 0, '.', ''); // convert dollar to cent
            $post_field = [
                'card' => $stripeToken,
                'currency' => 'USD',
                'amount' => $total_amount,
                'description' => $trndesc,
            ];
            $fields_string = http_build_query($post_field);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://api.stripe.com/v1/charges');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_USERPWD, env('STRIPE_SECRET') . ':' . '');
            $charge = curl_exec($ch);
            if (curl_errno($ch)) {
                echo 'Error:' . curl_error($ch);
            }
            curl_close($ch);
            $charge = json_decode($charge);
            if ($charge->status == 'succeeded') {
                $transaction_id = $charge->id;

            } else {
                $transaction_id = "";
            }

            return $transaction_id;
        }
    }

    
}

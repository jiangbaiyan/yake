<?php
/**
 * Created by PhpStorm.
 * User: Baiyan
 * Date: 2018/4/5
 * Time: 18:13
 */

namespace App\Http\Controllers\Common;
use App\Http\Helper\Controller;
use App\Service\Common\SmsService;
use App\Service\Common\WeChatService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller{

    /**
     * 获取短信验证码
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCode(Request $request){
        $validator = Validator::make($request->all(),['phone' => 'required|unique:users']);
        if ($validator->fails()){
            return $this->responseParamValidateFailed($validator->messages());
        }
        $phone = $request->phone;
        $res = SmsService::getCode($phone);
        //成功返回true，报错返回数组
        if (is_bool($res)){
            Log::info('send code to'.$phone.'successfully');
            return $this->responseSuccess();
        }
        else{
            Log::error($res['Message']);
            return $this->responseOperationFailed($res);
        }
    }

    /**
     * 验证两次验证码是否正确
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyCode(Request $request){
        $validator = Validator::make($request->all(),['phone' => 'required|unique:users','code' => 'required']);
        if ($validator->fails()){
            return $this->responseParamValidateFailed($validator->messages());
        }
        $phone = $request->phone;
        $code = $request->code;
        if (SmsService::verifyCode($phone,$code)){
            Log::info('用户'.$phone.'短信验证成功');
            return $this->responseSuccess();
        }
        else{
            Log::info('用户'.$phone.'短信验证失败');
            return $this->responseOperationFailed();
        }
    }

    /**
     * 微信网页授权并拉取用户个人信息
     * @param $step
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function getWeChatUserInfo($step,Request $request){
        switch ($step){
            case 1://初次请求
                $validator = Validator::make($request->all(),['phone' => 'required','password' => 'required']);
                if ($validator->fails()){
                    return $this->responseParamValidateFailed($validator->messages());
                }
                Session::put('phone',$request->phone);
                Session::put('password',$request->password);
                return WeChatService::getCode();
                break;
            case 2://微信回调地址
                $res = WeChatService::callback($request);
                //如果返回的是数组，说明有错误
                if (is_array($res)){
                    Log::error($res['errmsg']);
                    return $this->responseOperationFailed($res['errmsg']);
                }
                return $this->responseSuccess();
                break;
        }
    }
}
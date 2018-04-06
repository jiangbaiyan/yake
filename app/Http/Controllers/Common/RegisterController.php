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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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
        //如果返回的不是数组（只有报错才返回数组），那么直接返回验证码
        if (!is_array($res)){
            Log::info('send code to'.$phone.'successfully');
            return $this->responseSuccess($res);
        }
        else{
            Log::error($res);
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
            return $this->responseOperationFailed('wrong code');
        }
    }

}
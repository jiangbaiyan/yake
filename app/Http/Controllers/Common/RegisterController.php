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
use App\Exceptions\ResourceNotFoundException;
use App\Exceptions\OperateFailedException;

class RegisterController extends Controller
{

    /**
     * 获取短信验证码
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCode(Request $request)
    {
        $validator = Validator::make($request->all(), ['phone' => 'required|unique:users']);
        if ($validator->fails()) {
            return $this->responseParamValidateFailed($validator->messages());
        }
        $phone = $request->phone;
        try {
            SmsService::getCode($phone);
        } catch (OperateFailedException $e) {
            Log::error($e->getMessage());
            return $this->responseOperateFailed($e->getMessage());
        }
        Log::info('send code to ' . $phone . ' successfully');
        return $this->responseSuccess();
    }

    /**
     * 验证验证码是否正确
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyCode(Request $request)
    {
        $validator = Validator::make($request->all(), ['phone' => 'required|unique:users', 'code' => 'required']);
        if ($validator->fails()) {
            return $this->responseParamValidateFailed($validator->messages());
        }
        $phone = $request->phone;
        $code = $request->code;
        try{
            SmsService::verifyCode($phone,$code);
        }catch (ResourceNotFoundException $e){
            Log::info('用户' . $phone . '短信验证失败'.'错误信息为'.$e->getMessage());
            return $this->responseResourceNotFound($e->getMessage());
        }catch (OperateFailedException $e){
            Log::info('用户' . $phone . '短信验证失败'.'错误信息为'.$e->getMessage());
            return $this->responseOperateFailed($e->getMessage());
        }
        Log::info('用户' . $phone . '短信验证成功');
        return $this->responseSuccess();
    }

    /**
     * 微信网页授权并拉取用户个人信息
     * @param $step
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function getWeChatUserInfo($step, Request $request)
    {
        switch ($step) {
            case 1://初次请求
                $validator = Validator::make($request->all(), ['phone' => 'required', 'password' => 'required']);
                if ($validator->fails()) {
                    return $this->responseParamValidateFailed($validator->messages());
                }
                Session::put('phone', $request->phone);
                Session::put('password', $request->password);
                return $this->responseSuccess(WeChatService::getCode());
                break;
            case 2://微信回调地址
                try {
                    $res = WeChatService::callback($request);
                } catch (OperateFailedException $e) {
                    Log::error($e->getMessage());
                    return $this->responseOperateFailed($e->getMessage());
                }
                return $this->responseSuccess($res);
                break;
        }
    }
}
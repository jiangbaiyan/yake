<?php
/**
 * Created by PhpStorm.
 * User: Baiyan
 * Date: 2018/4/5
 * Time: 18:13
 */

namespace App\Http\Controllers\Common;

use App\Exceptions\ParamValidateFailedException;
use App\Helper\Controller;
use App\Service\SmsService;
use App\Service\WeChatService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use App\Exceptions\ResourceNotFoundException;
use App\Exceptions\OperateFailedException;

class RegisterController extends Controller{

    /**
     * 获取短信验证码
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws OperateFailedException
     * @throws ParamValidateFailedException
     */
    public function getCode(Request $request)
    {
        $validator = Validator::make($request->all(), ['phone' => 'required|unique:users']);
        if ($validator->fails()) {
            throw new ParamValidateFailedException($validator);
        }
        $phone = $request->phone;
        SmsService::getCode($phone);
        return $this->responseSuccess();
    }

    /**
     * 验证验证码是否正确
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws OperateFailedException
     * @throws ParamValidateFailedException
     * @throws ResourceNotFoundException
     */
    public function verifyCode(Request $request)
    {
        $validator = Validator::make($request->all(), ['phone' => 'required|unique:users', 'code' => 'required']);
        if ($validator->fails()) {
            throw new ParamValidateFailedException($validator);
        }
        $phone = $request->phone;
        $code = $request->code;
        SmsService::verifyCode($phone,$code);
        return $this->responseSuccess();
    }

    /**
     * 微信网页授权并拉取用户个人信息
     * @param $step
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws OperateFailedException
     * @throws ParamValidateFailedException
     */
    public function getWeChatUserInfo($step, Request $request)
    {
        switch ($step) {
            case 1://初次请求
                $validator = Validator::make($request->all(), ['phone' => 'required', 'password' => 'required','age' => 'required']);
                if ($validator->fails()) {
                    throw new ParamValidateFailedException($validator);
                }
                Session::put('phone', $request->phone);
                Session::put('password', $request->password);
                Session::put('age',$request->age);
                return $this->responseSuccess(WeChatService::getCode());
                break;
            case 2://微信回调地址
                if (WeChatService::callback($request)){
                    echo '<strong>恭喜您，注册成功！</strong>><br>三秒后自动跳转到登录界面。';
                    header("refresh:3;url=https://yake.hzcloudservice/mobilepages/login.html");
                }
                else{
                    echo '抱歉，注册失败，请重试';
                }
                break;
        }
    }
}
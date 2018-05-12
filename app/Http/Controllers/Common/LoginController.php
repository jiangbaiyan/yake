<?php
/**
 * Created by PhpStorm.
 * User: Baiyan
 * Date: 2018/4/11
 * Time: 14:22
 */
namespace App\Http\Controllers\Common;

use App\Exceptions\OperateFailedException;
use App\Exceptions\ParamValidateFailedException;
use App\Exceptions\ResourceNotFoundException;
use App\Exceptions\UnAuthorizedException;
use App\Helper\Controller;
use App\Model\UserModel;
use App\Service\LoginService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\VarDumper\Dumper\DataDumperInterface;

class LoginController extends Controller{

    /**
     * 登录
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws OperateFailedException
     * @throws ResourceNotFoundException
     * @throws ParamValidateFailedException
     */
    public function login(Request $request){
        $validator = Validator::make($request->all(),['phone' => 'required','password' => 'required']);
        if ($validator->fails()){
            throw new ParamValidateFailedException($validator);
        }
        $token = LoginService::login($request->phone, $request->password);
        return $this->responseSuccess(['jwtToken' => $token]);
    }

    /**
     * 获取个人信息
     * @throws \App\Exceptions\UnAuthorizedException
     */
    public function getOwnInfo(){
        $user = UserModel::getCurUser();
        $data = [
            'nickname' => $user->nickname,
            'age' => $user->age,
            'sex' => $user->sex,
            'avatar' => $user->avatar
        ];
        return $this->responseSuccess($data);
    }

    /**
     * 判断当前用户是否登录
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\UnAuthorizedException
     */
    public function isLogin(){
        if (!$user = \Auth::user()){
            throw new UnAuthorizedException();
        }
        return $this->responseSuccess();
    }
}
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
use App\Helper\Controller;
use App\Service\LoginService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
            throw new ParamValidateFailedException($validator->messages());
        }
        $token = LoginService::login($request->phone, $request->password);
        return $this->responseSuccess(['jwtToken' => $token]);
    }
}
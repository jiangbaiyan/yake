<?php
/**
 * Created by PhpStorm.
 * User: Baiyan
 * Date: 2018/4/11
 * Time: 14:22
 */
namespace App\Http\Controllers\Common;

use App\Exceptions\OperateFailedException;
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
     */
    public function login(Request $request){
        $validator = Validator::make($request->all(),['phone' => 'required','password' => 'required']);
        if ($validator->fails()){
            return $this->responseParamValidateFailed($validator->messages());
        }
        try {
            $token = LoginService::login($request->phone, $request->password);
        } catch (OperateFailedException $e) {
            return $this->responseOperateFailed($e->getMessage());
        } catch (ResourceNotFoundException $e) {
            return $this->responseResourceNotFound($e->getMessage());
        }
        return $this->responseSuccess(['jwtToken' => $token]);
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: Baiyan
 * Date: 2018/4/11
 * Time: 14:28
 */
namespace App\Service;

use App\Exceptions\OperateFailedException;
use App\Exceptions\ResourceNotFoundException;
use App\Helper\ConstHelper;
use App\Model\UserModel;
use Illuminate\Support\Facades\Auth;

class LoginService{

    /**
     * 登陆逻辑
     * @param $phone
     * @param $password
     * @return string
     * @throws OperateFailedException
     * @throws ResourceNotFoundException
     */
    public static function login($phone,$password){
        $user = UserModel::where('phone',$phone)->first();
        if (!$user){
            throw new ResourceNotFoundException(ConstHelper::USER);
        }
        if (!$token = Auth::attempt(['phone' => $phone,'password' => $password])){
            throw new OperateFailedException(ConstHelper::WRONG_PASSWORD);
        }
        if (!$token){
            throw new OperateFailedException(ConstHelper::TOKEN_SET_FAILED);
        }
        return $token;
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: Baiyan
 * Date: 2018/4/11
 * Time: 14:28
 */
namespace App\Service\Common;

use App\Exceptions\OperateFailedException;
use App\Exceptions\ResourceNotFoundException;
use App\Model\UserModel;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

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
            throw new ResourceNotFoundException('user not found');
        }
        if (!Hash::check($password,$user->password)){
            throw new OperateFailedException('wrong password');
        }
        $token = JWTAuth::fromUser($user);
        if (!$token){
            throw new OperateFailedException('token set failed');
        }
        return $token;
    }
}
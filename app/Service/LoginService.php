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
use App\Model\UserModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
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
        if (!$token = Auth::attempt(['phone' => $phone,'password' => $password])){
            throw new OperateFailedException('wrong password');
        }
        if (!$token){
            throw new OperateFailedException('token set failed');
        }
        Session::put('user',$user);
        return $token;
    }
}
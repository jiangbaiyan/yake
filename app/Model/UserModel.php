<?php

namespace App\Model;

use App\Exceptions\UnAuthorizedException;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Foundation\Auth\User as Authenticatable;

class UserModel extends Authenticatable implements JWTSubject
{
    protected $table = 'users';

    protected $guarded = ['id'];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * 获取当前登录用户
     * @return mixed
     * @throws UnAuthorizedException
     */
    public static function getCurUser(){
        if (!$user = \Auth::user()){
            throw new UnAuthorizedException();
        }
        return $user;
    }

    /**
     * 管理员发送的通知关联
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function infos(){
        return $this->hasMany(InfoModel::class,'user_id','id');
    }

    /**
     * 管理员发放的优惠券关联
     */
    public function sendedCoupons(){
        return $this->hasMany(InfoModel::class,'sender_id','id');
    }

    /**
     * 用户领取的优惠券关联
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function coupons(){
        return $this->hasMany(CouponModel::class,'user_id','id');
    }
}

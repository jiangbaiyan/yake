<?php
/**
 * Created by PhpStorm.
 * User: Baiyan
 * Date: 2018/4/28
 * Time: 9:23
 */
namespace App\Http\Controllers\User\Coupon;

use App\Exceptions\OperateFailedException;
use App\Exceptions\ParamValidateFailedException;
use App\Exceptions\ResourceNotFoundException;
use App\Helper\ConstHelper;
use App\Helper\Controller;
use App\Model\CouponModel;
use App\Model\UserModel;
use Illuminate\Support\Facades\Redis;

class CouponController extends Controller{

    /**
     * 获取所有可领取的优惠券
     * @return \Illuminate\Http\JsonResponse
     */
    public function getNotGrabbedCoupon(){
        $couponModel = new CouponModel();
        $data = \DB::select("select type,price,count(*) as amount from coupons where status = ? and expire_time >= ? group by type,price",[$couponModel::statusNotGrabbed,date('Y-m-d H:i:s')]);
        return $this->responseSuccess($data);
    }

    /**
     * 获取自己拥有的优惠券
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\UnAuthorizedException
     */
    public function getOwnCoupon(){
        $user = UserModel::getCurUser();
        $data = $user->coupons()
            ->where('status', CouponModel::statusGrabbed)
            ->where('expire_time','>=',date('Y-m-d H:i:s'))
            ->latest()
            ->get();
        return $this->responseSuccess($data);
    }

    /**
     * 抢优惠券
     * @param $type
     * @return \Illuminate\Http\JsonResponse
     * @throws OperateFailedException
     * @throws ParamValidateFailedException
     * @throws ResourceNotFoundException
     * @throws \App\Exceptions\UnAuthorizedException
     * @throws \Exception
     */
    public function getCoupon($type){
        if (!isset($type) || $type < 0 || $type > 5){
            throw new ParamValidateFailedException();
        }
        $user = UserModel::getCurUser();
        $couponModel = new CouponModel();
        //一种类型的优惠券,一个人只能在有效期内,获得一张
        $couponIsExist = $couponModel->where('user_id',$user->id)
            ->where('status',$couponModel::statusGrabbed)
            ->where('expire_time','>=',date('Y-m-d H:i:s'))
            ->where('type',$type)
            ->first();
        if ($couponIsExist){
            throw new OperateFailedException(ConstHelper::COUPON_EXIST);
        }
        \DB::beginTransaction();
        //从列表弹出特定种类的优惠券
        $couponId = Redis::lpop($type);
        if (!$couponId){
            throw new ResourceNotFoundException(ConstHelper::COUPON);
        }
        $coupon = $couponModel->find($couponId);
        if (!$coupon){
            throw new ResourceNotFoundException(ConstHelper::COUPON);
        }
        $coupon->user_id = $user->id;
        $coupon->status = $couponModel::statusGrabbed;
        if (!$coupon->save()){
            throw new OperateFailedException();
        }
        \DB::commit();
        return $this->responseSuccess();
    }
}
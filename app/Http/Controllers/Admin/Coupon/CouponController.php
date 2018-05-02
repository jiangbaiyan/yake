<?php
/**
 * Created by PhpStorm.
 * User: Baiyan
 * Date: 2018/4/28
 * Time: 9:11
 */

namespace App\Http\Controllers\Admin\Coupon;

use App\Exceptions\OperateFailedException;
use App\Exceptions\ParamValidateFailedException;
use App\Helper\ApiResponse;
use App\Helper\ConstHelper;
use App\Helper\Controller;
use App\Model\CouponModel;
use App\Model\UserModel;
use App\Service\WeChatService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Validator;

class CouponController extends Controller{

    /**
     * 管理员发放优惠券
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws OperateFailedException
     * @throws ParamValidateFailedException
     * @throws \Exception
     */
    public function giveCoupons(Request $request){
        $req = $request->all();
        $validator = Validator::make($req,[
            'price' => 'required',
            'type' => 'required|integer',
            'amount' => 'required|integer',
            'expireTime' => 'required|date|after:now'
        ]);
        if ($validator->fails()){
            throw new ParamValidateFailedException($validator);
        }
        if ($req['price'] <0 || $req['type'] <0 || $req['type'] > 5 ||$req['amount'] <0){
            throw new ParamValidateFailedException();
        }
        \DB::beginTransaction();
        for ($i=0;$i<$req['amount'];$i++){
            $coupon = CouponModel::create([
                'price' => $req['price'],
                'type' => $req['type'],
                'status' => CouponModel::statusNotGrabbed,
                'expire_time' => $req['expireTime'],
                'sender_id' => UserModel::getCurUser()->id
            ]);
            if (!$coupon){
                throw new OperateFailedException();
            }
            //放入优惠券于列表
            Redis::lpush($req['type'],$coupon->id);
        }
        WeChatService::sendCouponInfo($req['amount']);
        \DB::commit();
        return $this->responseSuccess();
    }


    /**
     * 管理员获取已经发送的优惠券列表(在期限内)
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\UnAuthorizedException
     */
    public function getSentCoupon(){
        $senderId = UserModel::getCurUser()->id;
        $data = \DB::select('select count(*) as amount,status,type,price,expire_time,created_at from coupons where sender_id = ? and expire_time >= ? group by type,status,price,expire_time,created_at order by created_at desc ',[$senderId,date('Y-m-d H:i:s')]);
        return $this->responseSuccess($data);
    }
}
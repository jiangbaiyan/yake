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
                'expire_time' => $req['expireTime']
            ]);
            if (!$coupon){
                throw new OperateFailedException();
            }
            //放入优惠券于列表
            Redis::lpush($req['type'],$coupon->id);
        }
        \DB::commit();
        return $this->responseSuccess();
    }

}
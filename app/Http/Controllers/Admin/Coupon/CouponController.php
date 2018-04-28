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
use App\Helper\Controller;
use App\Model\CouponModel;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
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
            'price' => 'required|min:0',
            'type' => 'required|min:0',
            'amount' => 'required|min:0',
            'expireTime' => 'required|regex:/^\d+ [a-z]+$/'
        ]);
        if ($validator->fails()){
            throw new ParamValidateFailedException($validator);
        }
        //前端传过来过期时间形如 3 minute/1 hour/2 day/3 month
        $timeArr = explode(' ',$req['expireTime']);
        $count = $timeArr[0];
        $timeType = $timeArr[1];
        if ($count>1){
            $timeType .= 's';
        }
        $parseStr = "+".$count.' '.$timeType;
        \DB::beginTransaction();
        for ($i=0;$i<$req['amount'];$i++){
            $conpon = CouponModel::create([
                'price' => $req['price'],
                'type' => $req['type'],
                'status' => CouponModel::statusNotGrabbed,
                'expire_time' => Carbon::parse($parseStr)->toDateTimeString()
            ]);
            if (!$conpon){
                throw new OperateFailedException();
            }
        }
        \DB::commit();
        return $this->responseSuccess();
    }
}
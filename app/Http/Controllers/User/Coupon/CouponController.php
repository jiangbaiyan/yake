<?php
/**
 * Created by PhpStorm.
 * User: Baiyan
 * Date: 2018/4/28
 * Time: 9:23
 */
namespace App\Http\Controllers\User\Coupon;

use App\Helper\Controller;
use App\Model\CouponModel;

class CouponController extends Controller{

    /**
     * 获取所有优惠券
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAdminGiveCouponList(){
        $couponModel = new CouponModel();
        $data = $couponModel->where(['status' => $couponModel::statusNotGrabbed])
            ->latest()
            ->get()
            ->groupBy('type');
        return $this->responseSuccess($data);
    }


}
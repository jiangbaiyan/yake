<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class CouponModel extends Model
{
    protected $table = 'coupons';

    protected $guarded = ['id'];

    /**
     * 优惠券状态
     */
    const
        statusNotGrabbed = 0,//已发放未被领取
        statusGrabbed = 1,//已被领取
        statusUsed = 2;//已使用

    /**
     *  优惠券种类
     */
    const
        typeCommon = 0,//通用
        typeRectify = 1,//矫正
        typeInsert = 2,//镶牙
        typeFill = 3,//补牙
        typeWash = 4,//洗牙
        typePlant = 5;//种植牙

}

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
        statusNotGrabbed = 0,
        statusNotUsed = 1,
        statusUsed = 2;

}

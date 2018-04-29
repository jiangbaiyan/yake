<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCouponsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->nullable()->comment('用户id');
            $table->float('price')->nullable()->comment('优惠券金额');
            $table->tinyInteger('status')->nullable()->comment('优惠券状态 0-已发放未被抢 1-未使用 2-已使用');
            $table->tinyInteger('type')->nullable()->comment('优惠券种类 0-通用 1-矫正 2-镶牙 3-补牙 4-洗牙 5-种植牙');
            $table->string('expire_time')->nullable()->comment('过期时间');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('coupons');
    }
}

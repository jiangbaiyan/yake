<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['prefix' => 'v1'], function () {
    Route::group(['prefix' => 'common'], function () {

        //获取验证码
        Route::post('getCode', 'Common\RegisterController@getCode');

        //验证验证码
        Route::post('verifyCode', 'Common\RegisterController@verifyCode');

        //微信授权并拉取用户个人信息，存储至数据库
        Route::any('getWeChatUserInfo/{step}', 'Common\RegisterController@getWeChatUserInfo');

        //登录
        Route::post('login', 'Common\LoginController@login');

        //判断是否已经登录
        Route::get('isLogin','Common\LoginController@isLogin');

    });

    Route::group(['middleware' => 'jwt'], function () {

        //管理员API
        Route::group(['prefix' => 'admin'], function () {
            Route::group(['middleware' => 'isAdmin'], function () {
                Route::group(['prefix' => 'info'], function () {

                    //发送通知
                    Route::post('send', 'Admin\Info\InfoController@send');

                    //获取所有通知
                    Route::get('allInfo', 'Admin\Info\InfoController@getAllInfo');

                    //获取某条通知的反馈情况
                    Route::get('infoFeedback/{infoId}', 'Admin\Info\InfoController@getInfoFeedback');

                });

                Route::group(['prefix' => 'coupon'],function (){

                    //发放优惠券
                    Route::post('give','Admin\Coupon\CouponController@giveCoupons');

                    //获取已发放的优惠券列表
                    Route::get('getSentCoupon','Admin\Coupon\CouponController@getSentCoupon');
                });
            });
        });



        //普通用户API
        Route::group(['prefix' => 'user'], function () {

            Route::group(['prefix' => 'info'], function () {

                //获取通知详情
                Route::get('infoDetail/{infoId}', 'User\Info\InfoController@getDetail');

                //获取收到的通知列表
                Route::get('infoReceiveList', 'User\Info\InfoController@getInfoList');

            });


            Route::group(['prefix' => 'coupon'],function(){

                //获取可领取的优惠券列表
                Route::get('notGrabbedCoupon','User\Coupon\CouponController@getNotGrabbedCoupon');

                //个人中心获取自己领取的优惠券
                Route::get('ownCoupon','User\Coupon\CouponController@getOwnCoupon');

                //领取优惠券
                Route::get('get/{type}','User\Coupon\CouponController@getCoupon');

                //使用优惠券
                Route::get('use/{couponId}','User\Coupon\CouponController@useCoupon');

                //获取自己的个人信息
                Route::get('ownInfo','Common\LoginController@getOwnInfo');
            });
        });
    });
});

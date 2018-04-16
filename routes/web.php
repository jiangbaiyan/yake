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

Route::group(['prefix' => 'v1'],function (){
    Route::group(['prefix' => 'common'],function (){

        //获取验证码
        Route::post('getCode','Common\RegisterController@getCode');

        //验证验证码
        Route::post('verifyCode','Common\RegisterController@verifyCode');

        //微信授权并拉取用户个人信息，存储至数据库
        Route::any('getWeChatUserInfo/{step}','Common\RegisterController@getWeChatUserInfo');

        //登录
        Route::post('login','Common\LoginController@login');

    });

    Route::group(['prefix' => 'admin/info'],function (){
        Route::group(['middleware' => 'jwt'],function (){
            Route::group(['middleware' => 'isAdmin'],function(){

                //发送通知
                Route::post('send','Admin\Info\InfoController@send');

                //获取所有通知
                Route::get('allInfo','Admin\Info\InfoController@getAllInfo');

                //获取某条通知的反馈情况
                Route::get('infoFeedback/{infoId}','Admin\Info\InfoController@getInfoFeedback');

                //获取通知详情
                Route::get('infoDetail','Admin\Info\InfoController@getDetail');
                
            });
        });
    });
});

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
        Route::post('getWeChatUserInfo/{step}','Common\RegisterController@getWeChatUserInfo');
    });
});

<?php
/**
 * Created by PhpStorm.
 * User: Baiyan
 * Date: 2018/4/5
 * Time: 18:12
 */

namespace App\Service\Common;

use App\Exceptions\OperateFailedException;
use App\Helper\ApiRequest;
use App\Model\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Tymon\JWTAuth\Facades\JWTAuth;

class WeChatService{
    use ApiRequest;

    //大荆口腔公众号基本配置
    private static $appId = 'wx48c158c300c446ec';
    private static $appKey = '3272591ea6a14977714f4d059d43d3ba';
    private static $baseUrl = 'https://yake.hzcloudservice.com/api/v1/common/getWeChatUserInfo/';

    /**
     * 第一步：用户同意授权，获取code
     */
    public static function getCode(){
        $appid = self::$appId;
        $redirectUrl = urlencode(self::$baseUrl . '2');
        $requestUrl = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=$appid&redirect_uri=$redirectUrl&response_type=code&scope=snsapi_userinfo&#wechat_redirect";
        return $requestUrl;
    }

    /**
     * 第二步：通过code换取网页授权access_token与openid，并拉取用户信息
     * @param Request $request
     * @return array|bool
     * @throws OperateFailedException
     */
    public static function callback(Request $request){
        $appid = self::$appId;
        $appKey = self::$appKey;
        $code = $request->code;
        $requestUrl = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=$appid&secret=$appKey&code=$code&grant_type=authorization_code";
        $res = self::sendRequest('GET', $requestUrl);
        if (isset($res['errcode'])) {
            throw new OperateFailedException($res['errcode']);
        }
        $accessToken = $res['access_token'];
        $openid = $res['openid'];
        $pullUserInfoUrl = "https://api.weixin.qq.com/sns/userinfo?access_token=$accessToken&openid=$openid&lang=zh_CN";
        $userInfo = self::sendRequest('GET', $pullUserInfoUrl);
        dd($userInfo);
        if (isset($userInfo['errcode'])) {
            throw new OperateFailedException($userInfo['errcode']);
        }
        if ($userInfo['sex'] == 1) {
            $sex = '男';
        } else if ($userInfo['sex'] == 2) {
            $sex = '女';
        } else {
            $sex = '未知';
        }
        $user = UserModel::create([
            'phone' => Session::get('phone'),
            'password' => Hash::make(Session::get('password'))
        ]);
        $token = JWTAuth::fromUser($user);
        $user->update([
            'openid' => $openid,
            'nickname' => $userInfo['nickname'],
            'sex' => $sex,
            'city' => $userInfo['city'],
            'province' => $userInfo['province'],
            'country' => $userInfo['country'],
            'avatar' => $userInfo['headimgurl']
            ]);
        return $token;
    }
}
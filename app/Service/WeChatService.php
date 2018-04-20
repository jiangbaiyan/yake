<?php
/**
 * Created by PhpStorm.
 * User: Baiyan
 * Date: 2018/4/5
 * Time: 18:12
 */

namespace App\Service;

use App\Exceptions\OperateFailedException;
use App\Helper\ApiRequest;
use App\Helper\ConstHelper;
use App\Helper\FileHelper;
use App\Model\InfoFeedbackModel;
use App\Model\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Tymon\JWTAuth\Facades\JWTAuth;

class WeChatService
{
    use ApiRequest;

    //大荆口腔公众号基本配置
    private static $appId = 'wx48c158c300c446ec';
    private static $appKey = '3272591ea6a14977714f4d059d43d3ba';
    private static $baseUrl = 'https://yake.hzcloudservice.com/api/v1/common/getWeChatUserInfo/';
    private static $frontUrl = '';//模板消息前端URL
    private static $config = [//模板消息基本配置
        'template_id' => 'VuCD_vLIZq0McLOM7IeGcjTsEfDbHwQbxM1VKRWBuY4',
        'url' => '',
        'data' => [
            'first' => [//通知标题
                'value' => '',
                'color' => '#FF0000'
            ],
            'keyword1' => [//通知时间
                'value' => ''
            ],
            'keyword2' => [
                'value' => '点我进入详情页查看',
                'color' => '#00B642'
            ],
            'remark' => [
                'value' => '                                 ☝',
            ],
        ]
    ];

    /**
     * 第一步：用户同意授权，获取code
     */
    public static function getCode()
    {
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
    public static function callback(Request $request)
    {
        $appid = self::$appId;
        $appKey = self::$appKey;
        $code = $request->code;
        $requestUrl = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=$appid&secret=$appKey&code=$code&grant_type=authorization_code";
        $res = self::sendRequest('GET', $requestUrl);
        if (isset($res['errcode'])) {
            \Log::error($res['errmsg']);
            throw new OperateFailedException($res['errmsg']);
        }
        $accessToken = $res['access_token'];
        $openid = $res['openid'];
        $user = UserModel::where('openid', $openid)->first();
        if ($user) {
            throw new OperateFailedException('WeChat was registered');
        }
        $pullUserInfoUrl = "https://api.weixin.qq.com/sns/userinfo?access_token=$accessToken&openid=$openid&lang=zh_CN";
        $userInfo = self::sendRequest('GET', $pullUserInfoUrl);
        if (isset($userInfo['errcode'])) {
            \Log::error($res['errmsg']);
            throw new OperateFailedException($userInfo['errmsg']);
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
            'password' => Hash::make(Session::get('password')),
            'openid' => $openid,
            'nickname' => $userInfo['nickname'],
            'sex' => $sex,
            'city' => $userInfo['city'],
            'province' => $userInfo['province'],
            'country' => $userInfo['country'],
            'avatar' => $userInfo['headimgurl']
        ]);
        $token = JWTAuth::fromUser($user);
        if (!$token) {
            throw new OperateFailedException('token set failed');
        }
        return $token;//注册成功，相当于成功登陆，返回token
    }

    //-----------以下为发送模板消息相关接口---------------

    /**
     * 发送模板消息
     * @param $title
     * @param $content
     * @param $limit
     * @param array $file
     * @throws OperateFailedException
     * @throws \App\Exceptions\UnAuthorizedException
     * @throws \Throwable
     */
    public static function sendModelInfo($title, $content, $limit, $file = [])
    {
        $user = UserModel::getCurUser();
        $limitStr = ConstHelper::ALL;//这个为要存入数据库的限制条件字符串
        $config = self::$config;
        //fixme：等待前端页面 $config['url'] = self::$frontUrl.$info->id;
        $config['data']['first']['value'] = $title;
        $config['data']['keyword1']['value'] = date('Y-m-d H:i');
        $res = UserModel::select('id', 'openid', 'phone');
        $limit = explode('&', $limit);
        //如果第一项年龄不是all，说明请求参数限制了年龄条件
        if ($limit[0] != 'all') {
            $ageLimit = explode(' ', $limit[0]);
            $limitStr = $ageLimit[0] . '~' . $ageLimit[1] . '岁';
            $res = $res->whereBetween('age', $ageLimit);
        }
        if ($limit[1] != 'all') {
            //如果字符串仍为默认值，说明第一个年龄条件是all，直接覆盖默认值，否则在年龄条件后面追加空格+条件
            switch ($limit[1]){
                case 'female':
                    $sex = ConstHelper::FEMALE;
                    break;
                case 'male':
                    $sex = ConstHelper::MALE;
                    break;
                default:
                    $sex = ConstHelper::UNKNOWN;
                    break;
            }
            if ($limitStr == ConstHelper::ALL) {
                $limitStr = $sex;
            } else {
                $limitStr .= ' ' . $sex;
            }
            $res = $res->where('sex', $sex);
        }
        $sendUsers = $res->get();
        if (!$sendUsers) {
            throw new OperateFailedException('no user in this query condition');
        }
        \DB::transaction(function () use ($title, $content, $limitStr, $file, $user, $sendUsers,$config) {
            $fileUrl = implode(',', FileHelper::saveFile($file));
            $infoData = ['title' => $title, 'content' => $content, 'limit' => $limitStr, 'url' => $fileUrl];
            $info = $user->infos()->create($infoData);
            if (!$info) {
                throw new OperateFailedException('info created failed');
            }
            $accessToken = self::getAccessToken();
            $requestUrl = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=$accessToken";
            foreach ($sendUsers as $sendUser) {
                $config['touser'] = $sendUser->openid;
                $res = self::sendRequest('POST', $requestUrl, ['json' => $config]);
                if ($res['errmsg'] != 'ok') {
                    \Log::error($res['errmsg']);
                    throw new OperateFailedException($res['errmsg']);
                }
                $insertData = ['user_id' => $sendUser->id, 'info_id' => $info->id, 'status' => 0];
                $infoFeedback = InfoFeedbackModel::create($insertData);
                if (!$infoFeedback) {
                    throw new OperateFailedException('infoFeedback created failed');
                }
            }
        });
    }

    /**
     * 获取access_token(微信基础支持)
     * @return mixed
     * @throws OperateFailedException
     */
    public static function getAccessToken()
    {
        if (Cache::has('accessToken')) {
            $accessToken = Cache::get('accessToken');
        } else {
            $appid = self::$appId;
            $appKey = self::$appKey;
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$appid&secret=$appKey";
            $res = self::sendRequest('GET', $url);
            if (!isset($res['access_token'])) {
                \Log::error($res['errmsg']);
                throw new OperateFailedException($res['errmsg']);
            }
            $accessToken = $res['access_token'];
            Cache::put('accessToken', $accessToken, 119);
        }
        return $accessToken;
    }
}
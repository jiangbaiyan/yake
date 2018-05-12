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
use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class WeChatService
{
    use ApiRequest;

    //大荆口腔公众号基本配置
    private static $appId = 'wx48c158c300c446ec';
    private static $appKey = '3272591ea6a14977714f4d059d43d3ba';
    private static $baseUrl = 'https://yake.hzcloudservice.com/api/v1/common/getWeChatUserInfo/';
    private static $frontUrl = 'https://yake.hzcloudservice.com/mobilepages/detail.html?id=';//模板消息前端URL
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
                'value' => '点我查看详情',
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
        if (Session::has('accessToken') && Session::has('openid')){
            $accessToken = Session::get('accessToken');
            $openid = Session::get('openid');
        }else{
            $code = $request->code;
            $requestUrl = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=$appid&secret=$appKey&code=$code&grant_type=authorization_code";
            $res = self::sendRequest('GET', $requestUrl);
            if (isset($res['errcode'])) {
                \Log::error($res['errmsg']);
                echo "<meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">";
                die(ConstHelper::WECHAT_ERROR);
            }
            $accessToken = $res['access_token'];
            $openid = $res['openid'];
            Session::put('accessToken',$accessToken);
            Session::put('openid',$openid);
        }
        $user = UserModel::where('openid', $openid)->first();
        if ($user) {
            echo "<meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">";
            die(ConstHelper::WECHAT_EXIST);
        }
        $pullUserInfoUrl = "https://api.weixin.qq.com/sns/userinfo?access_token=$accessToken&openid=$openid&lang=zh_CN";
        $userInfo = self::sendRequest('GET', $pullUserInfoUrl);
        if (isset($userInfo['errcode'])) {
            \Log::error($res['errmsg']);
            echo "<meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">";
            die(ConstHelper::WECHAT_ERROR);
        }
        if ($userInfo['sex'] == 1) {
            $sex = ConstHelper::MALE;
        } else {
            $sex = ConstHelper::FEMALE;
        }
        $user = UserModel::create([
            'phone' => Session::get('phone',''),
            'password' => Hash::make(Session::get('password','')),
            'openid' => $openid,
            'nickname' => $userInfo['nickname'],
            'sex' => $sex,
            'age' => Session::get('age',0),
            'city' => $userInfo['city'],
            'province' => $userInfo['province'],
            'country' => $userInfo['country'],
            'avatar' => $userInfo['headimgurl']
        ]);
        return isset($user) ? true :false;
    }

    //-----------以下为发送模板消息相关接口---------------

    /**
     * 通知系统发送通知(可带附件)
     * @param $title
     * @param $content
     * @param $limit
     * @param array $file
     * @throws OperateFailedException
     * @throws \App\Exceptions\UnAuthorizedException
     * @throws \Throwable
     */
    public static function sendInfo($title, $content, $limit, $file)
    {
        $user = UserModel::getCurUser();
        $limitStr = ConstHelper::ALL;//这个为要存入数据库的限制条件字符串
        $res = UserModel::select('id', 'openid', 'phone');
        $limit = explode('&', $limit);
        //如果第一项年龄不是all，说明请求参数限制了年龄条件
        if ($limit[0] != 'all') {
            $ageLimit = explode(' ', $limit[0]);
            $limitStr = $ageLimit[0] . '~' . $ageLimit[1] . '岁';
            if ($ageLimit[0] > $ageLimit[1]){
                throw new OperateFailedException();
            }
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
            throw new OperateFailedException(ConstHelper::NO_QUERY_RESULT);
        }
        $infoData = ['title' => $title,'content' => $content,'limit' => $limitStr];
        \DB::transaction(function () use ($infoData, $file, $user, $sendUsers) {
            if ($file){
                $filePath = implode(',', FileHelper::saveFile($file));
                $infoData['url'] = $filePath;
            }
            $info = $user->infos()->create($infoData);
            if (!$info) {
                throw new OperateFailedException();
            }
            $config = self::$config;
            $config['url'] = self::$frontUrl.$info->id;
            $config['data']['first']['value'] = '《' . $infoData['title'] . '》';
            $config['data']['keyword1']['value'] = date('Y-m-d H:i');
            self::sendModelInfo($sendUsers,$config);
            foreach ($sendUsers as $sendUser) {
                $insertData = ['user_id' => $sendUser->id, 'info_id' => $info->id, 'status' => 0];
                $infoFeedback = InfoFeedbackModel::create($insertData);
                if (!$infoFeedback) {
                    throw new OperateFailedException();
                }
            }
        });
    }


    /**
     * 给用户推送优惠券信息
     * @param $amount
     * @throws OperateFailedException
     */
    public static function sendCouponInfo($amount){
        $config = self::$config;
        //fixme:等待前端优惠券首页$config['url'] =
        $config['data']['first']['value'] = '我们发放了'.$amount.'张优惠券等您来拿!';
        $config['data']['keyword1']['value'] = date('Y-m-d H:i');
        self::sendModelInfo(UserModel::all(),$config);
    }

    /**
     * 通用发送模板消息方法
     * @param Collection $receivers
     * @param $config
     * @throws OperateFailedException
     */
    public static function sendModelInfo(Collection $receivers,$config){
        $accessToken = self::getAccessToken();
        $requestUrl = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=$accessToken";
        foreach ($receivers as $receiver){
            $config['touser'] = $receiver->openid;
            $res = self::sendRequest('POST', $requestUrl, ['json' => $config]);
            if ($res['errmsg'] != 'ok') {
                \Log::error($res['errmsg']);
                throw new OperateFailedException(ConstHelper::WECHAT_ERROR);
            }
        }
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
                throw new OperateFailedException(ConstHelper::WECHAT_ERROR);
            }
            $accessToken = $res['access_token'];
            Cache::put('accessToken', $accessToken, 119);
        }
        return $accessToken;
    }


}
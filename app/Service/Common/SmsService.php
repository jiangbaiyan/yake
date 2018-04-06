<?php
/**
 * Created by PhpStorm.
 * User: Baiyan
 * Date: 2018/4/5
 * Time: 18:45
 */
namespace App\Service\Common;

use Flc\Dysms\Client;
use Flc\Dysms\Request\SendSms;
use Illuminate\Support\Facades\Session;

class SmsService{

    //阿里云短信配置
    private static $config = [
        'accessKeyId'    => 'LTAIFUVgskph3h00',
        'accessKeySecret' => 'eq1CrL826196CvOm65wc7n5BPN3PZ9',
    ];

    /**
     * 获取验证码
     * @param $phone
     * @return array|string
     */
    public static function getCode($phone){
        $client = new Client(self::$config);
        $sendSms = new SendSms();
        $sendSms->setPhoneNumbers($phone);
        $sendSms->setSignName('帮帮吧');
        $sendSms->setTemplateCode('SMS_126460515');
        $code = rand(100000, 999999);
        //设置session，为验证接口使用
        Session::put($phone.'Code',$code);
        $sendSms->setTemplateParam(compact('code'));
        $res = $client->execute($sendSms);
        $res = json_decode(json_encode($res),true);
        //发送成功，返回验证码字符串
        if ($res['Code'] == 'OK'){
            return $code;
        }//发送失败返回失败信息数组
        else{
            return $res;
        }
    }

    /**
     * 判断验证码是否正确
     * @param $phone
     * @param $frontCode
     * @return bool
     */
    public static function verifyCode($phone,$frontCode){
        $backCode = Session::get($phone.'Code');
        return $frontCode == $backCode;
    }
}
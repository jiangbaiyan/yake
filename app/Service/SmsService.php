<?php
/**
 * Created by PhpStorm.
 * User: Baiyan
 * Date: 2018/4/5
 * Time: 18:45
 */
namespace App\Service;

use App\Helper\ConstHelper;
use Flc\Dysms\Client;
use Flc\Dysms\Request\SendSms;
use Illuminate\Support\Facades\Cache;
use App\Exceptions\OperateFailedException;
use App\Exceptions\ResourceNotFoundException;

class SmsService{

    //阿里云短信配置
    private static $config = [
        'accessKeyId'    => 'LTAIFUVgskph3h00',
        'accessKeySecret' => 'eq1CrL826196CvOm65wc7n5BPN3PZ9',
    ];

    /**
     * 获取验证码
     * @param $phone
     * @throws OperateFailedException
     */
    public static function getCode($phone){
        $client = new Client(self::$config);
        $sendSms = new SendSms();
        $sendSms->setPhoneNumbers($phone);
        $sendSms->setSignName('帮帮吧');
        $sendSms->setTemplateCode('SMS_126460515');
        $code = rand(1000, 9999);
        //设置Cache，为验证接口使用
        $sendSms->setTemplateParam(compact('code'));
        $res = $client->execute($sendSms);
        $res = json_decode(json_encode($res),true);
        //发送失败，抛出异常
        if ($res['Code'] != 'OK'){
            \Log::error($res['Message']);
            throw new OperateFailedException(ConstHelper::SMS_ERROR);
        }
        Cache::put($phone.'Code',$code,3);
    }

    /**
     * 判断验证码是否正确
     * @param $phone
     * @param $frontCode
     * @throws OperateFailedException
     * @throws ResourceNotFoundException
     */
    public static function verifyCode($phone,$frontCode){
        $backCode = Cache::get($phone.'Code');
        if (!$backCode){
            throw new ResourceNotFoundException(ConstHelper::CODE);
        }
        if ($frontCode != $backCode){
            throw new OperateFailedException(ConstHelper::WRONG_CODE);
        }
    }
}
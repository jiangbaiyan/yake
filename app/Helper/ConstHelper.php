<?php
/**
 * Created by PhpStorm.
 * User: Baiyan
 * Date: 2018/4/20
 * Time: 12:24
 */

namespace App\Helper;

class  ConstHelper
{

    //返回错误信息相关
    const

        BackUrl = 'https://yake.hzcloudservice.com/api/v1/',//后端URL
        FrontUrl = 'https://yake.hzcloudservice.com/',//前端URL

        //通用模块
        USER = '用户不存在',
        WRONG_PASSWORD = '密码错误',
        WRONG_CODE = '验证码错误',
        TOKEN_SET_FAILED = 'token设置失败',
        NO_QUERY_RESULT = '数据库查询结果为空',
        WECHAT_ERROR = '微信官方接口异常,请稍后重试',
        SMS_ERROR = '短信官方接口异常,请稍后重试',
        CODE = '后台短信验证码不存在',
        WECHAT_EXIST = '您不能在公众号注册多个手机号',

        FEMALE = '女',
        MALE = '男',
        ALL = '全部患者',

        //文件上传
        WRONG_FILE_FORMAT = '不合法的文件格式',
        FILE_UPLOAD_FAILED = '文件上传过程中出现错误',

        //通知模块
        INFO_FEEDBACK = '该通知的反馈不存在',
        INFO = '通知不存在',

        //优惠券模块
        COUPON = '优惠券已经没有啦',
        COUPON_EXIST = '一个类型的优惠券最多领取3张！',
        WRONG_COUPON_STATUS = '错误的优惠券状态',
        COUPON_OVERDUE = '优惠券已过期',

        //时间
        YEAR = 'year',
        MONTH = 'month',
        DAY = 'day',
        HOUR = 'hour',
        MINUTE = 'minute',
        SECOND = 'second';
}
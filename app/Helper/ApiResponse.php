<?php
/**
 * Created by PhpStorm.
 * User: Baiyan
 * Date: 2018/4/5
 * Time: 13:11
 */
namespace App\Helper;

use Illuminate\Support\Facades\Response;

trait ApiResponse{

    /**
     * @var string 成功状态码
     */
    public static $CODE_SUCCESS = '200';

    /**
     * @var string 失败状态码
     */
    public static $CODE_PARAM_ERROR = '400';//参数错误

    public static $CODE_UNAUTHORIZED = '401';//未授权（未登录）

    public static $CODE_OPERATE_FAILED = '402';//操作失败

    public static $CODE_PERMISSION_DENIED = '403';//无权限

    public static $CODE_NOT_FOUND = '404';//资源未找到

    /**
     * @var string 提示信息
     */
    public static $successStr = 'success';

    public static $operateFailedStr = 'operate failed';

    public static $resourceNotFoundStr = 'resource not found';

    public static $paramErrorStr = 'missing or wrong parameters';

    public static $unauthorizedStr = 'user unauthorized';

    public static $permissionDeniedStr = 'permission denied';

    /**
     * 通用基础返回
     * @param $status
     * @param string $msg
     * @param array $data
     * @return \Illuminate\Http\JsonResponse
     */
    public function response($status,$msg = '',$data = []){
        return Response::json(compact('status','msg','data'));
    }

    /**
     * 请求成功
     * @param array $data
     * @param string $msg
     * @return \Illuminate\Http\JsonResponse
     */
    public function responseSuccess($data = [],$msg = ''){
        return $this->response($this::$CODE_SUCCESS,$msg ? $msg :self::$successStr,$data);
    }

    /**
     * 资源未找到
     * @param string $msg
     * @return \Illuminate\Http\JsonResponse
     */
    public function responseResourceNotFound($msg = ''){
        return $this->response($this::$CODE_NOT_FOUND,$msg ? $msg : self::$resourceNotFoundStr);
    }

    /**
     * 参数缺失
     * @param string $msg
     * @return \Illuminate\Http\JsonResponse
     */
    public function responseParamValidateFailed($msg = ''){
        return $this->response($this::$CODE_PARAM_ERROR,$msg ? $msg :self::$paramErrorStr);
    }

    /**
     * 操作失败
     * @param string $msg
     * @return \Illuminate\Http\JsonResponse
     */
    public function responseOperateFailed($msg = ''){
        return $this->response($this::$CODE_OPERATE_FAILED,$msg ? $msg :self::$operateFailedStr);
    }

    /**
     * 用户未授权
     * @param string $msg
     * @return \Illuminate\Http\JsonResponse
     */
    public function responseUnauthorized($msg = ''){
        return $this->response($this::$CODE_UNAUTHORIZED,$msg ? $msg :self::$unauthorizedStr);
    }

    /**
     * 已授权，但是没有权限
     * @param string $msg
     * @return \Illuminate\Http\JsonResponse
     */
    public function responsePermissionDenied($msg = ''){
        return $this->response($this::$CODE_PERMISSION_DENIED,$msg ? $msg :self::$permissionDeniedStr);
    }

}
<?php
/**
 * Created by PhpStorm.
 * User: Baiyan
 * Date: 2018/4/5
 * Time: 13:11
 */
namespace App\Helper;

use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

trait ApiResponse{

    /**
     * @var string 成功状态码
     */
    public static $CODE_SUCCESS = '200';

    /**
     * @var string 失败状态码
     */
    public static $CODE_PARAM_MISSED = '400';//参数缺失

    public static $CODE_UNAUTHORIZED = '401';//未授权（未登录）

    public static $CODE_OPERATION_FAILED = '402';//操作失败

    public static $CODE_PERMISSION_DENIED = '403';//无权限

    public static $CODE_NOT_FOUND = '404';//资源未找到

    /**
     * 通用基础
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
    public function responseSuccess($data = [],$msg = 'success'){
        return $this->response($this::$CODE_SUCCESS,$msg,$data);
    }

    /**
     * 资源未找到
     * @param string $msg
     * @return \Illuminate\Http\JsonResponse
     */
    public function responseResourceNotFound($msg = 'resource not found'){
        return $this->response($this::$CODE_NOT_FOUND,$msg);
    }

    /**
     * 参数缺失
     * @param string $msg
     * @return \Illuminate\Http\JsonResponse
     */
    public function responseParamValidateFailed($msg = 'missing or wrong parameters'){
        return $this->response($this::$CODE_PARAM_MISSED,$msg);
    }

    /**
     * 操作失败
     * @param string $msg
     * @return \Illuminate\Http\JsonResponse
     */
    public function responseOperationFailed($msg = 'operate failed'){
        return $this->response($this::$CODE_OPERATION_FAILED,$msg);
    }

    /**
     * 用户未授权
     * @param string $msg
     * @return \Illuminate\Http\JsonResponse
     */
    public function responseUnauthorized($msg = 'user unauthorized'){
        return $this->response($this::$CODE_UNAUTHORIZED,$msg);
    }

    /**
     * 已授权，但是没有权限
     * @param string $msg
     * @return \Illuminate\Http\JsonResponse
     */
    public function responsePermissionDenied($msg = 'permission denied'){
        return $this->response($this::$CODE_PERMISSION_DENIED,$msg);
    }

}
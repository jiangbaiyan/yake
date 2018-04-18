<?php
/**
 * Created by PhpStorm.
 * User: Baiyan
 * Date: 2018/4/8
 * Time: 9:21
 */
namespace App\Exceptions;

use App\Helper\ApiResponse;

class OperateFailedException extends \Exception{

    public function __construct($message = '')
    {
        parent::__construct($message ? $message : ApiResponse::$operateFailedStr, ApiResponse::$CODE_OPERATE_FAILED);
    }
}
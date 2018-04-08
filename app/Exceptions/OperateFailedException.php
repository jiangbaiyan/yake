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
    use ApiResponse;

    public function __construct($message = "operate failed")
    {
        parent::__construct($message, self::$CODE_OPERATE_FAILED);
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: Baiyan
 * Date: 2018/4/18
 * Time: 13:11
 */
namespace App\Exceptions;

use App\Helper\ApiResponse;

class ParamValidateFailedException extends \Exception{
    public function __construct($message = '')
    {
        parent::__construct($message ? $message :ApiResponse::$paramErrorStr, ApiResponse::$CODE_PARAM_ERROR);
    }
}

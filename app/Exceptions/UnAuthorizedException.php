<?php
/**
 * Created by PhpStorm.
 * User: Baiyan
 * Date: 2018/4/18
 * Time: 13:15
 */
namespace App\Exceptions;

use App\Helper\ApiResponse;

class UnAuthorizedException extends \Exception{
    public function __construct($message = '', $code)
    {
        parent::__construct($message ? $message : ApiResponse::$unauthorizedStr, ApiResponse::$CODE_UNAUTHORIZED);
    }
}
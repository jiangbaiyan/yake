<?php
/**
 * Created by PhpStorm.
 * User: Baiyan
 * Date: 2018/4/8
 * Time: 9:07
 */
namespace App\Exceptions;

use App\Helper\ApiResponse;

class ResourceNotFoundException extends \Exception{
    public function __construct($message = '')
    {
        parent::__construct($message ? $message :ApiResponse::$resourceNotFoundStr,ApiResponse::$CODE_NOT_FOUND);
    }
}
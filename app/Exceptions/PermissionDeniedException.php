<?php
/**
 * Created by PhpStorm.
 * User: Baiyan
 * Date: 2018/4/18
 * Time: 13:15
 */
namespace App\Exceptions;

use App\Helper\ApiResponse;

class PermissionDeniedException extends \Exception{
    public function __construct($message = '')
    {
        parent::__construct($message ? $message : ApiResponse::$permissionDeniedStr, ApiResponse::$CODE_PERMISSION_DENIED);
    }
}
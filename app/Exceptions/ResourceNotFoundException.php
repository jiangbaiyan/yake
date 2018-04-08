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
    use ApiResponse;

    public function __construct($message = "resource not found")
    {
        parent::__construct($message,self::$CODE_NOT_FOUND);
    }
}
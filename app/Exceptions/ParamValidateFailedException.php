<?php
/**
 * Created by PhpStorm.
 * User: Baiyan
 * Date: 2018/4/18
 * Time: 13:11
 */
namespace App\Exceptions;

use App\Helper\ApiResponse;
use Illuminate\Validation\Validator;

class ParamValidateFailedException extends \Exception{
    public function __construct(Validator $validator = null)
    {
        $message = ApiResponse::$paramErrorStr;
        if (isset($validator)) {
            foreach ($validator->messages() as $value) {
                $message = $value[0];
            };
        }
        parent::__construct($message,ApiResponse::$CODE_PARAM_ERROR);
    }
}

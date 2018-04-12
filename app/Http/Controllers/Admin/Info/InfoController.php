<?php
/**
 * Created by PhpStorm.
 * User: Baiyan
 * Date: 2018/4/12
 * Time: 9:13
 */
namespace App\Http\Controllers\Admin\Info;

use App\Helper\Controller;
use Illuminate\Support\Facades\Session;

class InfoController extends Controller{

    public function send(){
        echo Session::get('user')->phone;
    }

}
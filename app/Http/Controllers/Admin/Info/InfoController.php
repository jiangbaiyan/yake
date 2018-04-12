<?php
/**
 * Created by PhpStorm.
 * User: Baiyan
 * Date: 2018/4/12
 * Time: 9:13
 */
namespace App\Http\Controllers\Admin\Info;

use App\Exceptions\OperateFailedException;
use App\Helper\Controller;
use App\Service\WeChatService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class InfoController extends Controller{

    /**
     * 发送通知
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function send(Request $request){
        $validator = Validator::make($request->all(),[
            'title' => 'required|max:255',
            'content' => 'required',
            'limit' => 'required',
        ]);
        if($validator->fails()){
            return $this->responseParamValidateFailed($validator->messages());
        }
        $limit = $request->limit;
        $title = $request->title;
        $content = $request->content;
        try {
            WeChatService::sendModelInfo($title, $content, $limit);
        } catch (OperateFailedException $e) {
            return $this->responseOperateFailed($e->getMessage());
        }
        return $this->responseSuccess();
    }
}
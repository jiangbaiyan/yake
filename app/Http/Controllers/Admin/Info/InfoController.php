<?php
/**
 * Created by PhpStorm.
 * User: Baiyan
 * Date: 2018/4/12
 * Time: 9:13
 */
namespace App\Http\Controllers\Admin\Info;

use App\Exceptions\OperateFailedException;
use App\Exceptions\ParamValidateFailedException;
use App\Exceptions\ResourceNotFoundException;
use App\Helper\ConstHelper;
use App\Helper\Controller;
use App\Model\InfoModel;
use App\Service\WeChatService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class InfoController extends Controller{

    /**
     * 发送通知
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws OperateFailedException
     * @throws ParamValidateFailedException
     * @throws \App\Exceptions\UnAuthorizedException
     * @throws \Throwable
     */
    public function send(Request $request){
        $validator = Validator::make($request->all(),[
            'title' => 'required|max:255',
            'content' => 'required',
            'limit' => 'required',
        ]);
        if($validator->fails()){
            throw new ParamValidateFailedException($validator);
        }
        $limit = $request->input('limit','all&all');
        $title = $request->input('title');
        $content = $request->input('content');
        WeChatService::sendInfo($title, $content, $limit,$request->has('file') ? $request->file('file') : false);
        return $this->responseSuccess();
    }

    /**
     * 获取所有通知
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllInfo(){
        return $this->responseSuccess(InfoModel::latest()->paginate(6));
    }

    /**
     * 获取通知反馈情况
     * @param $infoId
     * @return \Illuminate\Http\JsonResponse
     * @throws ParamValidateFailedException
     * @throws ResourceNotFoundException
     */
    public function getInfoFeedback($infoId){
        if (!$infoId){
            throw new ParamValidateFailedException();
        }
        $info = InfoModel::find($infoId);
        if (!$info){
            throw new ResourceNotFoundException(ConstHelper::INFO);
        }
        $data = $info->infoFeedbacks()
            ->join('users','users.id','=','info_feedbacks.user_id')
            ->join('infos','info_feedbacks.info_id','=','infos.id')
            ->select('info_feedbacks.status','users.nickname','users.phone','infos.title')
            ->orderBy('users.nickname')
            ->get();
        if (!$data){
            throw new ResourceNotFoundException(ConstHelper::NO_QUERY_RESULT);
        }
        return $this->responseSuccess($data);
    }

}
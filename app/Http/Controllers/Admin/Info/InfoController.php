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
     * @throws ParamValidateFailedException
     * @throws OperateFailedException
     */
    public function send(Request $request){
        $validator = Validator::make($request->all(),[
            'title' => 'required|max:255',
            'content' => 'required',
            'limit' => 'required',
        ]);
        if($validator->fails()){
            throw new ParamValidateFailedException($validator->messages());
        }
        $limit = $request->input('limit','all&all');
        $title = $request->input('title');
        $content = $request->input('content');
        WeChatService::sendModelInfo($title, $content, $limit);
        return $this->responseSuccess();
    }

    /**
     * 获取所有通知
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllInfo(){
        return $this->responseSuccess(InfoModel::simplePaginate(6));
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
            throw new ParamValidateFailedException('need infoId');
        }
        $info = InfoModel::find($infoId);
        if (!$info){
            throw new ResourceNotFoundException('info not found');
        }
        $data = $info->infoFeedbacks()
            ->join('users','users.id','=','info_feedbacks.user_id')
            ->join('infos','info_feedbacks.info_id','=','infos.id')
            ->select('info_feedbacks.status','users.nickname','users.phone','infos.title')
            ->get();
        if (!$data){
            throw new ResourceNotFoundException('feedback data not found');
        }
        return $this->responseSuccess($data);
    }

    /**
     * 获取通知详情
     * @param $infoId
     * @return \Illuminate\Http\JsonResponse
     * @throws ParamValidateFailedException
     * @throws ResourceNotFoundException
     */
    public function getDetail($infoId){
        if (!$infoId){
            throw new ParamValidateFailedException('need infoId');
        }
        $info = InfoModel::find($infoId);
        if (!$info){
            throw new ResourceNotFoundException('info not found');
        }
        return $this->responseSuccess($info);
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: Baiyan
 * Date: 2018/4/19
 * Time: 14:50
 */

namespace App\Http\Controllers\User\Info;

use App\Exceptions\ParamValidateFailedException;
use App\Exceptions\ResourceNotFoundException;
use App\Helper\ConstHelper;
use App\Helper\Controller;
use App\Model\InfoFeedbackModel;
use App\Model\InfoModel;
use App\Model\UserModel;

class InfoController extends Controller{

    /**
     * 普通用户获取接收到通知的列表
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\UnAuthorizedException
     */
    public function getInfoList(){
        $user = UserModel::getCurUser();
        $data = InfoFeedbackModel::join('infos','info_feedbacks.info_id','=','infos.id')
            ->where('info_feedbacks.user_id',$user->id)
            ->select('infos.*')
            ->simplePaginate(6);
        return $this->responseSuccess($data);
    }

    /**
     * 获取通知详情
     * @param $infoId
     * @return \Illuminate\Http\JsonResponse
     * @throws ParamValidateFailedException
     * @throws ResourceNotFoundException
     * @throws \App\Exceptions\UnAuthorizedException
     */
    public function getDetail($infoId){
        if (!$infoId){
            throw new ParamValidateFailedException();
        }
        $info = InfoModel::find($infoId);
        if (!$info){
            throw new ResourceNotFoundException(ConstHelper::INFO);
        }
        $infoFeedback = InfoFeedbackModel::where(['user_id' => UserModel::getCurUser()->id,'info_id' => $info->id])->first();
        $infoFeedback->status = 1;
        $infoFeedback->save();
        return $this->responseSuccess($info);
    }
}
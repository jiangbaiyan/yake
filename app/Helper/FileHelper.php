<?php
/**
 * Created by PhpStorm.
 * User: Baiyan
 * Date: 2018/4/20
 * Time: 10:09
 */
namespace App\Helper;

use App\Exceptions\OperateFailedException;
use Illuminate\Support\Facades\Storage;

class FileHelper{

    private static $allowedFileFormat =  ['doc', 'docx', 'pdf', 'DOC', 'DOCX', 'PDF', 'rar', 'zip', 'RAR', 'ZIP', 'xls', 'xlsx', 'XLS', 'XLSX'];//规定允许上传的文件格式

    private static $upyunUrl = 'https://cloudfiles.cloudshm.com/';//又拍云存储地址

    /**
     * 存储文件并返回文件路径(支持批量存储)
     * @param $file
     * @return array|false|string
     * @throws OperateFailedException
     */
    public static function saveFile($file){
        $path = [];
        self::isAllowedFormat($file);
        if (is_array($file)){
            foreach ($file as $fileItem){
                $path[] = self::$upyunUrl.Storage::disk('upyun')->putFileAs('yake/'.date('Y').'/'.date('md'),$fileItem,$fileItem->getClientOriginalName(),'public');
            }
        }else{
            $path[] = self::$upyunUrl.Storage::disk('upyun')->putFileAs('yake/'.date('Y').'/'.date('md'),$file,$file->getClientOriginalName(),'public');
        }
        if(!$path){
            throw new OperateFailedException('file upload failed');
        }
        return $path;
    }

    /**
     * 判断文件格式是否符合要求
     * @param $file
     * @throws OperateFailedException
     */
    public static function isAllowedFormat($file){
        if (is_array($file)){
            foreach ($file as $fileItem){
                $ext = $fileItem->getClientOriginalExtension();
                if (!in_array($ext,self::$allowedFileFormat)){
                    throw new OperateFailedException('wrong file format');
                }
            }
        }else{
            $ext = $file->getClientOriginalExtension();
            if (!in_array($ext,self::$allowedFileFormat)){
                throw new OperateFailedException('wrong file format');
            }
        }
    }
}
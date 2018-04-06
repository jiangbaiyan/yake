<?php
/**
 * Created by PhpStorm.
 * User: Baiyan
 * Date: 2018/4/5
 * Time: 18:26
 */
namespace App\Helper;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Response;

trait ApiRequest{

    use ApiResponse;
    /**
     * 模拟发送请求（暂时只是简单的请求）
     * @param $requestType
     * @param $url
     * @param array $params
     * @return mixed
     */
    public function sendRequest($requestType,$url,$params = []){
        $client = new Client();
        try{
            $result = $client->request($requestType,$url,[
                'json' => $params
            ]);
        }catch (GuzzleException $e){
            return $this->responseOperationFailed('request failed');
        }
        return json_decode($result);
    }
}

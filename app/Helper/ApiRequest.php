<?php
/**
 * Created by PhpStorm.
 * User: Baiyan
 * Date: 2018/4/5
 * Time: 18:26
 */

namespace App\Helper;

use App\Exceptions\OperateFailedException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

trait ApiRequest
{
    use ApiResponse;

    /**
     * 模拟发送请求
     * @param $requestType
     * @param $url
     * @param array $params
     * @return bool|mixed
     */
    public static function sendRequest($requestType, $url, $params = null)
    {
        try{
            $client = new Client();
            if (isset($params)) {
                $result = $client->request($requestType, $url, $params);
            } else {
                $result = $client->request($requestType, $url);
            }
        } catch (GuzzleException $e) {
            throw new OperateFailedException('send request failed');
        }
        return json_decode($result->getBody(),true);
    }
}

<?php
/**
 * Created by PhpStorm.
 * User: Baiyan
 * Date: 2018/4/5
 * Time: 18:26
 */

namespace App\Helper;

use GuzzleHttp\Client;

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
        $client = new Client();
        if (isset($params)) {
            $result = $client->request($requestType, $url, $params);
        } else {
            $result = $client->request($requestType, $url);
        }
        return json_decode($result->getBody(),true);
    }
}

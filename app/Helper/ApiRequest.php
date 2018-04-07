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

trait ApiRequest
{

    use ApiResponse;

    /**
     * 模拟发送请求（暂时只是简单的请求）
     * @param $requestType
     * @param $url
     * @param a rray $params
     * @return bool|mixed
     */
    public static function sendRequest($requestType, $url, $params = [])
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

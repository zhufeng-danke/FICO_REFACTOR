<?php namespace Forecast; // zhangwei@wutongwan.org

/**
 * 调用forecast的接口trait
 */
trait APITrait
{
    protected static function post($relPath, array $data = [])
    {
        $url = config('app.url') . '/forecast/' . $relPath;
        $client = new \HttpClient();
        $client->setHeader(['Content-Type:application/json']);
        $response = $client->post($url, json_encode($data, JSON_UNESCAPED_UNICODE), 10);
        $result = json_decode($response, JSON_OBJECT_AS_ARRAY);
        if (!$result) {
            \Email::debugEmail('zhangwei@dankegongyu.com', 'Forecast调用失败', [$response]);
        }
        return $result;
    }
}
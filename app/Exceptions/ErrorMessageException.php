<?php
//yubing@wutongwan.org

/**
 * 用户显示错误提示信息提示
 *
 * 都是代码中指定的错误信息，所以不会记录到错误日志里。
 */
class ErrorMessageException extends LaputaException
{

    public function response()
    {
        $data = [
            'code' => $this->getCode(),
            'message' => $this->getMessage(),
            'locate' => $this->snapshot()
        ];

        $response = response();
        if (\Request::ajax()) {
            return $response->json($data, 400);
        } else {
            return $response->view('errors.message', $data, 400);
        }
    }

    private function snapshot()
    {
        $data = [
            'time' => date('Y-m-d H:i:s'),
            'message' => $this->getMessage(),
            'url' => URL::full(),
            'last-trace' => $this->getTrace()[0],
            'requests' => Request::input(),
            'session-id' => Session::getId(),
            'session' => Session::all(),
            'corp-user' => CorpAuth::user(),
            'traces' => logBackTrace(),
            'user' => Auth::user(),
        ];
        $text = json_stringify($data);

        // 生成定位码，只取八位，方便检索
        $hash = md5(time() . $this->getMessage());
        $code = substr($hash, rand(0, strlen($hash) - 9), 8);

        // save to redis
        RedisClient::create()->set(__CLASS__ . $code, $text, 3600 * 5);

        return $code;
    }
}

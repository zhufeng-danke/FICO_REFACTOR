<?php

/**
 * Laputa Http Client
 *
 * Usage:
 *  $client = app(\HttpClient::class);
 *  $client->get(...);
 *  $client->post(...);
 */
class HttpClient
{
    private $ch;
    private $result;
    private $cookies; // ['uid' => '123', ]

    const UA_PC = 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 5.1; Trident/4.0)';
    const UA_MOBILE = 'Mozilla/5.0 (iPhone; CPU iPhone OS 8_3 like Mac OS X) AppleWebKit/600.1.4 (KHTML, like Gecko) Mobile/12F70 MicroMessenger/6.1.4 NetType/WIFI';

    public function __construct()
    {
        $this->ch = curl_init();
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, false);
        curl_setopt($this->ch, CURLOPT_HEADER, true);
        $this->initHeader();
    }

    public function get($url, $timeout = 3)
    {
        curl_setopt($this->ch, CURLOPT_HTTPGET, true);
        curl_setopt($this->ch, CURLOPT_URL, $url);

        $this->setTimeout($timeout);

        $start = microtime(true);
        $this->result = curl_exec($this->ch);

        $response = curl_errno($this->ch) ? 'no response' : $this->body();

        $end = microtime(true);
        \Log::debug(__METHOD__ . ' ' . $url . ' ' . round($end - $start, 3) . 'ms ' . json_stringify($response));

        return $response;
    }

    /**
     * @param $url
     * @param string|array $post_data
     * @param int $timeout
     * @return bool|mixed
     *
     *  注意 Content-Type 是根据提交的数据类型自动选择的：
     *  如果$post_data是字符串，则Content-Type是application/x-www-form-urlencoded。
     *  如果$post_data是k=>v的数组，则Content-Type是multipart/form-data
     */
    public function post($url, $post_data, $timeout = 3)
    {
        curl_setopt($this->ch, CURLOPT_COOKIEJAR, null);
        curl_setopt($this->ch, CURLOPT_COOKIEFILE, null);

        curl_setopt($this->ch, CURLOPT_POST, true);
        curl_setopt($this->ch, CURLOPT_URL, $url);
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, $post_data);

        $this->setTimeout($timeout);

        $start = microtime(true);
        $this->result = curl_exec($this->ch);

        $response = curl_errno($this->ch) ? 'no response' : $this->body();

        $end = microtime(true);
        $post_data = is_array($post_data) ? json_stringify($post_data) : $post_data;
        $response = is_array($response) ? json_stringify($response) : $response;
        \Log::debug(__METHOD__ . ' ' . $url . ' ' . round($end - $start, 3) . 's ' . $post_data . '|||' . $response);

        return $response;
    }

    public function put($url, $data, $timeout = 3)
    {
        curl_setopt_array($this->ch, [
            CURLOPT_URL => $url,
            CURLOPT_CUSTOMREQUEST => "PUT",
            CURLOPT_POSTFIELDS => $data,
        ]);

        $this->setTimeout($timeout);

        $this->result = curl_exec($this->ch);

        return curl_errno($this->ch) ? false : $this->body();
    }

    public function curlInfo()
    {
        return curl_getinfo($this->ch);
    }

    private function setTimeout($timeout)
    {
        $timeout = intval($timeout);
        curl_setopt($this->ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($this->ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    }

    private function initHeader()
    {
        $default_header = array(
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            'Accept-Language: zh-cn,zh;q=0.8,en-us;q=0.5,en;q=0.3',
        );
        curl_setopt($this->ch, CURLOPT_USERAGENT, self::UA_PC);
        curl_setopt($this->ch, CURLOPT_ENCODING, ""); //all supported encoding types will be sent
        $this->setHeader($default_header);
    }

    /**
     * @param array|string $header_lines 多次调用时后面的参数会覆盖前面的参数
     *
     * Sample:
     * [
     *  'Authorization: Basic xxxxx',
     * ]
     */
    public function setHeader($header_lines)
    {
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, $header_lines);
        return $this;
    }

    public function setRefer($refer)
    {
        curl_setopt($this->ch, CURLOPT_REFERER, $refer);

        return $this;
    }

    //  @todo 待测试
    public function setBinaryTransfer()
    {
        curl_setopt($this->ch, CURLOPT_BINARYTRANSFER, true);

        return $this;
    }

    //格式： "fruit=apple; colour=red"， CURL默认会记住服务器返回的Cookie。
    public function setCookie($cookie_string = 'N=A')
    {
        curl_setopt($this->ch, CURLOPT_COOKIE, $cookie_string);

        return $this;
    }

    public function setUserAgent($ua_string)
    {
        curl_setopt($this->ch, CURLOPT_USERAGENT, $ua_string);

        return $this;
    }

    public function followLocation($max_redirection)
    {
        $max_redirection = intval($max_redirection);
        curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, ($max_redirection > 0));
        curl_setopt($this->ch, CURLOPT_MAXREDIRS, $max_redirection);

        return $this;
    }

    public function body()
    {
        return substr($this->result, curl_getinfo($this->ch, CURLINFO_HEADER_SIZE));
    }

    public function headers()
    {
        $raw = substr($this->result, 0, curl_getinfo($this->ch, CURLINFO_HEADER_SIZE));
        preg_match_all('/(.*?): (.*?)\r\n/', $raw, $match);

        $headers = [];
        if (count($match) == 3) {
            foreach ($match[1] as $index => $key) {
                $key = trim($key);
                $value = trim($match[2][$index]);
                if (isset($headers[$key])) {
                    $headers[$key] .= ',' . $value;
                } else {
                    $headers[$key] = $value;
                }

                // parse cookies  !Hard Code
                // uid=123; path=.... => ['uid=123; ...', 'uid', '123'] => ['uid' => '123', ]
                if (strtolower($key) === 'set-cookie' && preg_match('/^(.*?)=(.*?);/', $value, $m)) {
                    $this->cookies[$m[1]] = $m[2];
                }
            }
        }
        return $headers;
    }

    public function cookie()
    {
        $cookie = '';
        $this->cookies = [];
        $this->headers();
        if ($this->cookies) {
            foreach ($this->cookies as $k => $v) {
                $cookie .= "{$k}={$v};";
            }
        }
        return $cookie;
    }

    /**
     * CURL Error Code
     * doc: https://curl.haxx.se/libcurl/c/libcurl-errors.html
     * @return int
     */
    public function errorCode()
    {
        return curl_errno($this->ch);
    }

    /**
     * CURL Error Message
     * doc: https://curl.haxx.se/libcurl/c/libcurl-errors.html
     * @return string
     */
    public function error()
    {
        return curl_error($this->ch);
    }
}

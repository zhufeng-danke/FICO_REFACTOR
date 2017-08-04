<?php

//yubing@wutongwan.org

class HttpProxy
{
    /**
     * @return array of Valid HttpProxy Servers
     *
     * eg:
     *  [
     *    '1.2.3.4:808'
     *  ]
     */
    public static function getProxy()
    {
        $proxy_list = [];
        $fetcher = new HttpClient(); //免得自己设置UA之类的东西

        //付费代理可以考虑： http://www.daili666.com/
        //'http://pachong.org/high.html' => '##',
        //'http://www.xici.net.co/nn/' => '#<td>(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})</td>#',

        /* Source 1 */
        $source1 = function () use ($fetcher) {
            $url = 'http://proxy.ipcn.org/proxylist.html';
            $patten = '#(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})\:(\d{1,5})#';
            $html = $fetcher->get($url);
            preg_match_all($patten, $html, $matches);
            $list = [];
            foreach ($matches[1] as $_i => $_ip) {
                $list[$_ip] = $_ip . ':' . $matches[2][$_i];
            }

            return $list;
        };

        $proxy_list += $source1();

        return self::validateProxy(array_values($proxy_list));
    }

    //用Curl_Multi加速代理验证。
    private static function validateProxy($list, $timeout = 2)
    {
        shuffle($list);
        $mh = curl_multi_init();
        $curl_array = array();
        $opts = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_ENCODING => '',
            CURLOPT_CONNECTTIMEOUT => $timeout,
            CURLOPT_TIMEOUT => $timeout,
        ];

        foreach ($list as $i => $proxy) {
            $opts[CURLOPT_PROXY] = $proxy;
            $curl_array[$i] = curl_init('http://www.baidu.com/robots.txt');
            curl_setopt_array($curl_array[$i], $opts);
            curl_multi_add_handle($mh, $curl_array[$i]);
        }
        $running = null;
        do {
            usleep(10000);
            curl_multi_exec($mh, $running);
        } while ($running > 0);

        $res = array();
        foreach ($list as $i => $proxy) {
            if (preg_match("#User-agent:#", curl_multi_getcontent($curl_array[$i]))) {
                $res[] = $proxy;
            }
            curl_multi_remove_handle($mh, $curl_array[$i]);
        }
        curl_multi_close($mh);

        return $res;
    }
}

//var_dump(HttpProxy::getProxy());

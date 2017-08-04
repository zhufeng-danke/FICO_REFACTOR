<?php
//yubing@wutongwan.org

/**
 * This class is used for detect user device by UserAgent
 */
class UserAgent
{
    const MOBILE = 'mobile';
    const PC = 'pc';

    public static function ua()
    {
        return isset($_SERVER['HTTP_USER_AGENT']) ? trim($_SERVER['HTTP_USER_AGENT']) : '';
    }

    /*
     * @return 'mobile'|'pc'
     */
    public static function env()
    {
        $ua = self::ua();
        //TODO: UA正则有待进一步清理
        $re = '/' .
            // Common Devices
            'MicroMessenger|DingTalk|' .
            'Android|iPhone|iPad|Windows Phone|' .
            // common mobile browsers
            'UCWEB|MQQBrowser|Opera\s*Mobi|Opera\s*Mini|NetFront|Fennec|' .
            // mobile protocols or platforms
            'WAP|Symbian|MIDP|WebOS|Windows CE|IEMobile|' .
            // telecoms or device manufacturers
            'Nokia|Samsung|Blackberry|Motorola|HTC|HUAWEI|Sony|Ericsson|Philips|' .
            'ppc|dopod|blazer|helio|hosin|novarra|CoolPad|techfaith|palmsource|' .
            'alcatel|amoi|ktouch|nexian|sagem|wellcom|bunjalloo|maui|' .
            'smartphone|phone|^spice|^bird|^ZTE\-|longcos|pantech|gionee|^sie\-|portalmmm|' .
            'hiptop|benq|haier|Softbank|docomo|kddi|up\.browser|up\.link' .
            '/i';
        if (
            isset($_SERVER['HTTP_X_WAP_PROFILE']) ||
            isset($_SERVER['HTTP_PROFILE']) ||
            isset($_SERVER['HTTP_VIA']) && preg_match('/wap/i', $_SERVER['HTTP_VIA']) ||
            preg_match($re, $ua)
        ) {
            return self::MOBILE;
        } else {
            return self::PC;
        }
    }

    public static function isPC()
    {
        return self::env() === self::PC;
    }

    public static function isMobile()
    {
        return self::env() === self::MOBILE;
    }

    public static function isWeChat()
    {
        return preg_match('/\bMicroMessenger\b/', self::ua());
    }

    /**
     * 判断是否为阿里钉钉客户端打开
     * @return int
     */
    public static function isDingTalk()
    {
        return preg_match('/\bDingTalk\b/', self::ua());
    }

    /**
     * 是否是各种机器人
     * @param $ua
     * @return bool
     */
    public static function isBot($ua)
    {
        return static::isSpider($ua) || static::isTranscoder($ua) || static::isTool($ua);
    }

    /**
     * 是否是爬虫
     * @param $ua
     * @return int
     */
    public static function isSpider($ua)
    {
        return preg_match("/spider|Baiduspider|Googlebot|Mediapartners-Google|AdsBot-Google/i", $ua);
    }

    /**
     * 是否是转码器
     * @param $ua
     * @return int
     */
    public static function isTranscoder($ua)
    {
        //todo: 识别各种“云加速”
        return preg_match('/transcoder/i', $ua);
    }

    /**
     * 是否是工具和库调用
     * list see http://www.useragentstring.com/pages/useragentstring.php
     * @param $ua
     * @return bool
     */
    public static function isTool($ua)
    {
        $ua = trim($ua);

        return (
            $ua === 'NativeHost' // .net
            || preg_match('/^(Java|curl|php|python|pycurl)/i', $ua)

        );
    }

}
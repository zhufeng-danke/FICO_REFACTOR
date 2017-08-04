<?php

class Mobile
{
    const DEFAULT_CCC = 86;

    /***
     * List Calling Country Code, 国际区号
     * @return array
     */
    public static function listCCC()
    {
        return [
            86 => '中国大陆',
            852 => '中国香港',
            853 => '中国澳门',
            886 => '中国台湾',
        ];
    }

    public static function listCCCOption()
    {
        $options = [];
        foreach (self::listCCC() as $code => $area) {
            $options [$code]= "{$area}（+{$code}）";
        }
        return $options;
    }
}
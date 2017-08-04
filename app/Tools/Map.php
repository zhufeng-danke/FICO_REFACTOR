<?php

/**
 * 地理计算工具
 */
class Map
{
    static private $earthRadius = 6370996.81;   //  单位：米

    /**
     * 计算两点直线距离，**按BD-09标准**
     *
     * 代码来自SDK中混淆后的代码的翻译，函数含义不明。
     *
     * @param $lng1
     * @param $lat1
     * @param $lng2
     * @param $lat2
     * @return float
     *
     */
    public static function getDistance($lng1, $lat1, $lng2, $lat2)
    {
        return round(self::calc($lng1, $lat1, $lng2, $lat2));
    }

    private static function calc($lng1, $lat1, $lng2, $lat2)
    {
        $lng1 = self::toRad(self::formatting($lng1, -180, 180));
        $lat1 = self::toRad(self::middle($lat1, -74, 74));
        $lng2 = self::toRad(self::formatting($lng2, -180, 180));
        $lat2 = self::toRad(self::middle($lat2, -74, 74));

        return self::$earthRadius * acos(sin($lat1) * sin($lat2) + cos($lat1) * cos($lat2) * cos($lng2 - $lng1));
    }

    private static function toRad($a)
    {
        return pi() * $a / 180;
    }

    private static function middle($a, $b, $c)
    {
        $a = max($a, $b);
        $a = min($a, $c);
        return $a;
    }

    private static function formatting($a, $b, $c)
    {
        while ($a > $c) {
            $a -= $c - $b;
        }
        while ($a < $b) {
            $a += $c - $b;
        }
        return $a;
    }

}
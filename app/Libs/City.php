<?php

/**
 * Helper类,方便代码中处理url等传参中的城市信息
 */
class City
{
    const DEFAULT_CITY_CODE = 'bj';
    const DEFAULT_CITY = Area::CITY_北京市;

    /**
     * 官网列表。后台列表请用 @see Area::listCit()
     * @return array
     */
    public static function list()
    {
        return [
            'bj' => Area::CITY_北京市,
            'sz' => Area::CITY_深圳市,
            'sh' => Area::CITY_上海市,
            'hz' => Area::CITY_杭州市,
        ];
    }

    /**
     * @param string $city 支持两种写法: bj|北京市
     * @return \Area\City|null
     */
    public static function find($city)
    {
        if ($city instanceof \Area\City) {
            return $city;
        }

        $mapping = self::list();
        if (isset($mapping[$city])) {
            $name = $mapping[$city];
        } elseif (in_array($city, $mapping)) {
            $name = $city;
        } else {
            return null;
        }

        return \Area\City::whereName($name)->first();
    }

    /**
     * 根据 IP 返回城市
     * @param null|string $ip 默认当前请求者的 IP
     * @return \Area\City
     */
    public static function findByIP(string $ip = null)
    {
        $name = \Ip::find($ip ?: \Request::getClientIp())[2] . '市';
        $name = in_array($name, City::list())
            ? $name
            : self::DEFAULT_CITY;

        return City::find($name);
    }

    /** 系统后台相关 */

    const INTERNAL_SESSION_KEY = 'internal_current_city';
    const DEFAULT_CITY_HASH_KEY = 'default_city_cache_hash';

    /**
     * 设置当前城市
     */
    public static function changeTo($cityName)
    {
        assert(in_array($cityName, Area::listCity()));

        Session::set(self::INTERNAL_SESSION_KEY, $cityName);
        RedisClient::create()->hSet(self::DEFAULT_CITY_HASH_KEY, CorpAuth::id(), $cityName);
    }

    /**
     * 当前城市
     *
     * 策略:
     *  1. 优先取session
     *  2. 尝试取用户跨平台的默认城市, 每次修改城市时更新, 存在redis中
     *  3. 以上为空时, 默认北京
     */
    public static function current()
    {
        $cityName = Session::get(self::INTERNAL_SESSION_KEY);
        if (!$cityName || in_array($cityName, Area::listCity())) {
            $cityName = RedisClient::create()->hGet(self::DEFAULT_CITY_HASH_KEY, CorpAuth::id());
            $cityName = in_array($cityName, Area::listCity()) ? $cityName : Area::CITY_北京市;
            self::changeTo($cityName);
        }

        return $cityName;
    }
}

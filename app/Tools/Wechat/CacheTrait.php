<?php namespace Wechat;

/**
 * 实现微信 SDK 中的缓存接口函数
 *
 * Class CacheTrait
 * @package Wechat
 */
trait CacheTrait
{
    protected function getCache($cachename)
    {
        return \Cache::get(static::class . $cachename);
    }

    protected function setCache($cachename, $value, $expired)
    {
        \Cache::put(static::class . $cachename, $value, $expired / 60);
    }

    protected function removeCache($cachename)
    {
        return \Cache::forget(static::class . $cachename);
    }
}
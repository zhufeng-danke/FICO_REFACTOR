<?php

class CDN
{
    const MANIFEST_CACHE_KEY = __CLASS__ . ':manifest';
    const MANIFEST_CACHE_EXPIRE = 7 * 24 * 60;

    private static $manifest = null;
    private static $domains = null;

    /**
     * 静态文件本地路径到 CDN 路径的映射表
     * @return array
     */
    public static function manifest()
    {
        if (is_null(self::$manifest)) {
            self::$manifest = Cache::remember(
                self::MANIFEST_CACHE_KEY,
                self::MANIFEST_CACHE_EXPIRE,
                function () {
                    return self::getManifestFromFile();
                }
            );
        }

        return self::$manifest;
    }

    /**
     * 更新 manifest 缓存
     */
    public static function refreshManifestCache()
    {
        Cache::put(self::MANIFEST_CACHE_KEY, self::getManifestFromFile(), 7 * 24 * 60);
    }

    /**
     * 直接从文件读取 manifest
     * @return array
     */
    public static function getManifestFromFile()
    {
        return json_decode(file_get_contents(public_path('build/rev-manifest.json')), true) ?: [];
    }

    /**
     * 获取静态文件的 CDN 路径
     * @param string $file 静态文件本地路径
     * @return string
     */
    public static function file($file)
    {
        $file = ltrim($file, DIRECTORY_SEPARATOR);
        $domain = 'http://' . self::domains()[crc32($file) % count(self::domains())];
        if (isset(self::manifest()[$file])) {
            $url = $domain . '/build/' . self::manifest()[$file];
        } else {
            $url = $domain . '/' . $file;
        }
        return $url;
    }

    public static function domains()
    {
        if (is_null(self::$domains)) {
            self::$domains = config('app.cdn_domains');
        }

        return self::$domains;
    }
}

<?php namespace Area;

abstract class AbstractArea extends \BaseModel
{
    const LEVEL_NAME = 'should be rewrite';

    protected $table = 'areas';

    protected static function boot()
    {
        // Query 中自动添加 Level 语句
        static::addGlobalScope(function ($query) {
            /** @var self $query */
            $query->where('level', static::LEVEL_NAME);
        });

        // 创建时填充 Level
        static::creating(function (self $area) {
            if (!$area->level) {
                $area->level = static::LEVEL_NAME;
            }
        });

        parent::boot();
    }

    public function getDescription()
    {
        return '地域数据-' . static::LEVEL_NAME;
    }
}
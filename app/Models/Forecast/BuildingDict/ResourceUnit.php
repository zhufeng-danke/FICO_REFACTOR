<?php namespace Forecast\BuildingDict;

/**
 * 楼盘字典-单元
 *
 * @property integer $id
 * @property string $name            单元名称
 * @property string $address         单元完整地址(小区地址 + 楼盘名称, 不包含单元名称)
 * @property integer $building_id    楼盘 ID
 * @property integer $community_id   小区 ID
 * @property integer $owner_id       增加者 ID
 * @property tinyint $is_active      是否被禁, 0: 未被禁, 1: 被禁
 * @property tinyint $is_readonly    是否只读, 0: 非只读, 1: 只读
 */
class ResourceUnit extends BaseDict
{
    protected $description = '楼盘字典-单元';
    protected $connection = 'forecast';
    protected $table = 'resource_unit';

    public $timestamps = false;

    public function save(array $options = []){
        if ($this->building->isLocked()) {
            throw new \ErrorMessageException("楼盘被禁用或被锁，禁止添加单元！");
        }

        return parent::save($options);
    }


    public function community()
    {
        return $this->belongsTo(Community::class);
    }

    public function building()
    {
        return $this->belongsTo(ResourceBuilding::class);
    }

    public function houses()
    {
        return $this->hasMany(ResourceHouse::class, 'unit_id');
    }

    public function isLocked()
    {
        return $this->is_active == self::NO || $this->is_readonly == self::NO;
    }

}

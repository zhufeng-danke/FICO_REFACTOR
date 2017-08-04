<?php namespace Forecast\BuildingDict;

use Forecast\HouseStatePlanTicket;

/**
 * 楼盘字典-公寓
 *
 * @property integer $id
 * @property string $name               住宅名称
 * @property string $address            住宅完整地址(小区地址 + 楼盘名称 + 单元名称, 不包含住宅名称)
 * @property integer $unit_id           单元 ID
 * @property integer $building_id       楼盘 ID
 * @property integer $community_id      小区 ID
 * @property integer $owner_id          增加者 ID
 * @property tinyint $is_active         是否被禁, 0: 未被禁, 1: 被禁
 * @property tinyint $is_readonly       是否只读, 0: 非只读, 1: 只读
 */
class ResourceHouse extends BaseDict
{
    protected $description = '楼盘字典-公寓';
    protected $connection = 'forecast';
    protected $table = 'resource_house';

    public $timestamps = false;

    public function community()
    {
        return $this->belongsTo(Community::class);
    }

    public function building()
    {
        return $this->belongsTo(ResourceBuilding::class);
    }

    public function unit()
    {
        return $this->belongsTo(ResourceUnit::class);
    }

    /**
     * 门牌号
     * @return string
     */
    public function doorplate()
    {
        return implode('-', [
            $this->building->name,
            $this->unit->name,
            $this->name
        ]);
    }

    public function fullAddress()
    {
        return implode('', [
            $this->community->name . '小区',
            $this->building->name . '楼',
            $this->unit->name . '单元',
            $this->name . '室',
        ]);
    }

    public function house_state_plans()
    {
        return $this->hasMany(HouseStatePlanTicket::class, 'house_id', 'id');
    }

    /**
     * 门牌号自动补全
     * @param integer $unitId 单元ID
     * @param string $keywords 关键字
     * @return \Illuminate\Support\Collection
     */
    public static function getByKeyword($unitId, $keywords)
    {
        return self::whereUnitId($unitId)
            ->where('name', 'like', "%{$keywords}%")
            ->orderBy("name")
            ->get()
            ->map(function (self $house) {
                return [
                    'id' => $house->id,
                    'name' => $house->name
                ];
            });
    }

}

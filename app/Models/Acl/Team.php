<?php
//  zhanghuiren@wutongwan.com

namespace Acl;


/**
 * Acl\Team
 *
 * @property integer $id
 * @property string $name
 * @property string $type
 * @property integer $city_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Team extends \BaseModel
{
    protected $table = 'acl_teams';

    public static function rule()
    {
        return [
            'name' => 'required',
            'type' => 'required|in' . join(self::constants('type')),
            'city_id' => 'required|in' . \Area::whereIsCity()->pluck('id')->toArray(),
        ];
    }

    const TYPE_出房销售 = '出房销售';
    const TYPE_收房销售 = '收房销售';
    const TYPE_退转换续 = '退转换续';

    //  同城市 同类型只能有一个
    public function isDuplicate()
    {
        return self::whereCityId($this->city_id)
            ->whereType($this->type)
            ->where('id', '!=', $this->id)
            ->exists();
    }

    public function internalTitle()
    {
        return $this->city->name . $this->type;
    }

    public function city()
    {
        return $this->belongsTo(\Area::class)->whereLevel(\Area::LEVEL_城市);
    }

}
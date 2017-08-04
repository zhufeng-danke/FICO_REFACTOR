<?php
//  qiyanjun@dankegongyu.com

namespace Tracking\Dispatch;

use Area\City;

/**
 * Tracking\Coterie
 *
 * @property int $id
 * @property int $city_id           // 城市id
 * @property int $creator_id        // 创建人id
 * @property string $status         // 状态
 * @property string $source         // 来源
 * @property string $name           // 圈子名称
 * @property string $note           // 圈子描述
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon $created_at
 */
class Coterie extends \BaseModel
{
    protected $description = '圈子';

    const STATUS_启用 = '启用';
    const STATUS_停用 = '停用';

    const SOURCE_电销 = '电销';

    public static function rules()
    {
        return [
            'city_id' => 'required|integer',
            'name' => 'required|max:30',
            'status' => 'in:' . join(',', self::constants('status')),
            'source' => 'in:' . join(',', self::constants('source')),
        ];
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function dispatch_blocks()
    {
        return $this->hasMany(CoterieDispatchBlock::class);
    }
}

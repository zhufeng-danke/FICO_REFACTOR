<?php namespace Forecast;

/**
 * Forecast\HouseStateNineTag
 *
 * @property integer $id
 * @property string $describe       描述
 * @property string $slug           编码
 * @property string $room_type      房间类型
 * @property string $state_category 房态类别
 * @property boolean $is_active     是否激活
 * @mixin \Eloquent
 * @property string $describes      描述
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class HouseStateNineTag extends \BaseModel
{
    protected $connection = 'forecast';
    protected $table = 'house_state_nine_tags';

    const CATEGORY_墙 = '墙';
    const CATEGORY_地 = '地';
    const CATEGORY_顶 = '顶';
    const CATEGORY_墙地 = '墙地';
    const CATEGORY_洁具 = '洗脸池/柱盆';
    const CATEGORY_座便器 = '座便器';
    const CATEGORY_橱柜 = '橱柜';
    const CATEGORY_门 = '门';
    const CATEGORY_窗 = '窗';
    const CATEGORY_拆除项 = '拆除项';

    const STATUS_ACTIVITY = '1';
}
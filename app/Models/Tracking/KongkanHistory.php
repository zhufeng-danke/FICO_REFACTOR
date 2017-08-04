<?php namespace Tracking;

use Carbon\Carbon;

/**
 * Tracking\DaikanHistory
 *
 * @property integer $id
 * @property integer $dealer_id
 * @property string $status
 * @property integer $suite_id
 * @property string $lng_lat_gcj02  不同格式的坐标
 * @property string $lng_lat_bd09
 * @property integer $offset        销售坐标和房屋的距离（单位:米）
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon $created_at
 */
class KongkanHistory extends BaseKanfangHistory
{
    protected $description = '空看记录列表';

    const STATUS_已空看 = '已空看';

    public function dealer()
    {
        return $this->belongsTo(\CorpUser::class);
    }

    public function suite()
    {
        return $this->belongsTo(\Suite::class);
    }

    /*
    * 获取关联关系的字段值,主要针对两层以上的关联关系
    */
    public function scopeRelationField($query, $value, $relation, $field)
    {
        if ($value) {
            return $query->whereHas($relation, function ($query) use ($field, $value){
                return $query->where($field, 'like', "%${value}%");
            });
        }

        return $query;
    }

    public function kongKanSuite($suiteId)
    {
        $this->status = self::STATUS_已空看;
        $this->suite_id = $suiteId;
        $this->dealer_id = \CorpAuth::id();
        $this->daikan_at = Carbon::now();
        $this->saveOrError();
    }
}

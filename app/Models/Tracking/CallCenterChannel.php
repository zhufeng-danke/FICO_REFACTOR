<?php namespace Tracking;

/**
 * CallCenterChannel 呼入电话来源
 * @property integer $id
 * @property string $channel_number         渠道号码
 * @property string $source                 客户来源
 * @property integer $city_id               城市
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class CallCenterChannel extends \BaseModel
{
    protected $table = 'call_center_channels';
    protected $description = '呼入电话来源';

    public function city()
    {
        return $this->belongsTo(\Area\City::class);
    }
}
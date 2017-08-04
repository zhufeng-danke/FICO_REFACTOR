<?php namespace Tracking;

/**
 * Tracking\HouseResourceRouse
 *
 * @property int $id
 * @property int $house_id               原房源ID
 * @property string $record_channel
 * @property string $record_type
 * @property int $record_by              重复录入人ID
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon $created_at
 */
class HouseResourceRouse extends \BaseModel
{
    protected $description = '唤醒列表';
    protected $table = 'house_resource_rouses';

    public function record_by_user()
    {
        return $this->belongsTo(HouseResourceUser::class, 'record_by');
    }

    public function house()
    {
        return $this->belongsTo(HouseResource::class);

    }
}
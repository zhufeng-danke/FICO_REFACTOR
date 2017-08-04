<?php namespace Forecast;

use Tracking\HouseResource;

/**
 * 具体逻辑在forecast中, 此model是为方便进行数据操作
 *
 * @property integer $id
 * @property integer $room_amount
 * @property integer $bathroom_amount
 * @property string $date_created
 * @property string $date_updated
 * @property integer $appraiser_id
 * @property string $community
 * @property integer $owner_id
 * @property string $city
 * @property integer $landlord_wish_price_yuan
 * @property integer $room_avg_price_yuan
 * @property integer $contract_year
 * @property integer $free_day
 * @property integer $sales_wish_growth
 * @property integer $sales_wish_price_yuan
 * @property integer $sales_wish_decoration_cost
 * @property boolean $is_confirmed
 * @property boolean $is_active
 * @property string $error_note
 * @property string $address
 * @property boolean $is_draft
 * @property boolean $has_central_heater
 * @property boolean $has_net
 * @property integer $original_room_amount
 * @mixin \Eloquent
 */
class DankePlan extends \BaseModel
{
    use APITrait;

    protected $connection = 'forecast';
    protected $table = 'calc_house_ticket';
    protected $description = '蛋壳模式-收房计算器';

    public static function createFromResource(HouseResource $resource)
    {
        $result = self::post('calc_house/create.api', [
            'city' => $resource->xiaoqu->city,
            'community' => $resource->xiaoqu->name,
            'owner' => Account::findByStaff($resource->offline_executor)->id,
            'original_room_amount' => $resource->record_bedroom_num,
            'room_amount' => 0,
            'bathroom_amount' => 0,
        ]);

        if (!$id = $result['id'] ?? null) {
            throw new \ErrorMessageException(current(current($result)));
        }

        $resource->danke_plan_id = $id;
        $resource->save();

        return self::findOrError($id);
    }

    public function isPassed()
    {
        return false;
    }

    public function getStatus()
    {
        return $this->valuation_status;
    }

    public function internalLink()
    {
        return config('app.url') . "/forecast/calc_house/{$this->id}/room-list/";
    }
}
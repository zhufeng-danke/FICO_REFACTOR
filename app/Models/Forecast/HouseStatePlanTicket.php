<?php namespace Forecast;

use Forecast\BuildingDict\ResourceHouse;
use RiskForecast\HouseStateOperator;

/**
 * Forecast\HouseStatePlanTicket
 *
 * @property integer $id
 * @property integer $before_room_num               改造前的卧室数
 * @property integer $after_room_num                改造后的卧室数
 * @property integer $after_public_bathroom_num     改造后的公共卫生间数量
 * @property integer $after_shower_room_num         改造后的淋浴房数量
 * @property integer $after_kitchen_room_num        改造后的厨房数量
 * @property integer $after_living_room_num         改造后的公共区域数量
 * @property string $date_created                   创建时间
 * @property integer $house_id                      房源id,与resource_house表关联
 * @property integer $owner_id                      创建者id,与account表关联
 * @property integer $after_private_bathroom_num    改造后的独立卫生间数量
 * @property boolean $is_two_prices_readonly        判断人工和模型计算的价格状态是否完成
 * @property integer $manual_decoration_cost        人工预估装配成本
 * @property integer $sales_wish_sum_price_yuan     预估总出房价
 * @property integer $system_decoration_cost        模型预估装配总成本
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class HouseStatePlanTicket extends \BaseModel
{
    protected $connection = 'forecast';
    protected $table = 'house_state_plan_ticket';

    const PLAN_MODE_NORMAL = 0;
    const PLAN_MODE_DETAIL = 1;
    const PLAN_MODE_STANDARD = 2;

    public function save(array $options = [])
    {
        if ($this->isCreated()) {

            if ($this->after_room_num > ($this->before_room_num + 2)) {
                throw new \ErrorMessageException('改造后的卧室数量不能比原卧室数量多两个以上');
            }

        }

        return parent::save($options);
    }

    public static function rules()
    {
        return [
            'before_room_num' => 'integer|min:0',
            'after_public_bathroom_num' => 'integer|min:0',
            'after_private_bathroom_num' => 'integer|min:0',
            'after_shower_room_num' => 'integer|min:0',
            'after_kitchen_room_num' => 'integer|min:0',
        ];
    }

    /**
     * 创建方案的空房间
     * @param $roomType
     * @param $number
     */
    public function createSomeRooms($roomType, $number)
    {
        for ($i = 0; $i < intval($number); $i++) {
            $houseSateRoom = new HouseStateRoom();
            $houseSateRoom->createEmptyRoom($roomType, $this);
        }
    }

    /**
     * 该记录是否创建
     * @return bool
     */
    public function isCreated()
    {
        return !$this->id ? true : false;
    }

    public function state_rooms()
    {
        return $this->hasMany(HouseStateRoom::class, 'plan_id');
    }

    public function resource_house()
    {
        return $this->belongsTo(ResourceHouse::class, 'house_id');
    }

    /**
     * 判断是否所有房间都锁定了
     * @return bool
     */
    public function isAllRoomLocked()
    {
        foreach ($this->state_rooms as $room) {
            if (!$room->is_room_info_readonly || !$room->is_house_state_readonly) {
                return false;
            }
        }
        return true;
    }

    /**
     * 判断房间的卧室数量是否合理
     * @return bool
     */
    public function isBedRoomAllow()
    {
        $privateBathRoomNum = 0;
        foreach ($this->state_rooms as $room) {
            if ($room->room_type === HouseStateRoom::ROOM_TYPE_卧室) {
                $privateBathRoomNum += $room->has_private_bathroom;
            }
        }

        return $privateBathRoomNum === $this->after_private_bathroom_num ;
    }

    /**
     * 生成改造方案名称
     * @param $mode 方案模式,分为0:普通模式, 1:详细模式, 2:标红的普通模式
     */
    public function genPlanName($mode = 0)
    {
        switch ($mode) {
            case self::PLAN_MODE_DETAIL :
                return $this->before_room_num . '室 改 '
                . $this->after_room_num . '室'
                . $this->after_public_bathroom_num . '公卫'
                . $this->after_private_bathroom_num . '独卫'
                . $this->after_shower_room_num . '公淋浴';
            case self::PLAN_MODE_STANDARD :
                return $this->before_room_num . '室 改 '
                . \HTMLWidget::label($this->after_room_num . '室'
                    . $this->after_public_bathroom_num . '公卫'
                    . $this->after_private_bathroom_num . '独卫'
                    . $this->after_shower_room_num . '公淋浴',
                    'primary');
            default :
                return $this->before_room_num . '室 改 '
                . $this->after_room_num . '室'
                . $this->after_public_bathroom_num . '公卫'
                . $this->after_private_bathroom_num . '独卫'
                . $this->after_shower_room_num . '公淋浴';
        }
    }

    /**
     * 请求计算改造方案成本,不返回结果
     */
    public function requestCaculate()
    {
        (new HouseStateOperator())->requestData(
            config('app.url') . '/forecast/house-state/rcalculator/' . $this->id . '.api'
        );
    }
}

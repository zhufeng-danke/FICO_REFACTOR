<?php namespace Tracking;

use Carbon\Carbon;
use Constants\WHETHER;
use Contract\CustomerBooking;
use Contract\WithCustomer;
use Passenger;

/**
 * Tracking\DaikanHistory
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $passenger_id
 * @property string $status
 * @property integer $dealer_id
 * @property integer $suite_id
 * @property string $unsigned_reason_list
 * @property string $lng_lat_gcj02  不同格式的坐标
 * @property string $lng_lat_bd09
 * @property integer $offset        销售坐标和房屋的距离（单位:米）
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon $created_at
 * @property-read \CorpUser $dealer
 * @property-read \User $user
 * @property-read \Suite $suite
 * @property-read \Passenger $passenger
 */
class DaikanHistory extends BaseKanfangHistory implements \Constants\WHETHER
{
    protected $description = '带看预约记录';

    public static function listNote()
    {
        return [
            '价格不满意',
        ];
    }

    public static function rules()
    {
        return [
            'user_id' => 'integer',
            'dealer_id' => 'required|integer',
            'suite_id' => 'required|integer',
        ];
    }

    public static function listStatus()
    {
        return [
            Passenger::STATUS_已预约,
            Passenger::STATUS_已带看未签约,
            Passenger::STATUS_已预订,
            Passenger::STATUS_已签约,
        ];
    }

    public function isBooked()
    {
        return $this->status === Passenger::STATUS_已预订;
    }

    public function isSigned()
    {
        return $this->status === Passenger::STATUS_已签约;
    }

    public function isOrdered()
    {
        return $this->status === Passenger::STATUS_已预约;
    }


    public function afterBooked(CustomerBooking $booking)
    {
        $this->status = Passenger::STATUS_已预订;
        $this->passenger->doBookingRoom($booking);
        $this->passenger->save();
        $this->save();
    }

    public function afterOrdered()
    {
        $this->status = \Passenger::STATUS_已带看未签约;
        $this->daikan_at = Carbon::now();
        $this->save();
    }

    public function afterSigned(WithCustomer $con)
    {
        $this->status = Passenger::STATUS_已签约;
        $this->passenger->doSignRoom($con);
        $this->passenger->save();
        $this->save();
    }

    public function afterPayment(WithCustomer $withCustomer)
    {

        //修改 老推新 短租的奖励金额
        if ($withCustomer->rent_months < 12 && $this->passenger) {
            if ($this->passenger->recommend_customer) {
                $city = $withCustomer->room->suite->xiaoqu->city;
                $this->passenger->recommend_customer->reward_yuan = config('customer_recommend.老推新.short_rent_price')[$city];
                $this->passenger->recommend_customer->save();
            }
        }
    }

    public function dealer()
    {
        return $this->belongsTo(\CorpUser::class);
    }

    public function user()
    {
        return $this->belongsTo(\User::class);
    }

    public function suite()
    {
        return $this->belongsTo(\Suite::class);
    }

    public function passenger()
    {
        return $this->belongsTo(Passenger::class);
    }

    public function contract()
    {
        $relation = $this->hasOne(WithCustomer::class, 'daikan_id');
        return $relation->whereRentApproveStatus(WithCustomer::APPROVE_STATUS_合格);
    }

    public function bookings()
    {
        return $this->hasMany(\Contract\CustomerBooking::class, 'daikan_id');
    }

    public function baiduMapLink()
    {
        //这里存的是gcj02坐标系,需要注意.
        list($lng, $lat) = explode(',', $this->lng_lat_gcj02);
        return "http://api.map.baidu.com/marker?coord_type=gcj02&location=" . $lat . ',' . $lng . "&output=html&title=" . urlencode($this->dealer->name) . "&content=" . urlencode('带看');
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

    /*
     * 获取是否有未签约原因的结果
     */
    public function scopeHasUnsignReason($query, $value)
    {
        if($value) {
            return $value === WHETHER::WHETHER_有
                ? $query->where('unsigned_reason_list', '!=', null)
                : $query->where('unsigned_reason_list', '=', null);
        }
        return $query;
    }


    public function scopeRoomSearch($query, $search_text)
    {
        if (!$search_text) {
            return $query;
        }
        return $query->whereHas('suite.rooms', function ($query) use ($search_text) {
            $query->where('search_text', 'like', "%{$search_text}%");
        });
    }
}

<?php

namespace App\Models\BI;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GeneralRentInformationCollection extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $connection = 'forecast';
    protected $description = '普租情报采集表';
    protected $fillable = ['city','block','xiaoqu_id','xiaoqu_name','lng','lat','building_code','floor','area','bedroom_num','bef_gw','bef_dw','room_status','enviorment_level','sale_price','check_price','source','picture','user_id','create_time','checker_id','check_status','check_note','check_time'];

    const STATUS_待审核 = '待审核';
    const STATUS_已入库 = '已入库';
    const STATUS_作废 = '作废';


    const CITY_北京 = '北京';
    const CITY_上海 = '上海';
    const CITY_深圳 = '深圳';
    const CITY_杭州 = '杭州';

    public static function listState()
    {
        return [
            self::STATUS_待审核 => self::STATUS_待审核,
            self::STATUS_已入库 => self::STATUS_已入库,
            self::STATUS_作废 => self::STATUS_作废,
        ];
    }

    public static function listCity()
    {
        return [
            self::CITY_北京 => self::CITY_北京,
            self::CITY_上海 => self::CITY_上海,
            self::CITY_深圳 => self::CITY_深圳,
            self::CITY_杭州 => self::CITY_杭州,
        ];
    }

}

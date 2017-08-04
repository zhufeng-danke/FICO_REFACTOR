<?php namespace Forecast;

use RiskForecast\HouseStateOperator;

/**
 * Forecast\HousePricing
 *
 * @property integer $id
 * @property string $input_json
 * @property string $result_json
 * @property string $status
 * @property datetime $create_at
 * *@property datetime $update_at
 */
class TempEstimateCaculate extends \BaseModel
{
    protected $connection = 'forecast';
    protected $table = 'temp_estimate_caculate';

    protected $casts = ['result_json' => 'array', 'input_json' => 'array'];

    const CALCULATE_STATUS_计算中 = '计算中';
    const CALCULATE_STATUS_计算完成 = '计算完成';

    /**
     * 请求计算临时方案,不返回结果
     */
    public function requestCaculate()
    {
        (new HouseStateOperator())->requestData(config('app.url') . '/forecast/house-state/rcalculator-02/' . $this->id . '.api');
    }
}

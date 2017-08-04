<?php namespace Forecast; //yubing@wutongwan.org

use Tracking\HouseResource;

/**
 * 预算模型, 具体逻辑在forecast中, 此model是为方便进行数据操作
 *
 * @property integer $id
 * @property string $user_id              用户ID
 * @property string $temp_community       小区
 * @property string $street               具体地址
 * @property string $temp_block
 * @property integer $valuation
 * @property string $valuation_status
 * @property boolean $has_net
 * @property string $central_heater
 * @property integer $origin_bedroom
 * @property integer $origin_parlor
 * @property integer $origin_bathroom
 * @property integer $origin_private_bathroom
 * @property integer $origin_kitchen
 * @property integer $improve_bedroom_total
 * @property string $note
 * @property string $error_note
 * @property string $date_created
 * @property string $date_updated
 * @property integer $fitment_revaluation 装修垫付款本金[优先](分)
 * @property integer $fitment_valuation   装修垫付款本金(分)
 * @property integer $improve_bathroom_total
 * @property integer $improve_kitchen_total
 * @property integer $improve_parlor_total
 * @property integer $improve_private_bathroom_total
 * @property string $temp_fitment_text
 * @property integer $sell_valuation      保底月租金(分)
 * @property string $temp_fitment_text2
 * @property string $temp_fitment_screenshot
 * @property string $temp_fitment_valuation_username
 * @property string $temp_valuation_username
 * @property float $area
 * @property string $fitment_revaluation_note
 * @mixin \Eloquent
 */
class LanJingPlan extends \BaseModel
{
    use APITrait;

    protected $connection = 'forecast';
    protected $table = 'purchase_valuation_ticket';
    protected $description = '蓝鲸模式-收房计算器';

    const STATUS_估价成功 = '估价成功';

    /**
     * 调用forecast的接口创建此对象
     *
     * @param HouseResource $resource
     * @return null|static
     * @throws \ErrorMessageException
     */
    public static function createFromResource(HouseResource $resource)
    {
        $result = self::post('purchase/valuation/create.api', [
            'temp_community' => $resource->xiaoqu->name,
            'temp_block' => $resource->xiaoqu->area->name,
            'street' => $resource->address . $resource->doorplate,
            'user' => Account::findByStaff($resource->offline_executor)->id,
            'has_net' => false,
            'central_heater' => self::transHeating($resource->heating),
            'area' => $resource->record_area ?? 0,
            'origin_bedroom' => $resource->record_bedroom_num ?? 0,
            'origin_parlor' => $resource->record_keting_num ?? 0,
            'origin_bathroom' => 0,
            'origin_private_bathroom' => 0,
            'origin_kitchen' => 0,
        ]);

        if (!$id = $result['id'] ?? null) {
            \Log::info(__METHOD__, [$resource->id, $result]);
            throw new \ErrorMessageException(current(current($result)));
        }

        $resource->lanjing_plan_id = $id;
        $resource->save();

        return self::findOrError($id);
    }

    private static function transHeating($resourceHeating)
    {
        switch ($resourceHeating) {
            case '自采暖':
                return $resourceHeating;

            case '集中供暖':
                return '集中采暖';

            default:
                return '无采暖';
        }
    }

    public function isPassed()
    {
        return $this->valuation_status === self::STATUS_估价成功;
    }

    public function getStatus()
    {
        return $this->valuation_status;
    }

    public function internalLink()
    {
        if ($this->isPassed()) {
            return config('app.url') . "/forecast/purchase/valuation/detail/{$this->id}/";
        } else {
            return config('app.url') . "/forecast/purchase/valuation/{$this->id}/";
        }
    }

    public function getFitmentValuation()
    {
        return $this->fitment_revaluation ?: $this->fitment_valuation;
    }
}
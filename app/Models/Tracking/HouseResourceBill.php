<?php namespace Tracking;

/**
 * HouseResourceBill
 *
 * @property integer $id 
 * @property integer $house_resource_id 
 * @property integer $house_resource_user_id 
 * @property string $type 
 * @property integer $transfer_id 
 * @property integer $amount 
 * @property string $tag 
 * @property string $note 
 * @property \Carbon\Carbon $updated_at 
 * @property \Carbon\Carbon $created_at 
 * @property-read HouseResource $houseResource 
 * @property-read \Trade\Transfer $transfer
 */
class HouseResourceBill extends \Trade\BaseBill
{
    protected $description = '外部录入的奖励流水账单';

    const TYPE_转入 = '转入';
    const TYPE_转出 = '转出';

    CONST PRICE_PER_HOUSE = 18;     // 每条信息的奖励金额
    CONST MIN_WITHDRAW = 20;       // 超过最小值后才允许提现

    const TAG_赠送 = '赠送';
    const TAG_奖励 = '奖励';
    const TAG_罚款 = '罚款';
    const TAG_提现 = '提现';
    const TAG_新年红包 = '新年红包';    //  额外红包

    public function houseResource()
    {
        return $this->belongsTo(HouseResource::class);
    }

    public static function listTypeText()
    {
        return [
            self::TYPE_转入 => '奖励',
            self::TYPE_转出 => '提现',
        ];
    }

    public function transfer()
    {
        return $this->belongsTo(\Trade\Transfer::class);
    }
}

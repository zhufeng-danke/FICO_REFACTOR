<?php
//  gaoyufeng@dankegongyu.com

namespace Tracking\Dispatch;

/**
 * Tracking\Dispatch\PassengerSaleTeamWorkload
 *
 * @property int $id
 * @property int $passenger_sale_team_id    // 销售团队ID
 * @property int $from                      // 团队排名开始
 * @property int $to                        // 团队排名结束
 * @property int $number                    // 客源量
 * @property string $type                   // 类型
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon $created_at
 */

class PassengerSaleTeamWorkload extends \BaseModel
{
    protected $description = '客源销售团队工作量';

    const TYPE_保底量 = '保底量';
    const TYPE_饱和量 = '饱和量';

    public static function rules()
    {
        return [
            'passenger_sale_team_id' => 'required|integer',
            'from' => 'required|integer',
            'to' => 'required|integer',
            'number' => 'required|integer',
            'type' => 'in:' . join(',', self::constants('type')),
        ];
    }
}



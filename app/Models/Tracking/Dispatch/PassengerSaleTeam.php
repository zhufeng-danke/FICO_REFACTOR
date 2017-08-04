<?php
//  gaoyufeng@dankegongyu.com

namespace Tracking\Dispatch;

use Area\City;

/**
 * Tracking\Dispatch\PassengerSaleTeam
 *
 * @property int $id
 * @property int $team_id           // 团队ID
 * @property int $city_id           // 城市ID
 * @property string $status         // 状态
 * @property string $name           // 团队名称
 * @property string $staff_ranking  // 员工上周排名（json串）
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon $created_at
 */
class PassengerSaleTeam extends \BaseModel
{
    protected $description = '客源销售团队';

    const STATUS_启用 = '启用';
    const STATUS_停用 = '停用';

    public static function rules()
    {
        return [
            'team_id' => 'required|integer',
            'city_id' => 'required|integer',
            'status' => 'in:' . join(',', self::constants('status')),
        ];
    }

    public function corp_department()
    {
        return $this->belongsTo(\CorpDepartment::class, 'team_id');
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function team_workloads()
    {
        return $this->hasMany(PassengerSaleTeamWorkload::class, 'passenger_sale_team_id');
    }

    public function listTeamWorkload($type)
    {
        return $this->team_workloads
            ->where('type', $type)
            ->map(function ($val) {
                return '排名：' . $val->from . '-' . $val->to . ' ' . $val->type . ' ' . $val->number;
            })->toArray();
    }
}
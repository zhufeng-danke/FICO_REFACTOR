<?php
//  qiyanjun@dankegongyu.com

namespace Tracking\Dispatch;

use Area\Block;

/**
 * Tracking\Dispatch\CoterieBlockTeam
 *
 * @property int $id
 * @property int $block_id                  // 商圈id
 * @property int $team_id                   // 团队id
 * @property string $type                   // 类型
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon $created_at
 */
class CoterieBlockTeam extends \BaseModel
{
    protected $description = '商圈分派团队';

    const TYPE_钉钉团队 = '钉钉团队';

    const PASSENGER_TEAM_TEXT = [
        \Area::CITY_北京市 => '##线下团队##出房团队##销售部门#',
        \Area::CITY_上海市 => '##出房团队##销售部门##上海#',
        \Area::CITY_深圳市 => '##出房团队##销售部门##深圳#',
        \Area::CITY_杭州市 => '##出房团队##销售部门##杭州#',
    ];

    public function team()
    {
        return $this->belongsTo(\CorpDepartment::class);
    }

    public static function listCityTeams(Block $block)
    {
        $city = $block->district->city->name ?? '';
        if (!$city || !array_key_exists($city, self::PASSENGER_TEAM_TEXT)) {
            throw new \ErrorMessageException('商圈不存在！');
        }
        $teamText = self::PASSENGER_TEAM_TEXT[$city];
        return \CorpDepartment::where('parent_text', 'like', '%' . $teamText . '%')->get();
    }

    /**
     * 保存圈子分配的团队
     * @param array $teamList     团队列表
     * @param array $selectedList 原始团队列表
     * @param int $blockId        商圈id
     */
    public static function changeTeam($teamList, $selectedList, $blockId)
    {
        foreach ($teamList as $aid) {
            if (!isset($selectedList[$aid])) {
                $cdb = new CoterieBlockTeam();
                $cdb->block_id = $blockId;
                $cdb->team_id = $aid;
                $cdb->type = self::TYPE_钉钉团队;
                $cdb->saveOrError();
            }
        }

        foreach ($selectedList as $aid => $dispatchTeam) {
            if (!in_array($aid, $teamList)) {
                $dispatchTeam->delete();
            }
        }
    }
}

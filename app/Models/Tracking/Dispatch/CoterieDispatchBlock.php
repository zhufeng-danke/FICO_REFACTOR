<?php
//  qiyanjun@dankegongyu.com

namespace Tracking\Dispatch;

use Area\Block;

/**
 * Tracking\Dispatch\CoterieDispatchBlock
 *
 * @property int $id
 * @property int $coterie_id        // 圈子ID
 * @property int $block_id          // 商圈ID
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon $created_at
 */
class CoterieDispatchBlock extends \BaseModel
{
    protected $description = '圈子分派商圈表';

    public function block()
    {
        return $this->belongsTo(Block::class);
    }

    public function coterie()
    {
        return $this->belongsTo(Coterie::class);
    }

    /**
     * 保存圈子分配的商圈
     * @param array $blockList    商圈列表
     * @param array $selectedList 原始商圈列表
     * @param int $coterieId      圈子id
     */
    public static function changeBlock($blockList, $selectedList, $coterieId)
    {
        foreach ($blockList as $aid) {
            if (!isset($selectedList[$aid])) {
                $cdb = new CoterieDispatchBlock();
                $cdb->coterie_id = $coterieId;
                $cdb->block_id = $aid;
                $cdb->saveOrError();
            }
        }

        foreach ($selectedList as $aid => $dispatchBlock) {
            if (!in_array($aid, $blockList)) {
                $dispatchBlock->delete();
            }
        }
    }
}

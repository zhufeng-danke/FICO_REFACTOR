<?php


use Carbon\Carbon;

Trait passengerTrait
{
    /** 从excel中导入租客信息, 格式参考从自如导出的表
     * @param $rows
     */
    public static function importFromArr($rows)
    {

        foreach ($rows as $rowId => $row) {
            $p = new Passenger;
            if ($row->REMOTENUMBERFMT) {
                $p->mobile = $row->REMOTENUMBERFMT;
                $p->name = '竞对:' . $p->mobile;
                $p->source = $p::TYPE_批量导入;
                $p->block_id = self::parseBlockFromZiroomTel($row->EXTENSIONNUMBER);
                $p->recycle_at = Carbon::now();
                $p->is_recycle = \Constant::IF_是;
                $p->created_at = $row->INITIATEDDATE;
                $p->note = $p->block ? ('城市: ' . $p->block->city()->name) : '';
            } else {
                //  电销公海批量导入客户信息
                $p->mobile = $row->MOBILENUMBER;
                $p->name = $row->CITYFROM ?? ''. $p::TYPE_批量导入;
                $p->status = $p::STATUS_未分派;
                $p->source = $row->SOURCEFROM;
            }
            $p->record_type = $p::TYPE_批量导入;
            $p->record_by_corp_id = 0;  //  spec

            if (!preg_match('/1[3-9]\d{9}/', $p->mobile)) {
                continue;
            }

            try {
                $p->save();
            } catch (Exception $e) {
                //  批量导入失败的数据还没有利用价值, 暂时只打日志
                Log::info('[Passenger.importFromArr]', $p->toArray());
            }

        }
    }

    public static function parseBlockFromZiroomTel($code)
    {
        static $mapping = null;
        if (is_null($mapping)) {
            $mapping = config('passenger-ziroom-code');
            foreach ($mapping as $key => &$val) {
                $val = $val[0];
            }
        }

        if ($name = $mapping[$code] ?? null) {
            return Area::whereLevel(Area::LEVEL_商圈)->where('name', 'like', "{$name}%")->first()->id ?? null;
        }

        return null;
    }

    public static function getLabel($field)
    {
        return [
            'id' => '编号',
            'name' => '客户',
            'status' => '状态',
            'result' => '结果',
            'online_check_status' => '电销核验结果',
            'online_push_result' => '电销推送结果',
            'dealer_check_status' => '销售核验结果',
            'mobile' => '客户手机号',
            'gender' => '性别',
            'job' => '工作',
            'block' => '商圈',
            'note' => '备注',
            'recorder' => '发布人',
            'assigner' => '当前分派人',
            'onliner' => '电销核验人',
            'dealer' => '当前销售',
            'created_at' => '创建时间',
            'daikan_date' => '预约带看时间',
            'dealer_assign_at' => '分派时间',
            'tracking_last_at' => '最后跟踪时间',
            'recycle_at' => '回收时间',
            'record_type' => '录入类型',
        ] [$field] ?? '';
    }

    /**
     * @param $field
     * @return Closure
     */
    public static function getGridCell($field)
    {
        return [

            'status' => function ($status, Passenger $passenger) {
                return
                    ($passenger->isRecycle() ? HTMLWidget::label('公海', 'danger') . ' ' : '') .
                    ModelTool::label($passenger, 'status') . ' ' .
                    (($delay = $passenger->delayTime()) ? HTMLWidget::label("{$delay}小时未处理", 'warning') : '');
            },

            'recorder' => function ($recorder, Passenger $passenger) {
                return $recorder->name ?? null;
            },

            'block' => function ($block, Passenger $passenger) {
                if ($block) {
                    return
                        $passenger->searchLink('block_name', $block->name)
                        . ($passenger->team_id ? ' | ' . $passenger->searchLink('team_title',
                                $passenger->team->title) : '');
                }
            },

            'assigner' => config('rapyd.cell.staff'),
            'onliner' => config('rapyd.cell.staff'),
            'dealer' => config('rapyd.cell.staff'),

        ] [$field] ?? config('rapyd.cell.original');

    }

    public function getRender($field)
    {
        $cell = self::getGridCell($field);
        return $cell($this->$field, $this);
    }

    /**
     * @param DataGrid $grid
     * @param array $fields
     */
    public static function addFieldsToGrid($grid, array $fields)
    {
        foreach ($fields as $field) {
            $grid->add($field, self::getLabel($field))->cell(self::getGridCell($field));
        }
    }
}

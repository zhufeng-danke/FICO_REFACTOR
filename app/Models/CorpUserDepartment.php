<?php

/**
 * CorpUser和CorpDepartment的关联表
 *
 * @property integer $id
 * @property integer $staff_id              CorpUser ID
 * @property integer $department_id         CorpDepartment ID
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \CorpUser $staff
 * @property-read \CorpDepartment $department
 * @mixin \Eloquent
 */
class CorpUserDepartment extends \BaseModel
{
    protected $description = '员工-部门对应表';

    public function staff()
    {
        return $this->belongsTo(CorpUser::class);
    }

    public function department()
    {
        return $this->belongsTo(CorpDepartment::class);
    }
}
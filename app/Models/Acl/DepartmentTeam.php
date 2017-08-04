<?php namespace Acl;

/**
 * Acl\DepartmentTeam
 *
 * 部门和辖区的关联表
 *
 * @property integer $id
 * @property string $name       名称
 * @property integer $area_id   商圈
 * @property integer $dep_id    部门
 * @property integer $team_id   辖区
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class DepartmentTeam extends \BaseModel
{
    protected $table = 'acl_department_teams';

    protected $description = '部门和辖区的关联表';

    public function department()
    {
        return $this->belongsTo(\CorpDepartment::class, 'dep_id');
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function area()
    {
        return $this->belongsTo(\Area::class);
    }

}
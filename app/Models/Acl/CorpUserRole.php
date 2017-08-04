<?php namespace Acl;

/**
 * Acl\CorpUserRole 关联关系表
 *
 * @property integer $id
 * @property integer $role_id
 * @property integer $corp_user_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class CorpUserRole extends \BaseModel
{
    protected $table = 'acl_corp_user_roles';

    protected $description = '员工-角色对应表';

    public function corp_user()
    {
        return $this->belongsTo(\CorpUser::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

}
<?php namespace Acl;

/**
 * Acl\Role
 *
 * @property integer $id
 * @property string $title
 * @property string $intro
 * @property integer $leader_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Role extends \BaseModel
{
    protected $table = 'acl_roles';
    protected $description = '后台角色';

    public function corp_users()
    {
        return $this->belongsToMany(\CorpUser::class, 'acl_corp_user_roles', 'role_id', 'corp_user_id');
    }

    public function leader()
    {
        return $this->belongsTo(\CorpUser::class);
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'acl_permission_roles', 'role_id', 'permission_id');
    }

    //  获得这个团队负责的区域
    public function areas()
    {
        return $this->belongsToMany(\Area::class, 'acl_permission_by_areas', 'role_id', 'area_id');
    }

}
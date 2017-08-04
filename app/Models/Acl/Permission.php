<?php namespace Acl;

/**
 * Acl\Permission
 *
 * @property integer $id
 * @property string $key 名称
 * @property string $intro
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Permission extends \BaseModel
{
    protected $table = 'acl_permissions';

    protected $description = '后台权限';

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'acl_permission_roles', 'permission_id', 'role_id');
    }
}

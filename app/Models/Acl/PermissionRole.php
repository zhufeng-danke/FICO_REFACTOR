<?php namespace Acl;

/**
 * Acl\PermissionRole
 *
 * @property integer $id
 * @property integer $permission_id
 * @property integer $role_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class PermissionRole extends \BaseModel
{
    protected $table = 'acl_permission_roles';
    protected $description = '角色-权限对应表';

}
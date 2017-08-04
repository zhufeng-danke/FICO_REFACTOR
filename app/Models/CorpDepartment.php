<?php
//yubing@wutongwan.org

/**
 * 企业部门组织结构
 * http://ddtalk.github.io/dingTalkDoc/#获取部门列表
 *
 * @property integer $id
 * @property string $name                   部门名称
 * @property integer $leader_id             主管id, 关联CorpUser
 * @property integer $dingtalk_id           钉钉中的id, 从钉钉同步得来
 * @property integer $parent_id             父部门id，根部门为null
 * @property string $parent_text            便于层级搜索的关键字
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon $created_at
 * @property-read CorpUser $leader
 * @property-read CorpDepartment $parent
 * @property-read string[] $parents
 * @property-read \Illuminate\Database\Eloquent\Collection|\CorpUser[] $staffs
 * @mixin \Eloquent
 */
class CorpDepartment extends BaseModel
{
    protected $description = '企业部门组织结构';

    protected static function boot()
    {
        parent::boot();

        static::saving(function (self $department) {
            $department->buildParentText();
        });
    }

    public function parent()
    {
        return $this->belongsTo(CorpDepartment::class);
    }

    public function leader()
    {
        return $this->belongsTo(CorpUser::class);
    }

    public function staffs()
    {
        return $this->belongsToMany(CorpUser::class, 'corp_user_departments', 'department_id', 'staff_id');
    }

    public function buildParentText()
    {
        $current = $this;
        $text = '';

        while ($current) {
            $text .= "#{$current->name}#";

            $current = $current->parent;
        }

        $this->parent_text = $text;
    }

    /**
     * @return string[]
     */
    public function getParentsAttribute()
    {
        return array_values(array_filter(explode('#', $this->parent_text), function ($name) {
            return $name;
        }));
    }
}

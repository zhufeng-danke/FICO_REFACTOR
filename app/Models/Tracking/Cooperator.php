<?php

use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use Tracking\BaseCooperator;

/**
 * Cooperator
 *
 * @property integer $id
 * @property string $name
 * @property string $mobile
 * @property string $company
 * @property string $status
 * @property integer $user_id
 * @property string $note
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $deleted_at
 * @property \Carbon\Carbon $updated_at
 * @property integer $verified_by
 * @property \Carbon\Carbon $verified_at
 * @property-read \User $user
 * @property-read \CorpUser $verifier
 */
class Cooperator extends BaseCooperator
{
    use SoftDeletes;

    protected $description = '外部渠道';

    protected $dates = ['verified_at', 'deleted_at'];

    public static function rules()
    {
        return [
            'name' => 'required',
            'mobile' => 'required|mobile',
        ];
    }

    /** 城市名称，从areas表里面获取城市数组
     *
     * @return array
     */
    public static function listCity()
    {
        return \Area::whereLevel(\Area::LEVEL_城市)->pluck('name', 'id')->all();
    }

    /** 公司名要用来统计数据, 如有修改都必须洗数据
     * @return array
     */
    public static function listCompany()
    {
        return [
            '个人',
            '个人中介',
        ];
    }

    public function checkExist()
    {
        if (self::whereName($this->name)->whereCompany($this->company)->exists()) {
            throw new ErrorMessageException('该渠道已存在, 请勿重复录入');
        };
    }

    public static function boot()
    {
        self::commentHistory([
            'status' => '认证状态',
        ]);

        parent::boot();
    }

    public function save(array $options = [])
    {
        if (!$this->id) {
            $this->checkExist();
        }

        //  员工修改状态时记录
        if ($this->isDirty('status') && CorpAuth::user()) {
            $this->verified_by = CorpAuth::user()->id;
            $this->verified_at = Carbon::now();
        }

        return parent::save($options);
    }

    public function adminLink()
    {
        return '';//link_to(action('Admin\Tracking\PassengerController@getList') . '?search=1&cooperator_name=' . $this->name, $this->name);
    }
}

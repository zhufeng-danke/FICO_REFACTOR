<?php namespace Forecast;// zhangwei@wutongwan.org

/**
 * Forecast中的用户数据
 *
 * @property integer $id
 * @property string $password
 * @property string $last_login
 * @property boolean $is_superuser
 * @property string $username
 * @property string $email
 * @property string $code
 * @property boolean $is_staff
 * @property boolean $is_active
 * @property boolean $is_verified
 * @property string $date_joined
 * @property string $cipher
 * @property string $old_password
 * @property string $old_old_password
 * @property string $device_id
 * @property integer $invite_status
 * @property string $avatar
 * @property string $nickname
 * @property string $realname
 * @property boolean $prefer_nickname
 * @property string $dingtalk_id
 */
class Account extends \BaseModel
{
    protected $description = 'Forecast中的账户';

    protected $connection = 'forecast';
    protected $table = 'account';

    /**
     * 根据Laputa中的CorpUser查找对应的Forecast Account
     * 为保证forecast部分独立才把这个函数放这里, 其实放CorpUser更合适
     *
     * @param \CorpUser $staff
     * @return self
     */
    public static function findByStaff(\CorpUser $staff)
    {
        $account = self::whereDingtalkId($staff->dingtalk_id)->first();
        if (!$account) {
            throw new \ErrorMessageException('找不到您的账户数据, 请联系王文沛');
        }
        return $account;
    }

    public function user()
    {
        return $this->belongsTo(\CorpUser::class, 'dingtalk_id', 'dingtalk_id');
    }
}
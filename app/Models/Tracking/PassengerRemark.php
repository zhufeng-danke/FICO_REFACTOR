<?php

use App\Jobs\Queueable\Customers\Passenger\TopRemarkPassenger;

/**
 * PassengerRemark    客户列表置顶关注
 *
 * @property integer $id
 * @property integer $dealer_id    后台登录账户的id
 * @property integer $passenger_id 客户的id
 * @property integer $is_remark    是否标识为置顶关注,0为置顶关注,1为已置顶关注
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class PassengerRemark extends BaseModel
{
    const REMARK_STATUS_置顶关注 = 1;
    const REMARK_STATUS_未置顶关注 = 0;

    public static function batchMarkStatus($ids, $status)
    {
        dispatch(new TopRemarkPassenger($ids, $status, \CorpAuth::id()));
    }

    public static function listRemarkStatus()
    {
        return [
            self::REMARK_STATUS_置顶关注 => '置顶关注',
            self::REMARK_STATUS_未置顶关注 => '未置顶关注',
        ];
    }
}

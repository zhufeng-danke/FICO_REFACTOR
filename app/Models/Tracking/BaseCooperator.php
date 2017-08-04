<?php
//  zhanghuiren@wutongwan.com

namespace Tracking;

use CorpUser;
use Trade\BankBranch;

abstract class BaseCooperator extends \BaseModel
{
    use CommentHistoryTrait;

    const STATUS_未认证 = '未认证';
    const STATUS_已认证 = '已认证';
    CONST STATUS_失效 = '失效';

    const CARDS = [
        'card_bank' => '开户支行',
        'card_name' => '银行卡姓名',
        'card_id' => '银行卡卡号',
    ];

    public function isVerified()
    {
        return $this->status === self::STATUS_已认证 && $this->verified_by;
    }

    public function user()
    {
        return $this->belongsTo(\User::class);
    }

    public function verifier()
    {
        return $this->belongsTo(CorpUser::class, 'verified_by');
    }

    public function bank_branch()
    {
        return $this->belongsTo(BankBranch::class);
    }

    public function inviter()
    {
        return $this->belongsTo(CorpUser::class, 'invited_by_staff_id');
    }

    public function city()
    {
        return $this->belongsTo(\Area::class);
    }

}
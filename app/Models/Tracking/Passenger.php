<?php

use Carbon\Carbon;
use Contract\CustomerBooking;
use Contract\WithCustomer;
use Tracking\CommentHistoryTrait;
use Tracking\DaikanHistory;

/**
 * Passenger    销售机会
 *
 * @property integer $id
 * @property string $name
 * @property string $mobile
 * @property string $gender
 * @property integer $price                             该价格及以下500的范围
 * @property string $job
 * @property string $source                             客户来源
 * @property string $note
 * @property string $tags                               给客户打的标签
 * @property integer $enterprise_coupon_id              企业优惠码
 * @property integer $block_id                          商圈ID
 * @property integer $team_id                           负责客户的销售商圈。冗余字段,方便查询
 * @property string $status                             跟进状态: 未预约、带看中、已带看等等
 * @property string is_remark                           置顶状态: 置顶未置顶
 * @property string $online_status                      电销状态
 * @property string $online_check_status                电销核验结果
 * @property string $online_push_result                 电销推送结果
 * @property integer $online_check_by                   电销核验人
 * @property string $online_last_at                     电销核验时间
 * @property string $result                             跟进结果: 有效、暂缓
 * @property string $result_reason                      失效原因
 * @property string $online_check_reason                核验无效原因
 * @property string $online_push_reason                 未推送原因
 * @property string $dealer_check_status                销售核验结果
 * @property string $first_came_to_city                 是否第一次来本市(容联)
 * @property integer $people_number                     居住人数(容联)
 * @property string $transport                          交通方式(容联)
 * @property string $origin_called_no                   被呼入电话(容联)
 * @property string $stage                              接听状态(容联)
 * @property integer $first_called_by                   管家首次呼叫(容联)
 * @property \Carbon\Carbon $first_called_at            管家首次呼叫时间(容联)
 * @property string $call_sheet_id                      通话记录ID(容联)
 * @property \Carbon\Carbon $next_active_date           暂缓时, 下次可用时间
 * @property integer $user_id
 * @property string $record_type                        录入类型: 电销录、渠道录入、销售录入、批量导入等
 * @property integer $record_by_corp_id                 这两个共同表示录入人的信息
 * @property integer $record_by_cooperator_id           对应cooperators表
 * @property \Carbon\Carbon $daikan_date                预约带看日期
 * @property string $daikan_real_time                   时间带看时间, 按取密码为准
 * @property \Carbon\Carbon $active_at                  任务激活时间
 * @property \Carbon\Carbon $finish_at                  任务结束时间
 * @property \Carbon\Carbon $tracking_last_at           最后跟进时间
 * @property integer $dealer_assign_by                  分派人
 * @property integer $dealer_assign_to                  当前负责销售
 * @property \Carbon\Carbon $dealer_assign_at           分派时间
 * @property \Carbon\Carbon $recycle_at                 回收到公海时间。非 null 说明正在公海里
 * @property string $is_recycle                         是否在公海中
 * @property string $queue                              客服技能组
 * @property string $missed_calls_claim_status          认领状态:认领,未认领
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Passenger extends BaseModel
{
    use passengerTrait;
    use CommentHistoryTrait;

    protected $description = '客源记录';

    protected $dates = [
        'daikan_date',
        'active_at',
        'finish_at',
        'dealer_assign_at',
        'tracking_last_at',
        'recycle_at',
        'next_active_date',
    ];

    const STATUS_未分派 = '未分派';
    const STATUS_未联系 = '未联系';
    const STATUS_已联系 = '已联系';
    const STATUS_已预约 = '已预约';
    const STATUS_已带看未签约 = '已带看未签约';
    const STATUS_已预订 = '已预订';
    const STATUS_已签约 = '已签约';
    const STATUS_失效 = '失效';

    const ONLINE_STATUS_待处理 = '待处理';
    const ONLINE_STATUS_未接听 = '未接听';
    const ONLINE_STATUS_再跟进 = '再跟进';
    const ONLINE_STATUS_已结束 = '已结束';
    const ONLINE_STATUS_已推看 = '已推看';

    const ONLINE_CHECK_STATUS_待处理 = '待处理';
    const ONLINE_CHECK_STATUS_有效 = '有效';
    const ONLINE_CHECK_STATUS_无效 = '无效';

    const ONLINE_PUSH_RESULT_未推送 = '未推送';
    const ONLINE_PUSH_RESULT_已推送 = '已推送';

    const DEALER_CHECK_STATUS_有效 = '有效';
    const DEALER_CHECK_STATUS_无效 = '无效';

    const DEFAULT_DEALER_CODE = 'dongleilei';

    const RESULT_有效 = '有效';
    const RESULT_暂缓 = '暂缓';
    const RESULT_无效 = '无效';

    const FAIL_REASON_TYPE_核验无效 = '核验无效';
    const FAIL_REASON_TYPE_未推送 = '未推送';
    const FAIL_REASON_TYPE_带看未签约 = '带看未签约';

    const TYPE_官网 = '官网';
    const TYPE_渠道对公 = '渠道对公';
    const TYPE_渠道对私 = '渠道对私';
    const TYPE_批量导入 = '批量导入';
    const TYPE_电销 = '电销';
    const TYPE_在线团队 = '在线团队';
    const TYPE_客服 = '客服';
    const TYPE_58团队 = '58团队';
    const TYPE_线下 = '线下';
    const TYPE_老推新 = '老推新';
    const TYPE_企业活动 = '企业活动';
    const TYPE_市场部 = '市场部';
    const TYPE_市场活动 = '市场活动';

    const SOURCE_官网 = '官网';
    const SOURCE_58 = '58';
    const SOURCE_自营 = '自营';
    const SOURCE_老客户转介绍 = '老客户转介绍';
    const SOURCE_个人 = '个人';
    const SOURCE_个人中介 = '个人中介';
    const SOURCE_市场活动优惠券减500元 = '市场活动优惠券减500元';
    const SOURCE_企业客户首月减500元 = '企业客户首月减500元';
    const SOURCE_转租 = '转租';
    const SOURCE_换租 = '换租';
    const SOURCE_春眠分期 = '春眠分期';
    const SOURCE_椋鸟第二季 = '椋鸟第二季';
    const SOURCE_非正式来源 = '非正式来源';

    const PARAM_UNSIGN = 'unsign';
    const EFFECTIVE_容联匿名 = '容联匿名';

    const REMARK_STATUS_置顶关注 = '置顶';
    const REMARK_STATUS_未置顶关注 = '未置顶';

    const MISSED_CALLS_STATUS_认领 = '认领';
    const MISSED_CALLS_STATUS_未认领 = '未认领';
    const EFFECTIVE_容联认领 = '容联认领';

    public static function boot()
    {
        self::commentHistory([
            'status' => '状态',
            'mobile' => '手机',
            'block.name' => '商圈名',
            'team.title' => '分组',
            'result' => '结果',
            'result_reason' => '无效原因',
            'online_status' => '电销结果',
            'source' => '客户来源',
        ]);

        parent::boot();
    }

    public function recommend_customer()
    {
        return $this->hasOne(\Recommend\RecommendCustomer::class, 'recommended_passenger_id');
    }

    public static function listSource($isRentalSales = null)
    {
        $source = ['老客户转介绍', '个人', '个人中介', '58', '安居客', '地推', '自营', '企业客户首月减500元', '转租', '换租'];

        if ($isRentalSales) {
            $source = array_diff($source, ['老客户转介绍', '个人', '个人中介']);
        }
        return [
            '常规：' => $source,
            '市场部：' => WithCustomer::listCustomerSource(WithCustomer::SOURCE_客服),
            '其它' => ['退转换续', self::SOURCE_非正式来源],
        ];
    }

    public static function listBankChannelSource()
    {
        return [
            '常规: ' => [
                self::SOURCE_官网,
                self::SOURCE_58,
                self::SOURCE_自营,
                self::SOURCE_老客户转介绍,
                self::SOURCE_个人中介,
                self::SOURCE_个人
            ]
        ];
    }

    public static function listTags()
    {
        return [
            '双人床',
            '三室内',
            '有电梯',
            '有阳台',
            '有独卫',
            '不朝北',
            '非隔断',
            '15平以上',
            '信风水',
            '新小区',
        ];
    }

    public static function listResultReason()
    {
        return [
            '电销核验状态无效原因（电销）' => [
                '离开本市',
                '已租蛋壳',
                '已租其他',
                '续租原房源',
                '非蛋壳目标客户（养宠物，有50岁以上老人，有6岁以下儿童，三人以上居住，1000以下预算，床位、地下室、平房等）',
                '广告',
                '商业合作',
                '业务委托',
                '同行骚扰',
            ],
            '客户有效未推送（电销）' => ['客户指定区域无房', '客户需求区域有房价格高', '客户七日内不看房'],
            '推送未带看（销售）' => ['推送未带看'],
            '销售带看未签约（销售）' => ['房子价格高', '房子离地铁远', '现住房子未到期不着急', '离上班地方远', '跟咨询价格差距大无法接受', '特殊要求无法得到满足', '同住室友原因', '客户预算低'],
        ];
    }

    public static function listFailReason($failReasonType)
    {
        $data = [
            self::FAIL_REASON_TYPE_核验无效 => [
                '电销核验状态无效原因（电销）' => [
                    '离开本市',
                    '已租蛋壳',
                    '已租其他',
                    '续租原房源',
                    '非蛋壳目标客户（养宠物，有50岁以上老人，有6岁以下儿童，三人以上居住，1000以下预算，床位、地下室、平房等)',
                    '广告',
                    '商业合作',
                    '业务委托',
                    '同行骚扰'
                ]
            ],
            self::FAIL_REASON_TYPE_未推送 => [
                '客户有效未推送（电销）' => ['客户指定区域无房', '客户需求区域有房价格高', '客户七日内不看房']
            ],
            self::FAIL_REASON_TYPE_带看未签约 => [
                '房子价格高',
                '房子离地铁远',
                '现住房子未到期不着急',
                '离上班地方远',
                '跟咨询价格差距大无法接受',
                '特殊要求无法得到满足',
                '同住室友原因',
                '客户预算低'
            ],
        ];
        return $data[$failReasonType] ?? self::listResultReason();
    }

    public static function listPrice($placeholder = '* 预算 *')
    {
        $prices = ['' => $placeholder];
        foreach (range(1500, 5000, 500) as $price) {
            $prices [$price] = ($price - 500) . ' ~ ' . $price;
        }
        return $prices;
    }

    public static function listTransport()
    {
        return [
            '地铁',
            '步行',
            '公交',
            '骑行',
            '汽车',
            '电动车/摩托车',
        ];
    }

    // 待看时间段
    public static function listDaikanTimesRange()
    {
        $daikanTimes = ['' => '预约看房时间段'];
        foreach (range(0, 22, 2) as $times) {
            $daikanTimes[$times . '-' . ($times + 2)] = $times . '-' . ($times + 2);
        }
        return $daikanTimes;
    }

    public static function rules()
    {
        return [
            'name' => 'required_unless:online_check_status,无效|max:30',
            'mobile' => 'required|max:11',
            'source' => 'required',
            'record_type' => 'in:' . join(',', self::constants('type')),
            'result_reason' => "required_if:result,无效",
        ];
    }

    public function allow($action)
    {
        static $visitor, $isDealer, $isLeader;
        if (is_null($visitor)) {
            $visitor = CorpAuth::user();
            $isDealer = $visitor->id === $this->dealer_assign_to;
            $isLeader = $this->dealer ? $visitor->isDepLeaderOf($this->dealer) : false; //  任务执行人的主管
        }

        switch ($action) {
            case 'daikan':
                return $isDealer;
            case 'reActive':
                return !$this->isActive();
            case 'finish':
                return true;
            case 'claim': // 销售认领
                return !$this->dealer_assign_to && $this->team && role($this->team->title);
            case 'tuikan':
                return can('维护预约带看信息');
            case 'assign-dealer':
                return $isLeader || $isDealer || $this->allow('claim') || $this->allow('tuikan');
            case 'modify-source':
                return can('出房跟踪_修改客户来源') || $this->isCreating();
        }

        return false;
    }

    //  有预订
    public function isBooked()
    {
        return $this->daikan_date;
    }

    // 和Human表中手机号关联
    public function isHuman()
    {
        return $this->hasOne(\Human::class, 'mobile', 'mobile');
    }

    public function isRecycle()
    {
        return $this->is_recycle === \Constant::IF_是;
    }

    //  有成单
    public function isSigned()
    {
        return $this->status === self::STATUS_已签约;
    }


    public static function delayRule()
    {
        return [
            //  每个状态最多持续多久, 从哪个时间计算
            self::STATUS_未分派 => ['active_at', 1],
            self::STATUS_未联系 => ['dealer_assign_at', 1],
        ];
    }

    public function delayTime()
    {
        $rule = self::delayRule();
        if (isset($rule[$this->status])) {
            list($feild, $limit) = $rule[$this->status];
            $final = $this->$feild->addHour($limit);
            if (Carbon::now()->gt($final)) {
                return Carbon::now()->diffInHours($this->$feild);
            }
        }
        return 0;
    }

    public function unclaimedTime()
    {
        return Carbon::now()->diffInHours($this->created_at);

    }

    public function save(array $options = [])
    {
        if ($this->isCreating() && !in_array($this->source, [self::SOURCE_换租, self::SOURCE_转租])) {
            $this->status = self::STATUS_未分派;
            $this->active_at = Carbon::now();
        }

        if ($this->online_check_status === self::ONLINE_CHECK_STATUS_无效 && !$this->online_check_reason) {
            throw new ErrorMessageException('请选择无效原因');
        }
        if ($this->online_check_status === self::ONLINE_CHECK_STATUS_有效
            && $this->online_push_result === self::ONLINE_PUSH_RESULT_未推送
            && !$this->online_push_reason
        ) {
            throw  new ErrorMessageException('请选择未推送原因');
        }

        if ($this->isDirty('dealer_assign_to')) {
            $this->is_remark = self::REMARK_STATUS_未置顶关注;
        }

        if (CorpAuth::user() && CorpAuth::user()->isRentalOnlineSales()) {
            if ($this->online_push_result !== Passenger::ONLINE_PUSH_RESULT_已推送
                || $this->online_check_status !== Passenger::ONLINE_CHECK_STATUS_有效
            ) {
                // 查看分派的人是否是销售
                if ($this->dealer_assign_to && \CorpUser::find($this->dealer_assign_to)->isRentalSales()) {
                    throw  new ErrorMessageException('电销结果不为有效已推送时，不能分配销售!');
                }
            } else {
                if ($this->dealer_assign_to && \CorpUser::find($this->dealer_assign_to)->isRentalOnlineSales()) {
                    throw  new ErrorMessageException('电销结果为有效已推送时，不能分配给出房电销');
                }
            }
        }

        if ($this->online_push_result === self::ONLINE_PUSH_RESULT_已推送) {
            $this->assign();
        }

        //  手机号变更时绑定用户
        if ($this->isDirty('mobile') && $u = User::whereMobile($this->mobile)->first()) {
            $this->bindUserId($u->id);
        }
        return parent::save($options);
    }

    // 分派操作
    private function assign()
    {
        if ($this->isUpdating()) {
            //  分派变更时检查权限
            if ($this->isDirty('dealer_assign_to')) {
                if (!$this->allow('assign-dealer')) {
                    throw new ErrorMessageException('只能分派自己的任务');
                }

                //  重新分派
                $this->load('dealer');

                $this->doAssignDealer($this->dealer_assign_to);

                $this->comment("重新分派 {$this->dealer->name}");
            }

            //  修改商圈
            if ($this->isDirty('block_id')) {
                $this->assignTeam();
            }
        }

        //  如果没有team就尝试更新
        if (!$this->team_id) {
            $this->assignTeam();
            // 分给雷雷: dealer和team都不对, team不对包括没有team和team没有leader
            if (!$this->dealer_assign_to && (!$this->team_id || !$this->team->leader_id)) {
                $this->doAssignDealer(CorpUser::whereCode(self::DEFAULT_DEALER_CODE)->first()->id);
            }
        }
        //  创建操作, creating会在 parent:save & saving 之后才执行, 所以要放在这里
        if ($this->isCreating()) {

            //  先尝试分给销售, 没得分时再分给 team
            $this->doAssignDealer($this->dealer_assign_to ?: \Acl\Role::find($this->team_id)->leader_id);

            self::created(function () {
                if ($this->dealer) {
                    $this->comment("发布信息. 分派给 {$this->dealer->name}");
                }
            });
        }
    }

    private function isCreating()
    {
        return !$this->id;
    }

    private function isUpdating()
    {
        return boolval($this->id);
    }

    public function bindUserId($userId)
    {
        $this->user_id = $userId;

        foreach ($this->daikans as $daikan) {
            if (!$daikan->user_id) {
                $daikan->user_id = $userId;
                $daikan->save();
            }
        }
    }

    public function assignTeam()
    {
        if (!$this->block_id) {
            return;
        }
        unset($this->block); // 重读block
        $roles = $this->block->role;
        $this->team_id = $roles->get(0)->id ?? null;
        $this->setRelation('team', $roles->get(0) ?? null);
    }

    public function notifyDealer()
    {
        $msg = "#{$this->id} {$this->name} {$this->mobile} \n";

        if ($count = Passenger::whereMobile($this->mobile)->count('id') > 1) {
            $msg .= "\n提示: 该客户已经被{$count}个销售在跟进。";
        }
        $this->load('dealer');
        if ($this->dealer) {
            \CorpNotice::sendNews(
                $this->dealer,
                '【新的带看任务】',
                $msg,
                $this->rapydLink()
            );
        }
    }

    public function getCustomersFromMobile()
    {
        return self::whereMobile($this->mobile)->whereAnswered()->get();
    }

    //  分派给销售
    public function doAssignDealer($id)
    {
        $this->updateStatus(self::STATUS_未联系);

        $this->dealer_assign_to = $id;
        $this->dealer_assign_at = Carbon::now();
        $this->dealer_assign_by = CorpAuth::user()->id;
        $this->recycle_at = null;
        $this->is_recycle = \Constant::IF_否;
    }

    /**
     * 批量分派给电销核验人
     * 细则：若已分派销售员，状态不做调整；若未分派销售员，核验状态置为待处理
     * @param $id
     */
    public function doAssignInspector($id)
    {
        if (empty($this->dealer_assign_to)) {
            $this->online_check_status = self::ONLINE_CHECK_STATUS_待处理;
            $this->online_push_result = self::ONLINE_PUSH_RESULT_未推送;
        }
        $this->online_check_by = $id;
    }

    /**
     * 带看
     * 细则：修改客户状态为已带看，并存日志
     * @param null $msg
     */
    public function doDaikan($msg = null)
    {
        $this->updateStatus(self::STATUS_已带看未签约);
        $this->comment("标记为【带看】{$msg}");
    }

    //  预订
    public function doBookingRoom(CustomerBooking $booking)
    {
        $this->updateStatus(self::STATUS_已预订);
        $this->comment("{$this->dealer->name} 预定房间 <{$booking->room->address_text}>");
    }

    /**
     * 放弃预订
     */
    public function doLostBookingRoom()
    {
        $this->doLost('预定失效');
    }

    /**
     * 签约
     * 细则：修改为'已签约'状态
     * @param WithCustomer $con
     */
    public function doSignRoom(WithCustomer $con)
    {
        $this->updateStatus(self::STATUS_已签约);
        $this->finish_at = Carbon::now();
        $this->comment("签约合同 <{$con->number}>");
    }

    /**
     * 标记失效
     * 细则：修改状态为失效，并存日志
     * @param null $msg
     */
    public function doLost($msg = null)
    {
        $this->status = self::STATUS_失效;
        $this->finish_at = Carbon::now();

        $this->comment("标记为【失效】{$msg}");
    }

    /**
     * 更新状态
     * 细则：状态更新，注意在此步没有做数据持久化
     * @param $status
     */
    public function updateStatus($status)
    {
        $level = [
            self::STATUS_未分派,
            self::STATUS_未联系,
            self::STATUS_已联系,
            self::STATUS_已预约,
            self::STATUS_已带看未签约,
            self::STATUS_已预订,
            self::STATUS_已签约,
        ];

        $level = array_combine(array_values($level), array_keys($level));

        if ($level[$status] > ($level[$this->status] ?? 0)) {
            $this->status = $status;
        }
    }

    /** 重新激活
     * @return Passenger
     */
    public function copy()
    {
        $new = new Passenger;
        foreach (['name', 'mobile', 'gender', 'job', 'note'] as $feild) {
            $new->$feild = $this->$feild;
        }
        $new->active_at = Carbon::now();
        $new->finish_at = null;

        return $new;
    }

    public function isActive()
    {
        return is_null($this->finish_at) && $this->status !== self::STATUS_失效;
    }

    public function scopeVisiable($query)
    {
        /* @var Passenger $query */
        return $query->whereRecycleAt(null)->where('status', '!=', self::STATUS_失效);
    }

    public function scopeWhereAnswered($query)
    {
        /* @var Passenger $query */
        return $query->where('name', '!=', Passenger::EFFECTIVE_容联匿名);
    }

    public function scopeWhereLeaderIs($query, $leaderId)
    {
        return $query->whereHas('dealer', function ($query) use ($leaderId) {
            /* @var CorpUser $query */
            return $query->departmentLeaderIs($leaderId);
        });
    }

    public function scopeOnlyId($query, $id)
    {
        return $id ? $query->where('passengers.id', '=', $id) : $query;
    }

    public function scopeOnlyCity($query, $city)
    {
        /* @var Passenger $query */
        return $city ? $query->whereHas('block.parent.parent', function ($query) use ($city) {
            /* @var Area $query */
            return $query->whereName($city);
        }) : $query;
    }

    //  监听任务延期
    private function listenDelay()
    {
    }

    // 电销推荐商圈
    public function block()
    {
        return $this->belongsTo(Area::class);
    }

    // 用户咨询商圈
    public function blocks()
    {
        return $this->belongsToMany(\Area\Block::class, 'passenger_blocks', 'passenger_id', 'block_id');
    }

    public function team()
    {
        return $this->belongsTo(\Acl\Role::class);
    }

    public function user()
    {
        return $this->belongsTo(\User::class);
    }

    public function recorder()
    {
        if ($this->record_by_cooperator_id) {   //  渠道
            return $this->cooperator();
        }
        return $this->belongsTo(CorpUser::class, 'record_by_corp_id');//BD
    }

    //  @todo 一个 passenger 签了多个合同的情况
    public function daikan()
    {
        $relation = $this->hasOne(DaikanHistory::class, 'passenger_id');
        return $relation->whereHas('contract', function ($query) {
            return $query->whereRentApproveStatus(WithCustomer::APPROVE_STATUS_合格);
        });
    }

    public function daikanSuite($suiteId)
    {
        $daikan = new DaikanHistory();
        $daikan->status = \Passenger::STATUS_已带看未签约;
        $daikan->passenger_id = $this->id;
        $daikan->suite_id = $suiteId;
        $daikan->user_id = $this->user_id;
        $daikan->dealer_id = $this->dealer_assign_to;
        $daikan->daikan_at = Carbon::now();
        $daikan->saveOrError();

        $this->updateStatus(\Passenger::STATUS_已带看未签约);
        $this->saveOrError();
    }

    public function cooperator()
    {
        return $this->belongsTo(Cooperator::class, 'record_by_cooperator_id');
    }

    public function bookings()
    {
        return $this->hasMany(\Contract\CustomerBooking::class);
    }

    public function assigner()
    {
        return $this->belongsTo(CorpUser::class, 'dealer_assign_by');
    }

    public function onliner()
    {
        return $this->belongsTo(CorpUser::class, 'online_check_by');
    }

    public function dealer()
    {
        return $this->belongsTo(CorpUser::class, 'dealer_assign_to');
    }

    public function daikans()
    {
        return $this->hasMany(DaikanHistory::class);
    }

    //  关联带看记录的公寓信息
    public function suites()
    {
        return $this->belongsToMany(Suite::class, 'daikan_histories', 'passenger_id', 'suite_id');
    }

    public function enterprise_coupon()
    {
        return $this->belongsTo(\Marketing\EnterpriseCoupon::class);
    }

    public function internalLink()
    {
        return '';//action('Admin\Tracking\PassengerController@anyDetail', $this->id);
    }

    public function rapydLink($action = 'modify')
    {
        return '';//action('Admin\Tracking\PassengerController@anyItem') . "?{$action}={$this->id}";
    }

    public static function searchLink($field, $val)
    {
        return '';//link_to(action('Admin\Tracking\PassengerController@getList') . "?search=1&{$field}={$val}", $val);
    }

    public function getSavingMessage()
    {
        $error = $this->getSavingErrors()->first();
        foreach ([
                     'name' => '姓名',
                     'mobile' => '手机号码',
                     'source' => '客户来源',
                 ] as $field => $label) {
            $error = str_replace($field, $label, $error);
        }
        return $error;
    }

    public static function listClaimedType()
    {
        return [
            self::TYPE_官网,
            self::TYPE_在线团队,
            self::TYPE_市场部,
            self::TYPE_批量导入,
            self::TYPE_老推新,
            self::TYPE_市场活动,
            self::TYPE_企业活动,
        ];
    }

    public function claimMissedCalls()
    {
        // 更新一个所有认领取一个传过去
        $claimUp = $this->where('mobile', $this->mobile);
        $claimUp->update(['missed_calls_claim_status' => Passenger::MISSED_CALLS_STATUS_认领]);
        // 带过去一个认领
        $claimUpOne = $this->findOrError($this->id);
        $claimUpOne->dealer_assign_to = \CorpAuth::id();
        $claimUpOne->dealer_assign_at = Carbon::now();
        $claimUpOne->dealer_assign_by = \CorpAuth::id();
        $claimUpOne->record_by_corp_id = \CorpAuth::id();
        $claimUpOne->name = Passenger::EFFECTIVE_容联认领;
        $claimUpOne->status = Passenger::STATUS_未联系;
        if (!$claimUpOne->save()) {
            \Log::info(__METHOD__, [$this->mobile, $claimUpOne->getMessage() . $claimUpOne->getMessage()]);
        }

    }

}

<?php namespace Tracking;

abstract class BaseKanfangHistory extends \BaseModel
{
    use \Traits\ModelAllowTrait;

    const DAIKAN_TYPE_带看 = '带看';
    const DAIKAN_TYPE_空看 = '空看';

    protected function processAllow($action)
    {
        switch ($action) {
            case 'owner' :
                return $this->dealer_id === \CorpAuth::id() || $this->allow('leader');
            case 'leader' :
                return \CorpUser::whereId($this->dealer_id)->departmentLeaderIs(\CorpAuth::id())->exists();
            case 'manger' :
                return can('查看出房带看记录');
            case 'daikan_history_editable' :
                return $this->allow('owner') || $this->allow('manger');
        }
    }

    /**
     * @param $id
     * @return static
     * @throws \ErrorMessageException
     */
    public static function findIfAllow($id)
    {
        $daikanHistories = self::findOrError($id);
        if (!$daikanHistories->allow('daikan_history_editable')) {
            throw new \ErrorMessageException('您无权访问该页面');
        }
        return $daikanHistories;
    }

    /**
     * $staff有权查看
     * @param \CorpUser|null $staff
     */
    public function scopeAllAllow($query, \CorpUser $staff = null)
    {
        $staff = $staff ?? \CorpAuth::user();

        return can('查看出房带看记录')
            ? $query
            : $query->where(function ($query) use ($staff) {
                $query->whereDealerId($staff->id)
                    ->orWhereHas('dealer', function ($query) use ($staff) {
                        return $query->departmentLeaderIs($staff->id);
                    });
            });
    }

}

<?php namespace Forecast\BuildingDict;

class BaseDict extends \BaseModel
{
    //未被禁或是否只读
    const YES = 0;
    const NO = 1;

    /**
     * 是否可以修改楼盘字典
     * @return bool
     */
    public static function canModifyDict()
    {
        return can("楼盘字典_修改楼盘信息") || \CorpUser::isLandlordTeam();
    }
}

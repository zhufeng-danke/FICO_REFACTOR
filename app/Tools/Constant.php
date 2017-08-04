<?php // zhangwei@wutongwan.org

// 常用常量
class Constant
{
    const IF_是 = '是';
    const IF_否 = '否';

    /**
     * 是和否
     * @return array
     */
    public static function listIf()
    {
        return [
            self::IF_是,
            self::IF_否,
        ];
    }

    const WHETHER_有 = '有';
    const WHETHER_无 = '无';

    /**
     * 有和无
     * @return array
     */
    public static function listWhether()
    {
        return [
            self::WHETHER_有,
            self::WHETHER_无,
        ];
    }

    const GENDER_男 = '男';
    const GENDER_女 = '女';

    public static function listGender()
    {
        return [
            self::GENDER_男,
            self::GENDER_女,
        ];
    }

}
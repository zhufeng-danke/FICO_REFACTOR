<?php
// zhangwei@wutongwan.org
// 云通讯接口

use App\Jobs\Queueable\YunTXQueueJob;

/**
 * Class YunTX
 *
 * 代码接口类,所有信息的发送都不是实时处理,而是进入后端队列中处理.
 */
class YunTX
{
    const CODE_SUCCESS = '000000';       // 成功
    const CODE_TRIES_LIMIT = '160021';   // 次数限制，一个号码，每天最多发10条短信，相同短信只能发1条
    const CODE_NETWORK_ERROR = '172001'; // 网络异常

    //需要在云通讯后台预先定义好
    const TEMPLATE_验证码短信 = '43630';

    /**
     * 发送模板短信
     * @param string $to 目标手机号码,多个用英文逗号分隔
     * @param int $templateId 模板Id
     * @param array $dataList 内容数据 格式为数组 例如：['Marry','Alon']
     *
     * @return bool @预留,以后加黑名单等前置检查,可能直接返回false
     */
    public function sendTemplateSMS($to, $templateId, Array $dataList = [])
    {
        $args = [
            'to' => $to,
            'dataList' => $dataList,
            'createdAt' => time(),
            'templateId' => $templateId,
        ];

        dispatch(new YunTXQueueJob(YunTXQueueJob::FUNC_模板短信, $args));

        Log::info("[YunTX]Send SMS to {$to}", $args);

        return true;
    }


    /**
     * 发送模板短信
     * @param string $to 目标手机号码,多个用英文逗号分隔
     * @param int $code 验证码 4-8位数字
     *
     * @return bool @预留,以后加黑名单等前置检查,可能直接返回false
     */
    public function sendVoiceVerify($to, $code)
    {
        $args = [
            'to' => $to,
            'code' => $code,
            'createdAt' => time(),
        ];

        dispatch(new YunTXQueueJob(YunTXQueueJob::FUNC_语音验证码, $args));

        Log::info("[YunTX]Send Voice Verify to {$to}, code {$code}");

        return true;
    }
}
<?php // zhangwei@wutongwan.org

use Illuminate\Mail\Message;
use Maatwebsite\Excel\Writers\LaravelExcelWriter;

class Email
{
    /**
     * 异步发邮件，队列操作，不阻塞
     *
     * @param string|array $to   发多个收件人时使用英文逗号隔开
     * @param string $subject    主题
     * @param string $content    内容
     * @param bool|false $isHtml 是否HTML格式，默认不是
     * @return mixed
     */

    public static function send($to, string $subject, string $content, bool $isHtml = false)
    {
        if (self::rateLimit($to, $subject)) {
            Log::info(__METHOD__ . " Dropped {$subject}", ['to' => $to, 'content' => $content]);
            return false;
        }

        if (is_string($to)) {
            $to = explode(',', trim($to, ", \t\n\r\0\x0B"));
        }

        $content = strval($content);

        Log::info("Email subject {$subject}", ['to' => $to, 'content' => $content]);

        return Mail::queue('emails.blank', ['msg' => $content, 'isHtml' => $isHtml],
            function (Message $message) use ($to, $subject) {
                $message->to($to)->subject($subject);
            }
        );
    }

    /**
     * @param string|array $to 收件人Email
     * @param string $subject  标题
     * @param array $context   附加信息
     * @return bool
     */
    public static function debugEmail($to, $subject, array $context = [])
    {
        if (self::rateLimit($to, $subject)) {
            Log::info(__METHOD__ . " Dropped {$subject}", ['to' => $to, '$context' => $context]);
            return false;
        }

        $context['_ua_'] = \UserAgent::ua();
        $context['_ips_'] = \Request::ips();
        $context['_url_'] = \Request::fullUrl();
        $context['_session_id_'] = \Session::getId();
        $context['_session_'] = \Session::all();
        $context['_post_string_'] = isset($GLOBALS["HTTP_RAW_POST_DATA"]) ? $GLOBALS["HTTP_RAW_POST_DATA"] : file_get_contents("php://input");

        return \Mail::queue(
            'emails.debug',
            ['subject' => $subject, 'arr' => $context],
            function (\Illuminate\Mail\Message $message) use ($to, $subject) {
                $message->to($to)->subject($subject);
            }
        );
    }

    private static function rateLimit($to, $subject)
    {
        $key = md5(json_encode([$to, $subject]));
        $f = new \Firewall(__METHOD__ . $key, 120, 2);

        return $f->hit();
    }

    /**
     * 将array写入excel并通过邮件发送
     * notes:
     *        array中指定key,以作为列名
     * @param $to              string|array 收件人
     * @param string $filename 文件名&邮件标题,不需要后缀名
     * @param array $rows
     */
    public static function sendDataByExcel($to, string $filename, array $rows)
    {
        if (count($rows) === 0) {
            self::send($to, $filename, '无数据');

            return;
        }

        if (is_string($to)) {
            $to = explode(',', trim($to, ", \t\n\r\0\x0B"));
        }

        $excel = Excel::create($filename,
            function (LaravelExcelWriter $excel) use ($rows) {
                $excel->sheet('SheetName',
                    function (\PHPExcel_Worksheet $sheet) use ($rows) {
                        $arrays = [];
                        foreach ($rows as $row) {
                            $arrays [] = (array)$row;
                        }

                        $sheet->fromArray($arrays);
                    }
                );
            }
        );

        Mail::send('emails.blank', ['msg' => '', 'isHtml' => false],
            function (Message $message) use ($excel, $to) {
                $message->to($to)->subject($excel->filename)
                    ->attachData($excel->string(), $excel->filename . '.' . $excel->ext);
            }
        );
    }
}

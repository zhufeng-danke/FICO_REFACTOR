<?php
//yubing@wutongwan.org

/**
 * 记录各种登录，退出等的安全日志。
 */
class AuditLog
{

    const ACTION_LOGIN = 'login';
    const ACTION_LOGOUT = 'logout';
    const ACTION_REGISTER = 'register';

    const ACTION_UPDATE = 'update';
    const ACTION_LOGIN_FAIL = 'login_fail';

    const INFO = 'info';

    const TYPE_USER = 'normal_user';
    const TYPE_CORP = 'corp_user';
    const TYPE_VENDOR = 'vendor_user';

    public static function log($action, $type, $user_id, $context = [])
    {
        $msg = __CLASS__ . " $type::$action $user_id " . \Request::ip();
        $context['_ua_'] = \UserAgent::ua();
        $context['_ips_'] = \Request::ips();
        $context['_method_'] = \Request::method();
        $context['_url_'] = \Request::fullUrl();
        $context['_session_'] = \Session::getId();

        Log::info($msg, $context);
    }
}

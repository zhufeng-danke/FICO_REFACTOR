<?php namespace App\Http\Controllers\Api;

use App\Models\CorpUser;
Use Illuminate\Encryption\Encrypter;
use Illuminate\Support\Facades\Session;
use Psy\Exception\ErrorException;

class JumpController extends BaseApiController
{

    public function getIndex()
    {
        //业务同步登陆
        $url = Request()->input('url');
        $token = Request()->input("token");
        $previous_url = url()->previous();

        if (!empty($token) && stripos($previous_url, env('LAPUTA_API_URL')) !== false) {
            $Encrypter = new Encrypter(env('LAPUTA_API_KEY'), 'AES-256-CBC');

            $token = $Encrypter->decrypt($token);
            list($session_id, $dateTime) = explode('|', $token);

            if (time() - $dateTime <= 180) {

                Session::setId($session_id);
                Session::start();

                $uid = Session::get("login_corp_user");

                Session::put('corp_user', CorpUser::find($uid));
                Session::put('login_corp_user', $uid);

                return redirect($url);

            } else {
                abort('404', '登陆超时');
            }
        } else {
            abort('404', '登陆失败');
        }

    }
}

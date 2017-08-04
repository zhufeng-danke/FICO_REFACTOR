<?php
// zhangwei@wutongwan.org
// 验证相关：短信...

class MobileVerify
{
    private $error; // 错误信息，可直接显示给用户的信息

    const CH_短信 = 'sms';
    const CH_语音 = 'voice';

    private function setError($err)
    {
        $this->error = $err;
    }

    public function getError()
    {
        return $this->error;
    }

    private function getVerifyCodeCacheKey($mobile)
    {
        return "MobileVerify:Code:Mobile:{$mobile}";
    }

    /**
     * 通过"短信"或"语音"发送验证码
     * @param $mobile
     * @param string $ch         发送渠道 [短信 or 语音]
     * @param int $expireMinutes 有效时间
     * @return bool
     */
    public function sendCode($mobile, $ch = self::CH_短信, $expireMinutes = 10)
    {
        if (!preg_match('/^1[3-9]\d{9}$/', $mobile)) {
            $this->setError('手机号码格式错误');

            return false;
        }

        if ((new Firewall(__METHOD__ . date('Y-m-d') . $mobile, 86400 * 7, 10))->hit()) {
            $this->setError('此手机号验证码下发次数超过当日限制');

            return false;
        }

        //每次发送后,老的验证码自动失效
        $key = $this->getVerifyCodeCacheKey($mobile);
        $code = mt_rand(1000, 9999);
        Cache::put($key, $code, $expireMinutes);

        if (isProduction()) {
            switch ($ch) {
                default:
                case self::CH_短信:
                    (new YunTX())->sendTemplateSms(
                        $mobile,
                        YunTX::TEMPLATE_验证码短信,
                        [$code, $expireMinutes]
                    );
                    break;
                case self::CH_语音:
                    (new YunTX())->sendVoiceVerify(
                        $mobile,
                        $code
                    );
                    break;
            }
        } else {
            \Debugbar::addMessage("[To:{$mobile}] by [{$ch}], verify code: $code", 'MobileVerifyCode');
        }

        return true;
    }

    /**
     * 快捷方法, 自动选择短信 or 语音
     * @param $mobile
     * @return array
     */
    public static function autoSend($mobile)
    {
        $static = new static();

        $channel = self::CH_短信;
        $message = '';

        if ((new \Firewall(__METHOD__ . '120' . $mobile, 120, 1))->hit()) {
            $channel = self::CH_语音; //短时间内,第一次短信没收到的话,第二次用语音.
            $message = "本次验证码将通过 『电话语音』 发给您,请注意接听!";
        }

        if ($static->sendCode($mobile, $channel)) {
            $success = true;
        } else {
            $message = $static->getError();
        }

        return compact('success', 'message');
    }

    /**
     * 验证验证码是否正确
     * @param $mobile
     * @param $code
     * @return bool
     */
    public function verifyCode($mobile, $code)
    {
        if (!($mobile && $code)) {
            $this->setError('信息不完整');
        }

        if ((new Firewall(__METHOD__ . "mobile:{$mobile}", 86400, 30))->hit()) {
            $this->setError('验证失败，今天验证次数过多！');
        }

        if ($user = Auth::user()) {
            if ((new Firewall(__METHOD__ . "user:{$user->id}", 86400, 30))->hit()) {
                $this->setError('验证失败，您今天验证次数过多！');
            }
        }

        $cacheCode = Cache::get($this->getVerifyCodeCacheKey($mobile));
        if (!$cacheCode) {
            $this->setError('验证码已过期');
        }

        Cache::forget($this->getVerifyCodeCacheKey($mobile));//验证通过后 将此验证码失效

        if (intval($code) !== intval($cacheCode)){
            $this->setError('验证码不正确');
        }

        return !$this->getError();
    }
}

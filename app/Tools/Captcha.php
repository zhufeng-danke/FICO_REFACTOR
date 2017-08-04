<?php

//验证码类
class Captcha
{
    const SESSION_KEY = 'vcode.captcha';

    protected $code;

    public function __construct()
    {
        $this->code = generateNumberCode(4);
        Session::put(self::SESSION_KEY, $this->code);
    }

    public static function instance()
    {
        return new static();
    }

    public static function verify($code)
    {
        return $code && $code === Session::get(self::SESSION_KEY);
    }

    public function src($width = 60, $height = 25)
    {
        $code = $this->code;

        $im = imagecreate($width, $height);
        imagecolorallocate($im, 0xFF, 0xFF, 0xFF);    //背景色
        $font = imagecolorallocate($im, 41, 163, 238);        //字体色

        imagestring($im, 5, 7, 5, $code, $font);//绘制随机生成的字符串
        imagerectangle($im, 0, 0, $width - 1, $height - 1, $font);//在验证码图像周围绘制1px的边框

        ob_start();
        imagepng($im);
        $data = ob_get_contents();
        ob_end_clean();
        imagedestroy($im);//将图片handle解构，释于内存空间

        // Note. 这里只是个半成品, 为了应付短信渠道验证用的, 所以没有真正保存 $code

        return "data:image/png;base64," . base64_encode($data);
    }

    /**
     * 先放这, 将来支持 点击刷新啥的
     * @return string
     */
    public function render($attr = [])
    {
        return Html::image($this->src(), '验证码', $attr);
    }
}

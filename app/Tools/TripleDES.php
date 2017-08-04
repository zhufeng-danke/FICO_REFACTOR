<?php

/**
 * 3DES 加解密工具类
 *
 * 注意：secret 推荐为 24 位
 *  - 如不足 24 位，会使用空字符补齐
 *  - 超过 24 位, 直接报错
 *
 * Usage:
 *  $des = new TripleDES('ni-hao-wa');
 *  $des->encrypt('content');
 *  $des->decrypt('data');
 *
 */
class TripleDES
{
    private $secret;

    public function __construct($secret = null)
    {
        $this->setSecret($secret);
    }

    public function setSecret($secret, $pad = "\x00")
    {
        // enforce secret size
        assert(strlen($secret) <= 24, 'secret size should equals 24');
        while (strlen($secret) < 24) {
            $secret .= $pad;
        }

        $this->secret = $secret;
    }

    public function encrypt(string $data)
    {
        return mcrypt_encrypt(MCRYPT_3DES, $this->secret(), $data, "ecb");
    }

    public function decrypt(string $data)
    {
        return rtrim(mcrypt_decrypt(MCRYPT_3DES, $this->secret(), $data, "ecb"), "\x00");
    }

    private function secret()
    {
        if (!$this->secret) {
            throw new Exception('`secret` not set');
        }

        return $this->secret;
    }
}
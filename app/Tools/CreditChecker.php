<?php
//yubing@wutongwan.org

/**
 * 用户征信相关功能
 *
 * 现在使用的主要是 "凭安征信" 提供的服务.
 *
 * 完整的API文档: https://github.com/wutongwan/Laputa/files/218344/default.pdf
 */
class CreditChecker
{
    private function request($api, $post_data)
    {
        $url = 'https://i.trustutn.org/' . $api;
        $c = new HttpClient();
        $pkey = config('services.ping-an-sec.pkey');
        $ptime = microtime(true);
        $vkey = md5($pkey . '_' . $ptime . '_' . $pkey);

        $post_data['pname'] = config('services.ping-an-sec.pname');
        $post_data['ptime'] = $ptime;
        $post_data['vkey'] = $vkey;

        $rtn = json_decode($c->post($url, $post_data), true);

        //dump($url,$post_data);
        if (is_array($rtn)) {
            $whiteList = [
                0,  //  请求成功    p_1.4
                2,  //  暂无数据
                12, //  实名信息不一致. 仅身份信息验证时存在. p_4.2.3
            ];
            if (isset($rtn['result']) && !in_array($rtn['result'], $whiteList)) {
                throw new \Exception("接口请求失败: {$rtn['message']} @ $url");
            } else {
                return $rtn;
            }
        } else {
            throw new \Exception("接口请求失败,请稍后重试: $url");
        }
    }

    //根据征信条例,需要将用户授权文件上传备查,这里先简单点绕过了,具体要查有实体文件留档就好了
    private function addAuthFile($name, $id_num)
    {
        //比较特别的写法,避免每次在线上建个临时文件,还得清理.
        $filename = md5($name . $id_num) . '.txt';
        $key = "authFile\"; filename=\"$filename\r\nContent-Type: text/plain\r\n";
        $content = "Name:$name\nNum:$id_num\nTime:" . date('c');

        return $this->request('authfile', [
            'name' => $name,
            'idCard' => $id_num,
            $key => $content,
            'idType' => '111'
        ]);
    }

    //[免费查询] 根据国标,简单校验身份证号码是否正确
    public function isValidIdNum($id_num)
    {
        return (new IdCardInfo($id_num))->isValid();
    }

    //[付费查询] 查询身份证号码和姓名是否和公安数据库一致
    public function isTrueId($name, $id_num)
    {
        if (!$this->isValidIdNum($id_num)) {
            return false;
        }

        $h = new \Log\CreditCheckHistory();
        $h->action = $h::ACTION_身份证号码核对;
        $h->id_name = $name;
        $h->id_number = $id_num;
        $ms = $h->getMatched();
        if ($ms->count() > 0) {
            $result = $ms->first()->result;
        } else {
            $this->addAuthFile($name, $id_num);
            $info = $this->request('idCheck', ['name' => $name, 'idCard' => $id_num, 'licenseType' => '0']);
            $h->result = $result = $info['data']['status'];
            $h->vendor = $h::VENDOR_凭安征信;
            $h->save();
        }

        return $result == '身份证信息与姓名一致'; // 对方的API写的实在比较傻!!!
    }

    //[免费查询] 查询电话号码是否被用户在360手机助手上做过标记.
    public function getPhoneTags($phone_num)
    {
        if (!isProduction()) {
            return [];
        }

        try {
            $info = $this->request('phonetag', ['phone' => preg_replace("/[^0-9]/", '', $phone_num)]);
        } catch (\Exception $e){
            return [];
        }
        $tags = [];
        foreach ($info['data'] ?? [] as $row) {
            $tags[] = $row['tag'];
        }

        return $tags;
    }

}

<?php
/**
 * 七牛相关操作
 * User: zhufeng@dankegongyu.com
 * Date: 17/7/29
 * Time: 上午10:39
 */

namespace App\Http\Controllers\Admin\Traits;


use Qiniu\Auth;
use Qiniu\Storage\BucketManager;
use Qiniu\Storage\UploadManager;

trait QnSdk
{
    /**
     * 配置信息
     * @return array
     */
    public function qnConfigs()
    {
        return [
            'accessKey' => config('services.qiniu.key.access'),
            'secretKey' => config('services.qiniu.key.secret'),
            'privateBucketName' => config('services.qiniu.bucket.private.name'),
            'privateBucketDomain' => config('services.qiniu.bucket.private.domain'),
            'publicBucketName' => config('services.qiniu.bucket.public.name'),
            'publicBucketDomain' => config('services.qiniu.bucket.public.domain'),
        ];
    }

    /**
     * 初始化
     * @return Auth
     */
    public function qnInit()
    {
        $qnConfigs = $this->qnConfigs();

        // 构建鉴权对象
        $accessKey = $qnConfigs['accessKey'];// 'PRek4t4j1QpBciNsrQ7L6lBPVZxcljjyjvM3jhw4'; //$qnConfigs['accessKey'];
        $secretKey = $qnConfigs['secretKey'];// 'simUVk7vrICIwF0C4P3TPP-0scCRuR-x1Rjr2XWz'; //$qnConfigs['secretKey'];
        return new Auth($accessKey, $secretKey);
    }

    /**
     * 上传文件
     * @param        $filePath
     * @param string $prefix
     * @param string $mode
     * @return array
     */
    public function qnUpLoadFile($filePath, $prefix = 'file-', $mode = 'public')
    {
        $auth = $this->qnInit();

        $qnConfigs = $this->qnConfigs();

        // 要上传的空间
        if($mode == 'public'){
            $bucket = $qnConfigs['publicBucketName'];
        } else {
            $bucket = $qnConfigs['privateBucketName'];
        }

        // 生成上传 Token
        $token = $auth->uploadToken($bucket);

        // 上传到七牛后保存的文件名
        $key = uniqid($prefix);

        // 初始化 UploadManager 对象并进行文件的上传
        $uploadMgr = new UploadManager();

        // 调用 UploadManager 的 putFile 方法进行文件的上传
        list($ret, $err) = $uploadMgr->putFile($token, $key, $filePath); // $filePath 要上传文件的本地路径

        return compact('ret', 'err', 'key');
    }

    /**
     * 删除文件
     * @param        $key
     * @param string $mode
     * @return bool
     */
    public function qnDeleteFile($key, $mode = 'public')
    {
        $auth = $this->qnInit();

        $qnConfigs = $this->qnConfigs();

        // 要上传的空间
        if($mode == 'public'){
            $bucket = $qnConfigs['publicBucketName'];
        } else {
            $bucket = $qnConfigs['privateBucketName'];
        }

        //初始化BucketManager
        $bucketMgr = new BucketManager($auth);

        //删除$bucket 中的文件 $key
        $err = $bucketMgr->delete($bucket, $key);

        if ($err !== null) {
            return false;
        }

        return true;
    }

    /**
     * 文件下载地址
     * @param        $key
     * @param string $mode
     * @return string
     */
    public function qnDownLoadFile($key, $mode = 'public')
    {
        $auth = $this->qnInit();
        $qnConfigs = $this->qnConfigs();

        if ($mode == 'public') {
            $authUrl = $qnConfigs['publicBucketDomain'] . '/' . $key;
        } else {
            //baseUrl:构造成私有空间的域名/key的形式
            $baseUrl = $qnConfigs['privateBucketDomain'] . '/' . $key;
            $authUrl = $auth->privateDownloadUrl($baseUrl);
        }

        return $authUrl;
    }


}
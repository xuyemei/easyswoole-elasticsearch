<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/11/26 0026
 * Time: 下午 9:29
 */
namespace App\Lib\AliyunSdk;

require_once EASYSWOOLE_ROOT.'/App/Lib/AliyunSdk/aliyun-php-sdk-core/Config.php';

if (is_file(EASYSWOOLE_ROOT.'/App/Lib/AliyunSdk/aliyun-oss-php-sdk-master/autoload.php')) {
    require_once EASYSWOOLE_ROOT.'/App/Lib/AliyunSdk/aliyun-oss-php-sdk-master/autoload.php';
}
if (is_file(EASYSWOOLE_ROOT.'/App/Lib/AliyunSdk/aliyun-oss-php-sdk-master/vendor/autoload.php')) {
    require_once EASYSWOOLE_ROOT.'/App/Lib/AliyunSdk/aliyun-oss-php-sdk-master/vendor/autoload.php';
}

use OSS\OssClient;
use OSS\Core\OssException;
use vod\Request\V20170321 as vod;
class AliVod{


    public $regionId = 'cn-shanghai';  // 点播服务接入区域
    public $accessKeyId;
    public $accessKeySecret;
    public $client;
    public $ossClient;
    public function __construct()
    {
        $this->accessKeyId = \Yaconf::get('Aliyun.accessKeyId');
        $this->accessKeySecret = \Yaconf::get('Aliyun.accessKeySecret');

        $this->client =  $this->initVodClient();

    }

    /**
     * 初始化客户端
     */
    public function initVodClient()
    {
        $profile = \DefaultProfile::getProfile($this->regionId, $this->accessKeyId, $this->accessKeySecret);
        return new \DefaultAcsClient($profile);
    }


    /**
     * 获取视频上传地址和凭证
     * @param $client
     * @return mixed
     */
    public function createUploadVideo($title,$videoFileName,$other=[]) {
        $request = new vod\CreateUploadVideoRequest();
        $request->setTitle($title);
        $request->setFileName($videoFileName);//源文件名称
        if(!empty($other['description'])){
            $request->setDescription($other['description']);
        }

        if(!empty($other['coverURL'])){
            $request->setCoverURL($other['coverURL']);
        }

        if(!empty($other['tags'])){
            $request->setTags($other['tags']);
        }
        if(!empty($other['AcceptFormat'])){
            $request->setAcceptFormat($other['AcceptFormat']);
        }else{
            $request->setAcceptFormat('JSON');
        }
        $result = $this->client->getAcsResponse($request);
        if(empty($result) || empty($result->VideoId)){
            throw new \Exception('获取上传凭证失败');
        }

        return $result;
    }

    /**
     * 初始化oss客户端
     * @param $uploadAuth
     * @param $uploadAddress
     */
    public function initOssClient($uploadAuth,$uploadAddress){
        $endpoint = "http://oss-cn-hangzhou.aliyuncs.com";
        $this->ossClient = new OssClient($uploadAuth['AccessKeyId'], $uploadAuth['AccessKeySecret'],$uploadAddress['Endpoint'],false,
            $uploadAuth['SecurityToken']);
        $this->ossClient->setTimeout(86400*7);
        $this->ossClient->setConnectTimeout(10);
    }

    /**
     * 上传本地文件
     * @param $uploadAddress
     * @param $object
     * @param $filePath
     * @return mixed
     */
    public function uploadLocalFile($uploadAddress,$filePath){

        $result = $this->ossClient->uploadFile($uploadAddress['Bucket'], $uploadAddress['FileName'], $filePath);
        return $result;
    }

    public function getPlayInfo($videoId){
        if(empty($videoId)){
            return [];
        }


        $request = new vod\GetPlayInfoRequest();
        $request->setVideoId($videoId);
        $request->setAuthTimeout(3600*24);
        $request->setAcceptFormat('JSON');
        return $this->client->getAcsResponse($request);
    }



}


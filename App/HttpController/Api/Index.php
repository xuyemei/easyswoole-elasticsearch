<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/11/23 0023
 * Time: 下午 9:35
 */

namespace App\HttpController\Api;


use App\HttpController\Api\Base;
use App\Lib\AliyunSdk\AliVod;
use App\Lib\Redis\Redis;
use EasySwoole\Core\Component\Cache\Cache;
use EasySwoole\Core\Component\Di;
use App\Model\Video as VideoModel;
use EasySwoole\Core\Component\Logger;
use EasySwoole\Core\Http\Message\Status;

use \App\Lib\Cache\Video as VideoCache;
use EasySwoole\Core\Swoole\Task\TaskManager;

class Index extends Base
{

    /**
     * 视频播放数据
     *
     */
    function index()
    {
        // TODO: Implement index() method.

        $videoId = $this->request()->getRequestParam('videoId');
        $videoId = intval($videoId);
        $videoModel = new VideoModel();
        try{
            $video = $videoModel->getDataById($videoId);
        }catch (\Exception $e){
            return $this->writeJson(Status::CODE_INTERNAL_SERVER_ERROR,'请求失败');
        }

        if(empty($video) || $video['status'] != \Yaconf::get('status.normal')){
            return $this->writeJson(Status::CODE_BAD_REQUEST,'内容不存在');
        }
        if(!empty($video)){
            $video['video_duration'] = gmstrftime("%H:%M:%S",$video['video_duration']);
        }

//        test
//        $num = Di::getInstance()->get('REDIS')->zscore(\Yaconf::get('redis.video_play_num'),$videoId);
//        print_r($num);

//        利用task的异步任务+redis 记录播放数
        TaskManager::async(function () use($videoId){
            //TO DO
            Di::getInstance()->get('REDIS')->zincrby(\Yaconf::get('redis.video_play_num'),1,$videoId);
        });

        return $this->writeJson(Status::CODE_OK,'ok',$video);
    }

    /**
     * 播放排行榜 日排，周排，月排
     */
    public function rank(){

        $res = Di::getInstance()->get('REDIS')->zrevrange(\Yaconf::get('redis.video_play_num'),0,-1,'withscores');
        return $this->writeJson(Status::CODE_OK,'ok',$res);

    }

    /**
     * 通过mysql获取list数据
     * @return bool
     */
    public function listsMysql(){

        $condition = [];
        if(!empty($this->params['cat_id'])){
            $condition['cat_id'] = $this->params['cat_id'];
        }
        //……
        try{
            $video = new VideoModel();
            $data = $video->getVideoList($condition,$this->params['page'],$this->params['size']);
        }catch (\Exception $e){
            Logger::getInstance()->log($e->getMessage(),'mysql');
            $this->writeJson(Status::CODE_BAD_REQUEST,$e->getMessage());
        }

        if(!empty($data['lists'])){
            foreach($data['lists'] as &$list){
                $list['create_time'] = date('Y-m-d H:m:s',$list['create_time']);
                $list['video_duration'] = gmstrftime('%H:%M:%S',$list['video_duration']);
            }
        }

        return $this->writeJson(Status::CODE_OK,'ok',$data);

    }

    /**
     * 通过API静态化获取list数据
     * @return bool
     */
    public function listsApi(){
        $catId = !empty($this->params['cat_id']) ? intval($this->params['cat_id']) : 0;
        try{
            $data = (new VideoCache())->getIndexVideo($catId);
        }catch (\Exception $e){
            return $this->writeJson(Status::CODE_BAD_REQUEST,'请求失败',[]);

        }

        $total = count($data);
        return $this->writeJson(Status::CODE_OK,'ok',$this->getPaginateDatas($total,$data));
    }

    public function getRedis(){
//        $redis = Redis::getInstance();
//        $val = $redis->get('xuyemei');
        $val = Di::getInstance()->get('REDIS')->set('xuyemei',time());
        $val = Di::getInstance()->get('REDIS')->get('xuyemei');
        $this->writeJson(200,'ok',$val);
    }

    public function yaconf(){
        $config = \Yaconf::get('redis');
        return $this->writeJson(200,'ok',$config);
    }

    public function testAli(){
        $obj = new AliVod();
        $title = 'test-video';
        $result = $obj->createUploadVideo($title,'videoFile.mov');

        $uploadAddress = json_decode(base64_decode($result->UploadAddress),true);
        $uploadAuth = json_decode(base64_decode($result->UploadAuth),true);

        $obj->initOssClient($uploadAuth,$uploadAddress);
        $filePath = EASYSWOOLE_ROOT.'/webroot/1.mp4';
        $result = $obj->uploadLocalFile($uploadAddress,$filePath);
    }

    public function getVideo(){
        $videoId = '39cb830f4b294aa7988097397aaf6389';
        $obj = new AliVod();

        try {
            $playInfo = $obj->getPlayInfo($videoId);
        } catch (\Exception $e) {
            print $e->getMessage()."\n";
        }
    }


    /**
     * 发布消息到redis队列
     */
    public function pub(){
        $params = $this->request()->getRequestParam();
        Di::getInstance()->get('REDIS')->rpush('task_list',$params['list_key']);
    }
}
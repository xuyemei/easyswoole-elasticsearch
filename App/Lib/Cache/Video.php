<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/11/27 0027
 * Time: 下午 10:29
 */
namespace App\Lib\Cache;

use App\Model\Video as VideoModel;
use EasySwoole\Core\Component\Cache\Cache;
use EasySwoole\Core\Component\Di;
use EasySwoole\Core\Component\Logger;

class Video{

    public function setIndexVideo(){
        $catIds = array_keys(\Yaconf::get('category.cats'));
        array_unshift($catIds,0);
        $videoModel = new VideoModel();
        foreach ($catIds as $catId){

            $condition = [];
            if(!empty($catId)){
                $condition['cat_id'] = $catId;
            }
            try{
                $data = $videoModel->getVideoCacheList($condition);
            }catch (\Exception $e){
                //报警：短信，邮件
                Logger::getInstance()->log($e->getMessage());
            }

            if(empty($data)){
                continue;
            }

//            缓存数据 file/table/redis
            $cacheType = \Yaconf::get('Base.VideoCacheType');
            switch($cacheType){
                case 'file':
                    $dir = EASYSWOOLE_ROOT.'/webroot/video/json/';
                    if(!is_dir($dir)){
                        mkdir($dir,0777,false);
                    }
                    $filename = $this->getCacheFileName($catId);
                    $flag = file_put_contents($filename,json_encode($data));
                    if(empty($flag)){
                        //报警：短信，邮件等等
                        echo 'catID:'.$catId.' put data error'.PHP_EOL;
                    }
                    break;
                case 'table':
                    try{
                        $flag = Cache::getInstance()->set($this->getCacheKey($catId),json_encode($data));
                    }catch (\Exception $e){
                        //报警：短信，邮件等等
                        Logger::getInstance()->log($e->getMessage(),'swoole_table');
                    }
                    break;
                case 'redis':
                    try{
                        $flag = Di::getInstance()->get('REDIS')->set($this->getCacheKey($catId) ,json_encode($data));
                    }catch (\Exception $e){
                        Logger::getInstance()->log('redis保存video首页数据出错：'.$e->getMessage(),'redis');
                    }

                    break;
                default:
                    throw new \Exception('请求不合法',404);
                    break;
            }
        }
    }


    public function getIndexVideo($catId){
        $cacheType = \Yaconf::get('Base.VideoCacheType');
        switch ($cacheType){
            case 'file':
                $data = file_get_contents($this->getCacheFileName($catId));
                break;
            case 'table':
                $data = Cache::getInstance()->get($this->getCacheKey($catId));
                break;
            case 'redis':
                $data = Di::getInstance()->get('REDIS')->get($this->getCacheKey($catId));
                break;
            default:
                throw new \Exception('请求不合法',404);
                break;
        }

        return (!empty($data)) ? json_decode($data,true) : [];
    }
    /**
     * @param $catId
     * @return string
     */
    public function getCacheFileName($catId){
        $dir = EASYSWOOLE_ROOT.'/webroot/video/json/';
        if(!is_dir($dir)){
            mkdir($dir,0777,false);
        }
        $filename = $dir.$catId.'.json';
        return $filename;
    }

    /**
     * @param $catId
     * @return string
     */
    public function getCacheKey($catId){
        return 'video_index_data_cat_id_'.$catId;
    }

}
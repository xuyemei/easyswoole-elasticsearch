<?php

namespace App\Lib\Redis;

use EasySwoole\Config;
use EasySwoole\Core\AbstractInterface\Singleton;

class Redis{
    use Singleton;
    public $redis = '';
    private function __construct()
    {
        if(!extension_loaded('redis')){
            throw new \Exception('redis.so 不存在');
        }
        try{

//            $config = Config::getInstance()->getConf('redis');
            //通过yaconf获取配置
            $config = \Yaconf::get('redis');
            $this->redis = new \Redis();
            $result = $this->redis->connect($config['host'],$config['port'],$config['time_out']);
        }catch (\Exception $e){
            throw new \Exception('redis服务异常');
        }

        if($result === false){
            throw new \Exception('redis 连接失败');
        }
    }
//
//    public function get($key){
//
//        if(!empty($key)){
//            return $this->redis->get($key);
//        }
//        return '';
//    }
//
//    public function set($key,$value,$time=0){
//
//        if(empty($key)){
//            return '';
//        }
//
//        if(is_array($value)){
//            $value = json_encode($value);
//        }
//       if(!empty($time)){
//           return $this->redis->setex($key,$time,$value);
//       }
//        return $this->redis->set($key,$value);
//
//
//    }
//
//    public function hset(){
//
//    }
//    public function hget(){
//
//    }
//
//
//    /**
//     * @param $key
//     * @param $number
//     * @param $member
//     * @return bool|float
//     */
//    public function zincrby($key,$number,$member){
//        if(empty($key) || empty($member)){
//            return false;
//        }
//
//        return $this->redis->zIncrBy($key,$number,$member);
//    }
//
//    /**
//     * @param $key
//     * @param $member
//     * @return bool|float
//     */
//    public function zscore($key,$member){
//        if(empty($key) || empty($member)){
//            return false;
//        }
//
//        return $this->redis->zScore($key,$member);
//    }
//
//    /**
//     * @param $key
//     * @param $start
//     * @param $stop
//     * @param $withsocre
//     * @return array|bool
//     */
//    public function zrevrange($key,$start,$stop,$withsocre){
//
//        if(empty($key)){
//            return false;
//        }
//        if($withsocre){
//            return $this->redis->zRevRange($key,$start,$stop,$withsocre);
//        }
//        return $this->redis->zRevRange($key,$start,$stop);
//    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        // TODO: Implement __call() method.
        return $this->redis->$name(...$arguments);
    }
}
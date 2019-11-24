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

    public function get($key){

        if(!empty($key)){
            return $this->redis->get($key);
        }
        return '';
    }

    public function set($key,$value){

        if(!empty($key)){
            return $this->redis->set($key,$value);
        }
        return false;
    }

    public function hset(){

    }
    public function hget(){

    }
}
<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/11/23 0023
 * Time: 下午 9:35
 */

namespace App\HttpController\Api;


use App\HttpController\Api\Base;
use App\Lib\Redis\Redis;
use EasySwoole\Core\Component\Di;

class Index extends Base
{

    function index()
    {
        // TODO: Implement index() method.
        $this->response()->write('hello world');
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
}
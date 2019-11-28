<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/1/9
 * Time: 下午1:04
 */

namespace EasySwoole;

use App\Lib\Cache\Video as videoCache;
use \EasySwoole\Core\AbstractInterface\EventInterface;
use EasySwoole\Core\Component\Crontab\CronTab;
use EasySwoole\Core\Component\Di;
use \EasySwoole\Core\Swoole\ServerManager;
use \EasySwoole\Core\Swoole\EventRegister;
use \EasySwoole\Core\Http\Request;
use \EasySwoole\Core\Http\Response;
use App\Lib\Redis\Redis;
use EasySwoole\Core\Swoole\Time\Timer;
use EasySwoole\Core\Utility\File;
//use App\Vendor\Db\MysqliDb;

Class EasySwooleEvent implements EventInterface {

    public static function frameInitialize(): void
    {
        // TODO: Implement frameInitialize() method.
        date_default_timezone_set('Asia/Shanghai');

        self::loadConf(EASYSWOOLE_ROOT . '/Config');
    }

    public static function loadConf($ConfPath)
    {
        $Conf  = Config::getInstance();
        $files = File::scanDir($ConfPath);
        foreach ($files as $file) {
            $data = require_once $file;
            $Conf->setConf(strtolower(basename($file, '.php')), (array)$data);
        }
    }

    public static function mainServerCreate(ServerManager $server,EventRegister $register): void
    {
        // TODO: Implement mainServerCreate() method.
        Di::getInstance()->set('MYSQL',\MysqliDb::class,Array (
            'host' => \Yaconf::get('mysql.host'),
            'username' => \Yaconf::get('mysql.username'),
            'password' => \Yaconf::get('mysql.password'),
            'db'=> \Yaconf::get('mysql.db'),
            'port' => \Yaconf::get('mysql.port'),
            'charset' => \Yaconf::get('mysql.charset'),
            )
    );

        Di::getInstance()->set('REDIS', Redis::getInstance());

        //利用crontab实现定时器
       $videoCacheObj = new videoCache();
        CronTab::getInstance()
            ->addRule('test_crontab1',"*/1 * * * *",
            function () use($videoCacheObj){
//                $videoCacheObj->getVideoInfo();
            });

//        利用woole定时器实现
        $register->add(EventRegister::onWorkerStart, function (\swoole_server $server, $workerId) use($videoCacheObj){
            if($workerId == 0){
                Timer::loop(1000*2,function () use($videoCacheObj){
                    $videoCacheObj->setIndexVideo();
                });
            }
        });
    }

    public static function onRequest(Request $request,Response $response): void
    {
        // TODO: Implement onRequest() method.
    }

    public static function afterAction(Request $request,Response $response): void
    {
        // TODO: Implement afterAction() method.
    }
}
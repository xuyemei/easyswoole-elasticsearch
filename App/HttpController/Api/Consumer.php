<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/11/29 0029
 * Time: 上午 10:48
 */
namespace App\HttpController\Api;

use EasySwoole\Core\Component\Di;
use EasySwoole\Core\Component\Logger;
use EasySwoole\Core\Swoole\Process\AbstractProcess;
use Swoole\Process;

class Consumer extends AbstractProcess{

    private $isRun=false;
    public  function run(Process $process){

        $this->addTick(1000,function (){
            if(!$this->isRun){
                $isRun = true;
                $redis = Di::getInstance()->get('REDIS');
                while (true){
                    try{
                        $task = $redis->lpop('task_list');
                        if($task){
                            //to do 邮件 短信，log等
                            echo $task;
                            Logger::getInstance()->log($this->getProcessName().'---'.$task);
                        }else{
                            break;
                        }
                    }catch (\Exception $e){
                        break;
                    }
                }
                $this->isRun = false;
            }
//            var_dump($this->getProcessName(). ' task run check');
        });

    }
    public  function onShutDown(){

    }
    public  function onReceive(string $str,...$args){

    }
}
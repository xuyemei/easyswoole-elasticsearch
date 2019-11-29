<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/11/29 0029
 * Time: 上午 10:48
 */
namespace EasySwoole;

use EasySwoole\Core\Component\Di;
use EasySwoole\Core\Swoole\Process\AbstractProcess;
use Swoole\Process;

class Consumer extends AbstractProcess{

    private $isRun;
    public  function run(Process $process){

        $this->addTick(500,function (){
            if(!$this->isRun){
                $isRun = true;
                $redis = Di::getInstance()->get('REDIS');
                while (true){
                    try{
                        $task = $redis->lpop('task_list');
                        if($task){
                            echo 123;
                        }else{
                            break;
                        }
                    }catch (\Exception $e){
                        break;
                    }
                }
                $this->isRun = false;
            }
            var_dump($this->getProcessName(). ' task run check');
        });

    }
    public  function onShutDown(){

    }
    public  function onReceive(string $str,...$args){

    }
}
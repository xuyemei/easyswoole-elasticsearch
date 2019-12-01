<?php


namespace App\Model\Es;
use EasySwoole\Core\Component\Di;

class EsVideo{

    public $index = 'test_video';
    public $type='video';

    public function searchByName($name,$type='match'){
        if(empty($name)){
            return [];
        }

        $param = [
            'index'=>$this->index,
            'type'=>$this->type,
            'body'=>[
               'query'=>[
                   $type=>[
                       'name'=>$name
                   ],
               ]
            ],
        ];

        $res = Di::getInstance()->get('ES')->search($param);
        return $res;
    }
}

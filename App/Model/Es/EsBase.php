<?php
namespace App\Model\Es;
use EasySwoole\Core\Component\Di;

class EsBase{

    public $esClient=null;

    public function __construct()
    {
        $this->esClient = Di::getInstance()->get('ES');
    }

    /**
     * @param $name
     * @param $from 分页from
     * @param $size 分页size
     * @param string $type
     * @return array
     */
    public function searchByName($name,$from,$size,$type='match'){
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
               ],
               'from'=>$from,
               'size'=>$size,
            ],
        ];

        $res = $this->esClient->search($param);
        return $res;
    }
}

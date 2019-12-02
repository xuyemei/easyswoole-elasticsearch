<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/11/23 0023
 * Time: 下午 9:35
 */

namespace App\HttpController\Api;


use App\HttpController\Api\Base;
use App\Model\Es\EsVideo;
use EasySwoole\Core\Http\Message\Status;

class Search extends Base
{

    /**
     * 搜索API
     */
    public function index()
    {
        $keyword = trim($this->params['keyword']);
        if(empty($keyword)){
            return $this->writeJson(Status::CODE_OK,'ok',$this->getPaginateDatas(0,[],0));
        }

        $result = (new EsVideo())->searchByName($keyword,$this->params['from'],$this->params['size']);
        if(empty($result)){
            return $this->writeJson(Status::CODE_OK,'ok',$this->getPaginateDatas(0,[],0));

        }
        $hits = $result['hits']['hits'];
        $total = $result['hits']['total'];
        if($total == 0){
            return $this->writeJson(Status::CODE_OK,'ok',$this->getPaginateDatas(0,[],0));

        }
        foreach ($hits as $hit){
//            $resData[] = $this->getPaginateDatas($total,$hit,0);
            $resData[] = [
                'id'=>$hit['_id'],
                'name'=>$hit['source']['name'],
                'image'=>$hit['source']['image'],
                'uploader'=>$hit['source']['uploader'],
                'video_duration'=>'',
                'create_time'=>'',
                'keyword'=>$keyword,
            ];
        }

        return $this->writeJson(Status::CODE_OK,'ok',$this->getPaginateDatas($total,$resData,0));

    }

}
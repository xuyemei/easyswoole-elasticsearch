<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/11/23 0023
 * Time: 下午 9:35
 */

namespace App\HttpController;


use App\Model\Es\EsVideo;
use EasySwoole\Core\Component\Di;
use EasySwoole\Core\Http\AbstractInterface\Controller;
use Elasticsearch\ClientBuilder;

class Index extends Controller
{

    function index()
    {
        $res = (new EsVideo())->searchByName($this->request()->getRequestParam('name'),'match_phrase');
        return $this->writeJson(200,$res,'ok');
    }
}
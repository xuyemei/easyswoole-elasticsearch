<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/11/23 0023
 * Time: 下午 9:35
 */

namespace App\HttpController\Api;


use EasySwoole\Core\Http\AbstractInterface\Controller;

/**
 * 基础类库
 * Class Base
 * @package App\HttpController\Api
 */
class Base extends Controller
{


    public $params;
    function index()
    {

    }

    /**
     * 可能会做拦截器，比如权限控制等等
     * @param $action
     * @return bool|null
     */
    public function onRequest($action):?bool
    {

        $this->params = $this->request()->getRequestParam();
        $this->params['page'] = $this->params['page'] ?? 1;
        $this->params['size'] = $this->params['size'] ?? 2;
        $this->params['from'] = ($this->params['page'] - 1) * $this->params['size'];

        return true;
    }

    /**
     * 重写writeJson方法
     * @param int $statusCode
     * @param null $msg
     * @param null $result
     * @return bool
     */
    protected function writeJson($statusCode = 200,$msg = null,$result = null){
        if(!$this->response()->isEndResponse()){
            $data = Array(
                "code"=>$statusCode,
                "msg"=>$msg,
                "result"=>$result,
            );
            $this->response()->write(json_encode($data,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
            $this->response()->withHeader('Content-type','application/json;charset=utf-8');
            $this->response()->withStatus($statusCode);
            return true;
        }else{
            trigger_error("response has end");
            return false;
        }
    }


    /**
     * @param $total
     * @param $data
     * @return array
     */
    public function getPaginateDatas($total,$data){

        $totalPage = ceil($total/$this->params['size']);
        $data = $data ?? [];
        $data = array_splice($data,$this->params['from'],$this->params['size']);
        return [
            'total_page'=>$totalPage,
            'page_size'=>$this->params['size'],
            'count'=>intval($total),
            'lists'=>$data,
        ];
    }

    /**
     * 重新异常处理
     * @param \Throwable $throwable
     * @param $actionName
     * @throws \Throwable
     */
//    protected function onException(\Throwable $throwable,$actionName):void
//    {
//        $this->writeJson(403,'请求不合法',[]) ;
//    }
}
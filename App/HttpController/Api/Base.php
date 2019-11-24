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

    function index()
    {

    }

    /**
     * 可能会做拦截器，比如权限控制等等
     * @param $action
     * @return bool|null
     */
//    public function onRequest($action):?bool
//    {
//        return true;
//    }


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
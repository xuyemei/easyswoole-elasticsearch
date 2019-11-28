<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/11/23 0023
 * Time: 下午 9:35
 */

namespace App\HttpController\Api;


use EasySwoole\Core\Http\AbstractInterface\Controller;

class Category extends Base
{

    public function index()
    {
       $conf = \Yaconf::get('category.cats');
        return $this->writeJson(200,'ok',$conf);
//        $this->response()->write('hello world 4123');
    }
}
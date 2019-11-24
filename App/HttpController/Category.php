<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/11/23 0023
 * Time: 下午 9:35
 */

namespace App\HttpController;


use EasySwoole\Core\Http\AbstractInterface\Controller;

class Category extends Controller
{

    function index()
    {
        $data = [
            'code'=>200,
            'msg'=>'ok',
        ];
        return $this->writeJson(200,'ok',$data);
//        $this->response()->write('hello world 4123');
    }
}
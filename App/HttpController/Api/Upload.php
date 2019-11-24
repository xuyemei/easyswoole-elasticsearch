<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/11/23 0023
 * Time: 下午 9:35
 */

namespace App\HttpController\Api;


use App\HttpController\Api\Base;
use EasySwoole\Core\Component\Di;

/**
 * 文件上传类，图片/视频
 * Class Upload
 * @package App\HttpController\Api
 */
class Upload extends Base
{

    public function file(){

        $request = $this->request();
        $videos = $request->getUploadedFile('file');
        $flag = $videos->moveTo('/data/myEW/webroot/2.mp4');
        if($flag){
            $this->writeJson(200,'ok',[]);
        }else{
            $this->writeJson(400,'error',[]);

        }
    }
}
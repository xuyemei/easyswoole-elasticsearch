<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/11/23 0023
 * Time: 下午 9:35
 */

namespace App\HttpController\Api;


use App\HttpController\Api\Base;
use App\Lib\ClassArr;
use EasySwoole\Core\Component\Di;
use App\Lib\Upload\Video;
use App\Lib\Upload\Image;

/**
 * 文件上传类，图片/视频
 * Class Upload
 * @package App\HttpController\Api
 */
class Upload extends Base
{


    public $type;
    public function file(){

        $request = $this->request();
        $files = $request->getSwooleRequest()->files;
        $type = array_keys($files)[0];
//        if($type == 'video'){
//            $obj = new Video($request);
//        }elseif($type == 'image'){
//            $obj = new Image($request);
//        }

        try{
//            利用php的反射机制实例化不同的文件上传类
            $classObj = new ClassArr();
            $uploadStat = $classObj->uploadClassStat();
            $uploadObj = $classObj->initClass($type,$uploadStat,[$request,$type],true);
            $flag = $uploadObj->upload();
        }catch (\Exception $e){
            return $this->writeJson(400,$e->getMessage(),[]);
        }

        $data = [
            'file'=>$uploadObj->file,
        ];
        if(!empty($flag)){
            return $this->writeJson(200,'ok',$data);
        }else{
            return $this->writeJson(400,'上传失败',[]);
        }




//        $flag = $videos->moveTo('/data/myEW/webroot/2.mp4');
//        if($flag){
//            $this->writeJson(200,'ok',[]);
//        }else{
//            $this->writeJson(400,'error',[]);
//
//        }
    }
}
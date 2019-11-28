<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/11/25 0025
 * Time: 上午 10:52
 */

namespace App\Lib\Upload;
use App\Lib\Utils;

class Base{


    /**
     * 上传文件的file--type
     * @var string
     */
    public $type = '';
    /**
     * 文件大小
     * @var int
     */
    public $size;

    /**
     * 文件上传成功后要返回给前端的文件路径
     * @var
     */
    public $file;

    public $clientMediaType;
    public function __construct($request,$type=null)
    {
        $this->request = $request;
        if(empty($type)){

            $files = $this->request->getSwooleRequest()->files;
            $type = array_keys($files);
            $this->type = $type[0];
        }else{
            $this->type = $type;
        }

    }

    public function upload(){
        if($this->type != $this->fileType){
            return false;
        }

        $videos = $this->request->getUploadedFile($this->type);
        $this->size = $videos->getSize();
        if(!$this->checkSize()){
            return false;
        }
        $filename = $videos->getClientFileName();
        $this->clientMediaType = $videos->getClientMediaType();
        $this->checkType();
        $this->getFile($filename);
        $flag = $videos->moveTo($this->file);
        if(!empty($flag)){
            return $this->file;
        }
        return $flag;

    }

    /**
     * 生成长传的文件的随机名称
     * @param $filename
     */
    public function getFile($filename){
        $pathinfo = pathinfo($filename);
        $extension = $pathinfo['extension'];
        //定义文件保存路径
        $basename = '/'.Utils::makeRandomString(16).'.'.$extension;
        $dir = EASYSWOOLE_ROOT.'/webroot/' .$this->type.'/'.date('Y').'/'.date('m');
        if(!is_dir($dir)){
            mkdir($dir,0777,true);
        }
        $this->file = $dir.$basename;

    }


    /**
     * check上传文件大小
     * @return bool
     */
    public function checkSize(){

        if(empty($this->size)){
            return false;
        }
        if($this->size > $this->maxSize){
            return false;
        }

        return true;
    }

    /**
     * check上传的文件类型是否正确
     */
    public function checkType(){

       $clientExtType = explode('/',$this->clientMediaType);
        $clientExtType = $clientExtType[1] ?? '';
       if(empty($clientExtType)){
           throw new \Exception("上传的{$this->type}文件不合法");
       }
       if(!in_array($clientExtType,$this->fileExtType)){

           throw new \Exception("上传的{$this->type}文件不合法");
       }
       return true;
    }
}


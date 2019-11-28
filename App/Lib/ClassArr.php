<?php
namespace App\Lib;

/**
 * 通用工具类
 */
class ClassArr {

    /**
     * 文件上传的反射机制
     * @return array
     *
     */
    public function uploadClassStat()
    {
        return [
            'image'=>'App\Lib\Upload\Image',
            'video'=>'App\Lib\Upload\Video',
        ];
    }

    /**
     * @param $type
     * @param $supportClass
     * @param array $params
     * @param bool $needInstance
     * @return bool|object
     */
    public function initClass($type,$supportClass,$params=[],$needInstance = true){

        if(!array_key_exists($type,$supportClass)){
            return false;
        }

        $className = $supportClass[$type];
        return $needInstance ? (new \ReflectionClass($className))->newInstanceArgs($params) : $className;
    }


}
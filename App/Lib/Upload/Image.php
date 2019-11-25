<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/11/25 0025
 * Time: 上午 10:52
 */

namespace App\Lib\Upload;

class Video extends Base{

    public $fileType = 'video';
    public $maxSize = 5000000;

    /**
     * 视频合法类型
     * @var array
     */
    public $fileExtType = ['mp4','x-flv'];

}


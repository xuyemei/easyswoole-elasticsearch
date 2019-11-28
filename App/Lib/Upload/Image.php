<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/11/25 0025
 * Time: 上午 10:52
 */

namespace App\Lib\Upload;

class Image extends Base{

    public $fileType = 'image';
    public $maxSize = 2000000;

    /**
     * 图片合法类型
     * @var array
     */
    public $fileExtType = ['jpg','png','jpeg'];

}


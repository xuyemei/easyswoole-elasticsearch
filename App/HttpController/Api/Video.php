<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/11/23 0023
 * Time: 下午 9:35
 */

namespace App\HttpController\Api;


use App\HttpController\Api\Base;
use EasySwoole\Core\Http\AbstractInterface\Controller;
use App\Model\Video as VideoModel;
use EasySwoole\Core\Http\Message\Status;
use EasySwoole\Core\Utility\Validate\Rules;
use EasySwoole\Core\Utility\Validate\Rule;
class Vedio extends Base
{
    public function add(){
        $request = $this->request();
        $params = $request->getRequestParam();
        $rulesObj = new Rules();
        $rulesObj->add('name','名称不合法')
            ->withRule(Rule::REQUIRED)
            ->withRule(Rule::MAX_LEN,20)
            ->withRule(Rule::MIN_LEN,2);
        $rulesObj->add('url','URL不合法')
            ->withRule(Rule::REQUIRED)
            ->withRule(Rule::URL);
        $rulesObj->add('image','图片地址不合法')
            ->withRule(Rule::REQUIRED)
            ->withRule(Rule::URL);
        $rulesObj->add('content','内容不合法')
            ->withRule(Rule::REQUIRED)
            ->withRule(Rule::MIN_LEN,5);
        $rulesObj->add('cat_id','栏目ID不合法')
            ->withRule(Rule::INTEGER)
            ->withRule(Rule::REQUIRED);
        $validate = $this->validateParams($rulesObj);
        if($validate->hasError()){
            print_r($validate->getErrorList());
            return;
        }

        $data = [
              'name'=>$params['name'],
              'url'=>$params['url'],
              'image'=>$params['image'],
              'content'=>$params['content'],
              'cat_id'=>$params['cat_id'],
              'create_time'=>$params['create_time'],
              'uploader'=>'yemei',
              'status'=>\Yaconf::get('status.normal'),
        ];
        try{
            $videoModel = new VideoModel();
            $videoId = $videoModel->add($data);
        }catch (\Exception $e){
            return $this->writeJson(Status::CODE_BAD_REQUEST,$e->getMessage());
        }
        if(!empty($videoId)){
            return $this->writeJson(Status::CODE_ACCEPTED,'ok');
        }else{
            return $this->writeJson(Status::CODE_BAD_REQUEST,'数据插入失败');
        }
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/11/25 0025
 * Time: 下午 8:58
 */
namespace App\Model;

use EasySwoole\Core\Component\Di;
use EasySwoole\Core\Component\Logger;
use EasySwoole\Core\Swoole\Coroutine\Client\Mysql;
//use App\Vendor\Db\MysqliDb;

class Base{

    /**
     * db对象实例
     * @var \MysqliDb|null|string
     */
    public $db;

    public function __construct()
    {
        if(empty($this->tableName)){
            throw new \Exception('table error');
        }

        $db = Di::getInstance()->get('MYSQL');
        if($db instanceof \MysqliDb){
            $this->db = $db;
        }else{
            throw new \Exception('db error');
        }
    }

    /**
     * @param $data
     * @return bool
     * @throws \Exception
     */
    public function add($data){
        if(empty($data) || !is_array($data)){
            throw new \Exception('数据不合法');
        }

        $videoId = $this->db->insert($this->tableName,$data);
//        try{
//            $videoId = $this->db->insert($this->tableName,$data);
//
//        }catch (\Exception $e){
//            throw new \Exception("插入出错");
//        }

        if(!empty($videoId)){
            return $videoId;
        }else{
            throw new \Exception("插入出错");
        }
    }

    /**
     * @param $id
     */
    public function getDataById($id){
        $id = intval($id);
        if(empty($id)){
            return [];
        }
        try{
            $this->db->where('id',$id);
            $data = $this->db->getOne($this->tableName);
        }catch (\Exception $e){

            Logger::getInstance()->log($e->getMessage(),'dbError');
            throw new \Exception($e->getMessage());
        }

        return $data;

    }

}
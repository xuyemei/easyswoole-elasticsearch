<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/11/25 0025
 * Time: 下午 8:58
 */
namespace App\Model;

class Video extends Base {

    public $tableName = 'video';

    /**
     * 根据条件获取数据的video
     * @param array $condition
     * @param int $page
     * @param int $size
     * @return array
     */
    public function getVideoList($condition=[],$page=1,$size=10){

        if(!empty($size)){
            $this->db->pageLimit = $size;
        }

        if(!empty($condition)){
            foreach($condition as $key=>$value){
                $this->db->where($key,$value);
            }
        }

        $this->db->orderBy('id','DESC');

        $lists = $this->db->paginate($this->tableName,$page);
//        echo $this->db->getLastQuery();

        $data = [
            'total_page'=>$this->db->totalPages,//分页后的总页数
            'page_size'=>$size,//每页的记录数
            'count'=>intval($this->db->totalCount),//总记录数
            'lists'=>$lists,
        ];

        return $data;
    }

    /**
     * cache的数据
     * @param array $condition
     * @param int $page
     * @param int $size
     * @return array
     */
    public function getVideoCacheList($condition=[],$size=1000){

        if(!empty($size)){
            $this->db->pageLimit = $size;
        }

        if(!empty($condition)){
            foreach($condition as $key=>$value){
                $this->db->where($key,$value);
            }
        }

        $this->db->orderBy('id','DESC');

        $lists = $this->db->paginate($this->tableName,1);
        return $lists;
    }



}
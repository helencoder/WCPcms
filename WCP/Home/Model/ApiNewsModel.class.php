<?php
/**
 * Author：helen
 * CreateTime: 2016/08/22 09:23
 * Description：新闻存储表
 */
namespace Home\Model;

use Think\Model;

class ApiNewsModel extends Model
{
    protected $tablePrefix = 'wcp_';

    /**
     * 实例化当前数据表
     */
    protected function db_table()
    {
        $db_table = D('api_news');
        return $db_table;
    }

    /**
     * 获取当前数据表中的所有数据
     */
    public function getData()
    {
        $db = $this->db_table();
        $data = $db->select();
        return $data;
    }

    /**
     * 获取当前数据表中的数据条数
     */
    public function getCount()
    {
        $db = $this->db_table();
        $count = $db->count();
        return $count;
    }

    /**
     * 根据新闻类型获取相应的新闻信息
     */
    public function getNewsDataByType($type)
    {
        $db = $this->db_table();
        $map['type'] = $type;
        $data = $db->where($map)->select();
        return $data;
    }

    /**
     * 根据新闻日期获取相应的新闻信息
     */
    public function getNewsDataByDate($date)
    {
        $db = $this->db_table();
        $map['date'] = $date;
        $data = $db->where($map)->select();
        return $data;
    }

    /**
     * 根据特定条件获取相应的新闻信息
     */
    public function getNewsDataByCondition(array $map)
    {
        $db = $this->db_table();
        $data = $db->where($map)->select();
        return $data;
    }
}
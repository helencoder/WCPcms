<?php
/**
 * Author：helen
 * CreateTime: 2016/07/19 22:29
 * Description：网站用户记录表
 */
namespace Home\Model;

use Think\Model;

class WebsiteUserModel extends Model
{
    protected $tablePrefix = 'wcp_';

    /**
     * 实例化当前数据表
     */
    protected function db_table()
    {
        $db_table = D('website_user');
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
}
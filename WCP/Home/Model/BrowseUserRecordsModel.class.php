<?php
/**
 * Author: helen
 * CreateTime: 2016/06/07 16:59
 * Description: 访问用户记录表
 */
namespace Home\Model;

use Think\Model;

class BrowseUserRecordsModel extends Model
{
    protected $tablePrefix = 'wcp_';
    protected $table = array();

    /*protected function __initialize()
    {
        $table = D('browse_user_records');
        $this->table = $table;
        return $this;
    }*/

    /**
     * 需要定义模型类中定义的方法时，需要使用D方法实例化此模型类，M方法不可用。
     */

    /**
     * 实例化当前数据表
     */
    protected function db_table()
    {
        $db_table = D('browse_user_records');
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

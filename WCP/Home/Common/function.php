<?php
/**
 * Author: helen
 * CreateTime: 2016/4/19 15:07
 * description: 公共函数库
 */

/*
* 存储访问用户记录函数
* IP相同，PHPSESSID相同 不进行存储
* @return interger $id 存储记录id号，存储失败返回null
* */
function save_browse_user_records()
{
    //实例化访问用户记录表
    $browse_user_records_table = M('browse_user_records');
    //访问人员记录、时间、IP等
    $data['ip'] = get_client_ip();
    if (strpos($_SERVER['HTTP_USER_AGENT'], 'Mobile') !== false) {
        $data['device'] = 'Mobile';
    } else {
        $data['device'] = 'Web';
    }
    if (strpos($_SERVER['HTTP_COOKIE'], 'PHPSESSID') !== false) {
        $str = strstr($_SERVER['HTTP_COOKIE'], 'PHPSESSID');
        $data['phpsessid'] = substr($str, 10);
    } else {
        $data['phpsessid'] = '';
    }
    $data['browse_time'] = date('Y-m-d H:i:s', time());
    //进行数据查询；首先查找IP，其次查找PHPSESSID
    if ($data['phpsessid'] != '') {     //首先检查用户是否存在
        $map['phpsessid'] = $data['phpsessid'];
        $map['ip'] = $data['ip'];
        $res = $browse_user_records_table->where($map)->find();
        if ($res) {   //用户已存在，不进行新增
            return $res;
        } else {      //用户未存在，新增
            $res = $browse_user_records_table->data($data)->add();
        }
    } else {
        $res = $browse_user_records_table->data($data)->add();
    }
    return $res;
}

/*
 * 获取用户ip，避免使用代理情况，获取真实IP
 * */
function getip()
{
    $unknown = 'unknown';
    if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])
        && $_SERVER['HTTP_X_FORWARDED_FOR']
        && strcasecmp($_SERVER['HTTP_X_FORWARDED_FOR'],
            $unknown)
    ) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } elseif (isset($_SERVER['REMOTE_ADDR'])
        && $_SERVER['REMOTE_ADDR'] &&
        strcasecmp($_SERVER['REMOTE_ADDR'], $unknown)
    ) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    /*
    处理多层代理的情况
    或者使用正则方式：$ip = preg_match("/[\d\.]
    {7,15}/", $ip, $matches) ? $matches[0] : $unknown;
    */
    if (false !== strpos($ip, ','))
        $ip = reset(explode(',', $ip));
    return $ip;
}

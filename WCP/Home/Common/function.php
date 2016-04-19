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

/*
 * 自定义错误处理函数
 * 功能：错误日志记录
 * 功能：错误信息表记录，返回相关信息
 * 参数	描述
    error_level	    必需。为用户定义的错误规定错误报告级别。必须是一个值数。
    error_message	必需。为用户定义的错误规定错误消息。
    error_file	    可选。规定错误在其中发生的文件名。
    error_line	    可选。规定错误发生的行号。
    error_context	可选。规定一个数组，包含了当错误发生时在用的每个变量以及它们的值。
 *
 * */
/*
 * 文件打开模式(fopen、fclose)
 * 模式	描述
    r	打开文件为只读。文件指针在文件的开头开始。
    w	打开文件为只写。删除文件的内容或创建一个新的文件，如果它不存在。文件指针在文件的开头开始。
    a	打开文件为只写。文件中的现有数据会被保留。文件指针在文件结尾开始。创建新的文件，如果文件不存在。
    x	创建新文件为只写。返回 FALSE 和错误，如果文件已存在。
    r+	打开文件为读/写、文件指针在文件开头开始。
    w+	打开文件为读/写。删除文件内容或创建新文件，如果它不存在。文件指针在文件开头开始。
    a+	打开文件为读/写。文件中已有的数据会被保留。文件指针在文件结尾开始。创建新文件，如果它不存在。
    x+	创建新文件为读/写。返回 FALSE 和错误，如果文件已存在。
 * */

function customError($error_level, $error_message, $error_file, $error_line, $error_context)
{
    $time = date('Y-m-d H:i:s', time());
    $errmsg = $time . " <b>Error:</b> [$error_level] $error_message<br />" . "error_file:$error_file" . "($error_line)。\r\n\r\n";
    $date = date('Y-m-d', time());
    $root = $_SERVER['DOCUMENT_ROOT'];
    $filename = $root . '/WCPcms/Log/' . "$date" . '.log';
    $dir = $root . '/WCPcms/Log';
    //用户错误信息记录表记录
    $error_records = M('error_records');
    $data['msg'] = $errmsg;
    $data['occur_time'] = $time;
    $error_records->data($data)->add();
    //错误日志添加
    if (!file_exists($dir)) {
        @mkdir($dir, 0777);
    }
    //错误日志记录,文件不存在，则直接创建后添加信息；否则直接追加
    if (!file_exists($filename)) {
        touch($filename);
        @$fp = fopen($filename, "a");
        fwrite($fp, $errmsg);
        fclose($fp);
    } else {
        error_log($errmsg, 3, $filename);
    }
}

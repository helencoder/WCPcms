<?php
/**
 * Author: helen
 * CreateTime: 2016/4/19 15:07
 * description: 公共函数库
 */

/**
 * 存储访问用户记录函数
 * IP相同，PHPSESSID相同 不进行存储
 * @return interger $id 存储记录id号，存储失败返回null
 */
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
        if (strpos($str, ';')) {
            $data['phpsessid'] = substr($str, 10, strpos($str, ';') - 10);
        } else {
            $data['phpsessid'] = substr($str, 10);
        }
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
/**
 * 获取用户ip，避免使用代理情况，获取真实IP
 */
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

/**
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
 */
/**
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
 */

function customError($error_level, $error_message, $error_file, $error_line, $error_context)
{
    $time = date('Y-m-d H:i:s', time());
    $errmsg = $time . " <b>Error:</b> [$error_level] $error_message<br />" . "error_file:$error_file" . "($error_line)。\r\n\r\n";
    $date = date('Y-m-d', time());
    $root = dirname($_SERVER['DOCUMENT_ROOT']);     //获取website的上一级目录
    $filename = $root . '/Log/' . "$date" . '.log';
    $dir = $root . '/Log';
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

/**
 * 缓存函数
 * @param $type 是否缓存，默认缓存$type=1，其他参数为不缓存
 * @param $offset 默认缓存时间一小时
 *
 */
function cache($type = 1, $offset = 86400)
{
    switch ($type) {
        case '1':
            //浏览器缓存文件一个月（过期时间得用gmdate来设置，而不是date）
            header("Cache-Control: public");
            header("Pragma: cache");
            //$offset = 30*60*60*24; // cache 1 month
            $ExpStr = "Expires: " . gmdate("D, d M Y H:i:s", time() + $offset) . " GMT";
            header($ExpStr);
            break;
        default :
            //不设置缓存
            header("Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0"); // HTTP/1.1
            header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
            header("Pragma: no-cache"); // Date in the past
            break;
    }
}

/**
 * 获取当前页面的完整url
 */
function getCurrentUrl()
{
    //当前页面的url
    $url = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    return $url;
}

/**
 * 功能：接口请求函数
 * 参数：url，[data](通过是否传入data判断其为get请求还是post请求)
 * 返回：json数据
 */
function request($url, $data = null)
{
    //初始化cURL方法
    $ch = curl_init();
    //设置cURL参数（基本参数）
    $opts = array(
        //在局域网内访问https站点时需要设置以下两项，关闭ssl验证！
        //此两项正式上线时需要更改（不检查和验证认证）
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_URL => $url,
        /*CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $data*/
    );
    curl_setopt_array($ch, $opts);
    //post请求参数
    if (!empty($data)) {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    }
    //执行cURL操作
    $output = curl_exec($ch);
    if (curl_errno($ch)) {    //cURL操作发生错误处理。
        var_dump(curl_error($ch));
        die;
    }
    //关闭cURL
    curl_close($ch);
    $res = json_decode($output);
    return ($res);   //返回json数据
}

/**
 * 随机数生成函数
 * 可以指定生成随机数的长度,默认16位
 */
function getRandomCode($length = 16)
{
    $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    $code = '';
    for ($i = 0; $i < $length; $i++) {
        $tip = mt_rand(0, 61);
        $code .= $chars[$tip];
    }
    return $code;
}

/**
 * 获取参数数组、用于正则等验证(包括大写字母数组、小写字母数组、数字数组)
 * 方法：
 *      chr()函数可把ASCII码转换成普通字符
 *      1.0-9的ASCII码为48-57
 *      2.a-z的ASCII码为97-122
 *      3.A-Z的ASCII码为65-90
 */
function verifyArgs()
{
    $args = array(
        'lowerCase' => array(),
        'upperCase' => array(),
        'number' => array()
    );
    // 处理方式1 (直接字符串增加)
    /*for ($i = 'a', $j = 'A'; $i < 'z' , $j < 'Z'; $i++, $j++) {
        array_push($args['lowerCase'], $i);
        array_push($args['upperCase'], $j);
    }
    array_push($args['lowerCase'], 'z');
    array_push($args['upperCase'], 'Z');*/

    // 处理方式2 (ASCII码转换)
    for ($i = 65, $j = 97; $i < 91 ,$j < 123; $i++, $j++) {
        array_push($args['upperCase'], chr($i));
        array_push($args['lowerCase'], chr($j));
    }

    // 处理数字数组(此处貌似有更好方法)
    for ($k = 0; $k <= 9; $k++) {
        array_push($args['number'], $k);
    }

    return $args;
}

/**
 * 避免页面缓存
 */
function no_cache()
{
    $gmt_time = gmdate ("l d F Y H:i:s")." GMT";
    header("Last-Modified: " . $gmt_time);
    header("Expires: Sunday 31 July 2016 02:45:27 GMT");
    header("Pragma: no-cache");
    header("Cache-Control: no-cache");
}
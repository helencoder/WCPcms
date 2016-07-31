<?php
/**
 * Author：helen
 * CreateTime: 2016/07/31 11:15
 * Description：会话处理函数
 */
$sdbc = NULL;

function open_session()
{
    global $sdbc;
    $sdbc = mysqli_connect('127.0.0.1', 'root', '', 'wcp');
    return true;
}

function close_session()
{
    global $sdbc;
    return mysqli_close($sdbc);
}

function read_session($sid)
{
    global $sdbc;
    $query = sprintf('SELECT data FROM wcp_sessions WHERE id="%s"', mysqli_real_escape_string($sdbc, $sid));
    $res = mysqli_query($sdbc, $query);
    if (mysqli_num_rows($res) == 1) {
        list($data) = mysqli_fetch_array($res, MYSQLI_NUM);
        return $data;
    } else {
        return '';
    }
}

function write_session($sid, $data)
{
    global $sdbc;
    $query = sprintf('REPLACE INTO wcp_sessions (id, data) VALUES ("%s", "%s")', mysqli_real_escape_string($sdbc, $sid), mysqli_real_escape_string($sdbc, $data));
    $res = mysqli_query($sdbc, $query);
    return true;
}

function destory_session($sid)
{
    global $sdbc;
    $query = sprintf('DELETE FROM wcp_sessions WHERE id="%s"', mysqli_real_escape_string($sdbc, $sid));
    $res = mysqli_query($sdbc, $query);
    $_SESSION = array();
    return true;
}

function clean_session($expire)
{
    global $sdbc;
    $query = sprintf('DELETE FROM wcp_sessions WHERE DATE_ADD(last_accessed, INTERVAL %d SECOND) < NOW()', (int) $expire);
    $res = mysqli_query($sdbc, $query);
    return true;
}

session_set_save_handler('open_session', 'close_session', 'read_session', 'write_session', 'destory_session', 'clean_session');

session_start();
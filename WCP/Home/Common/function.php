<?php
/**
 * Author: helen
 * CreateTime: 2016/4/19 15:07
 * description: ����������
 */

/*
* �洢�����û���¼����
* IP��ͬ��PHPSESSID��ͬ �����д洢
* @return interger $id �洢��¼id�ţ��洢ʧ�ܷ���null
* */
function save_browse_user_records()
{
    //ʵ���������û���¼��
    $browse_user_records_table = M('browse_user_records');
    //������Ա��¼��ʱ�䡢IP��
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
    //�������ݲ�ѯ�����Ȳ���IP����β���PHPSESSID
    if ($data['phpsessid'] != '') {     //���ȼ���û��Ƿ����
        $map['phpsessid'] = $data['phpsessid'];
        $map['ip'] = $data['ip'];
        $res = $browse_user_records_table->where($map)->find();
        if ($res) {   //�û��Ѵ��ڣ�����������
            return $res;
        } else {      //�û�δ���ڣ�����
            $res = $browse_user_records_table->data($data)->add();
        }
    } else {
        $res = $browse_user_records_table->data($data)->add();
    }
    return $res;
}
/*
 * ��ȡ�û�ip������ʹ�ô����������ȡ��ʵIP
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
    �������������
    ����ʹ������ʽ��$ip = preg_match("/[\d\.]
    {7,15}/", $ip, $matches) ? $matches[0] : $unknown;
    */
    if (false !== strpos($ip, ','))
        $ip = reset(explode(',', $ip));
    return $ip;
}

/*
 * �Զ����������
 * ���ܣ���¼������Ϣ�����������Ϣ
 * ����	����
    error_level	    ���衣Ϊ�û�����Ĵ���涨���󱨸漶�𡣱�����һ��ֵ����
    error_message	���衣Ϊ�û�����Ĵ���涨������Ϣ��
    error_file	    ��ѡ���涨���������з������ļ�����
    error_line	    ��ѡ���涨���������кš�
    error_context	��ѡ���涨һ�����飬�����˵�������ʱ���õ�ÿ�������Լ����ǵ�ֵ��
 *
 * */
/*
 * �ļ���ģʽ(fopen��fclose)
 * ģʽ	����
    r	���ļ�Ϊֻ�����ļ�ָ�����ļ��Ŀ�ͷ��ʼ��
    w	���ļ�Ϊֻд��ɾ���ļ������ݻ򴴽�һ���µ��ļ�������������ڡ��ļ�ָ�����ļ��Ŀ�ͷ��ʼ��
    a	���ļ�Ϊֻд���ļ��е��������ݻᱻ�������ļ�ָ�����ļ���β��ʼ�������µ��ļ�������ļ������ڡ�
    x	�������ļ�Ϊֻд������ FALSE �ʹ�������ļ��Ѵ��ڡ�
    r+	���ļ�Ϊ��/д���ļ�ָ�����ļ���ͷ��ʼ��
    w+	���ļ�Ϊ��/д��ɾ���ļ����ݻ򴴽����ļ�������������ڡ��ļ�ָ�����ļ���ͷ��ʼ��
    a+	���ļ�Ϊ��/д���ļ������е����ݻᱻ�������ļ�ָ�����ļ���β��ʼ���������ļ�������������ڡ�
    x+	�������ļ�Ϊ��/д������ FALSE �ʹ�������ļ��Ѵ��ڡ�
 * */

function customError($error_level, $error_message, $error_file, $error_line, $error_context)
{
    $time = date('Y-m-d H:i:s', time());
    $errmsg = $time . " <b>Error:</b> [$error_level] $error_message<br />" . "error_file:$error_file" . "($error_line)��\r\n\r\n";
    $date = date('Y-m-d', time());
    $root = $_SERVER['DOCUMENT_ROOT'];
    $filename = $root . '/WCPcms/Log/' . "$date" . '.log';
    $dir = $root . '/WCPcms/Log';
    if (!file_exists($dir)) {
        @mkdir($dir, 0777);
    }
    //������־��¼,�ļ������ڣ���ֱ�Ӵ����������Ϣ������ֱ��׷��
    if (!file_exists($filename)) {
        touch($filename);
        @$fp = fopen($filename, "a");
        fwrite($fp, $errmsg);
        fclose($fp);
    } else {
        error_log($errmsg, 3, $filename);
    }
}

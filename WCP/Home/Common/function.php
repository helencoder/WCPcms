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
<?php
/**
 * Author: helen
 * CreateTime: 2016/4/19 9:58
 * description: ��վ����������
 */
namespace Home\Controller;
use Think\Controller;

class CommonController extends Controller
{
    /*
     * �ղ���Ĭ��Ϊ��½
     * ������ǰ�ˣ�����ת����վ��ҳ��
     * �����ں�̨������ת����̨��ҳ�棨ͨ���ж��Ƿ����cookie��Ϣ��
     * */
    public function _empty()
    {
        redirect(U('Index/Display/main'), 0, '������վ��');
    }

    /*
     * �����߹�������
     * ǰ��չʾ·������
     * ��̨��½�����û��Ƿ��Ѿ���½����ʱ�����cookie��Ϣ
     * */
    protected function _initialize()
    {
        //�趨��Ŀ����·����������Ϣ
        ini_set("include_path", "D:wamp/www/WCPcms");
        $root = $_SERVER['DOCUMENT_ROOT'];
        $project_path = $root . 'WCPcms/';
        $this->assign('root', $root);
        $this->assign('project_path', $project_path);
        //�趨��Ŀ����
        header('Content-type: text/html; charset=utf-8');

    }
}
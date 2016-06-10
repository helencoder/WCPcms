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
    /**
     * 空方法操作
     * 空操作默认跳转到网站主展示界面
     */
    public function _empty()
    {
        redirect(U('Index/Display/main'), 0);
    }

    /**
     * 初始化方法
     * 控制器的基础方法
     * 添加基本配置
     */
    protected function _initialize()
    {
        //基本配置方法
        $this->config();

        //错误控制器
        set_error_handler("customError", E_ALL);

    }

    /**
     * 基础配置
     */
    protected function config()
    {
        //页面编码
        header('Content-type: text/html; charset=utf-8');
        //网站路径配置
        $document_root = $_SERVER['DOCUMENT_ROOT'];
        $include_path = $document_root . 'WCPcms';
        ini_set("include_path", $include_path);
        $root = $_SERVER['DOCUMENT_ROOT'];
        $project_path = $root . 'WCPcms';
        $this->assign('root', $document_root);
        $this->assign('project_path', $project_path);
        //时区设置
        date_default_timezone_set('PRC');
    }
}
<?php
/**
 * Author: helen
 * CreateTime: 2016/4/19 9:58
 * description: 网站公共配置类
 */
namespace Home\Controller;
use Think\Controller;

class CommonController extends Controller
{
    /*
     * 空操作默认为登陆
     * 假设在前端，则跳转到网站主页面
     * 假设在后台，则跳转到后台主页面（通过判断是否带有cookie信息）
     * */
    public function _empty()
    {
        redirect(U('Index/Display/main'), 0, '进入网站中');
    }

    /*
     * 开发者公共配置
     * 前端展示路径控制
     * 后台登陆后检测用户是否已经登陆，随时检测其cookie信息
     * */
    protected function _initialize()
    {
        //引入公共配置项
        $this->config();

        //判断是否登录

        //设定错误处理函数
        set_error_handler("customError", E_ALL);



    }

    /*
     * 公共配置项
     * */
    protected function config()
    {
        //设定项目编码
        header('Content-type: text/html; charset=utf-8');
        //设定项目基础路径等配置信息
        $document_root = $_SERVER['DOCUMENT_ROOT'];
        $include_path = $document_root . 'WCPcms';
        ini_set("include_path", $include_path);
        $root = $_SERVER['DOCUMENT_ROOT'];
        $project_path = $root . 'WCPcms';
        $this->assign('root', $document_root);
        $this->assign('project_path', $project_path);
        //设置中国时区
        date_default_timezone_set('PRC');
    }
}
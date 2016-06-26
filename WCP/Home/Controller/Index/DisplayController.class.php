<?php
/**
 * Author: helen
 * CreateTime: 2016/4/19 10:24
 * description: 网站前端展示主页
 */
namespace Home\Controller\Index;

use Home\Controller\CommonController;

class DisplayController extends CommonController
{
    // 网站主界面
    public function main()
    {
        //存储访问用户记录（访问人员记录、时间、IP等）
        //$res = save_browse_user_records();

        $this->display();

    }

    // 前端测试页
    public function test()
    {
        $this->display();
    }

    // 模板测试页
    public function template()
    {
        // 定制页面基本信息
        $page = array(
            'title' 	  => 'WCPcms微信公众平台管理系统',
            'description' => '定制化、模板化',
            'author'      => 'helen',
            /*'icon'        => ''*/  
        );

        $this->assign('page',$page);
        $this->display();
    }

    // cover页面
    public function cover()
    {
        $this->display();
    }

    // 前端index页面测试
    public function index()
    {
        $this->display();
    }

    // 后台index页面测试
    public function admin()
    {
        $this->display();
    }

}
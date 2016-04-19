<?php
namespace Home\Controller;
use Think\Controller;

/*
 * 项目入口控制器
 * */

class IndexController extends Controller
{
    //项目入口跳转行为(访问人员记录)
    public function index()
    {
        dump('hello,CMS');
        //访问人员记录、时间、IP等

        //页面重定向
        redirect(U('Home/Index/Display/main'), 0, '信息');
    }
}
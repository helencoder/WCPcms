<?php
namespace Home\Controller;

use Think\Controller;

/**
 * 项目入口控制器
 */

class IndexController extends Controller
{
    //项目入口跳转行为(访问人员记录)
    public function index()
    {

        //sleep for 5 seconds
        //sleep(5);

        //存储访问用户记录（访问人员记录、时间、IP等）
        $res = save_browse_user_records();
        if ($res) {
            //页面重定向
            redirect(U('Home/Index/Display/main'), 0);
        } else {
            $this->save_errmsg_records();
            //页面重定向
            redirect(U('Home/Index/Display/main'), 0);
        }

    }

    /*
     * 存储错误记录函数
     *
     * */
    function save_errmsg_records()
    {

    }

}
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
    //网站主界面
    public function main()
    {
        //存储访问用户记录（访问人员记录、时间、IP等）
        $res = save_browse_user_records();

        //set error handler


        //trigger error
        echo($test);
        /*$test=2;
        if ($test>1)
        {
            trigger_error("Something went wrong , but Don't worry!",E_USER_WARNING);
        }*/
    }


}
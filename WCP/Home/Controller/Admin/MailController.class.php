<?php
/**
 * Author: helen
 * CreateTime: 2016/4/19 23:22
 * description: 网站邮箱控制类--账号激活，密码找回等
 */
namespace Home\Controller\Admin;

use Home\Controller\CommonController;

class MailController extends CommonController
{
    //账号激活
    public function activate()
    {
        $nickname = '';
        $this->assign('nickname', $nickname);
        $url = getCurrentUrl();
        dump($url);
        $code = getRandomCode(32);
        dump($code);
        dump(mt_rand(1, 10));

        $email = '';
        $this->display();
    }

    //忘记密码
    public function forgotPassword()
    {

    }
    //密码重置

}
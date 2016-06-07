<?php
/**
 * Author: helen
 * CreateTime: 2016/4/19 23:22
 * description: 邮箱控制器
 */
namespace Home\Controller\Admin;

use Home\Controller\CommonController;

class MailController extends CommonController
{
    //�˺ż���
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

    //�������
    public function forgotPassword()
    {

    }
    //��������

}
<?php
/**
 * Author：helen
 * CreateTime: 2016/06/25 16:50
 * Description：SSO控制器
 */
namespace Home\Controller;

use Think\Controller;

class AdminController extends Controller
{

    /**
     * sso验证机制
     * 1、利用登陆用户的用户名、密码进行加密(利用crypt函数进行加密)
     * 2、对于加密生成的字符串即为所需的ticket
     * 3、存入指定的cookie
     * 4、cookie设定两小时过期时间
     */

    /**
     * 生成sso验证ticket
     */
    public function createTicket($username, $password)
    {
        $str = $username . 'wcp' . ($password);
        //$salt = '$5$rounds=5000$usesomesillystringforsalt$';    //SHA-256
        $salt = md5($password);
        $ticket = crypt($str, $salt);
        return $ticket;
    }

    /**
     * 获取sso验证ticket
     */
    public function getTicket()
    {
        $ticket = $_COOKIE['ticket'];
        return $ticket;
    }

    /**
     * 验证sso的ticket
     */
    public function verify($username, $password)
    {
        $ticket = $this->getTicket();
        $str = $username . 'wcp' . ($password);
        $salt = md5($password);
        $verify_ticket = crypt($str, $salt);
        if ($ticket == $verify_ticket) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * ticket过期设置
     */
    public function expire($ticket)
    {
        $expire = time() + 3600;
        setcookie('ticket', $ticket, $expire);
    }
}
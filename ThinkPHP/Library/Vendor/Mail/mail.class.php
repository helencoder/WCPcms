<?php
/**
 * Author: helen
 * CreateTime: 2016/4/19 22:10
 * description: SMTP邮件发送函数
 */
include 'smtp.php'; //引入smtp类
class Mail
{

    private $_smtp_server = 'smtp.163.com';       //使用163邮箱服务器
    private $_smtp_server_port = 25;                   //端口号
    private $_smtp_email_from;                          //163邮箱账号
    private $_smtp_email_to;                            //收件人信箱
    private $_smtp_user;                                //邮箱账号（@163.com之前的部分）
    private $_smtp_password;                            //邮箱密码
    private $_mail_subject;                             //邮箱主题
    private $_mail_body;                                //邮箱正文
    private $_mail_type = 'HTML';               //邮箱格式（HTML/TXT）,TXT为文本邮件

    public function __construct($email_from, $password, $email_to, $subject, $body, $debug = false)
    {

        $this->_smtp_email_from = $email_from;
        $this->_smtp_email_to = $email_to;
        $this->_smtp_password = $password;
        $this->_smtp_user = substr($email_from, 0, strpos($email_from, '@'));
        $this->_mail_subject = $subject;
        $this->_mail_body = $body;

        //调用smtp类，进行邮件发送
        //这里面的一个true是表示使用身份验证,否则不使用身份验证.
        @$smtp = new smtp($this->_smtp_server, $this->_smtp_server_port, true, $this->_smtp_user, $this->_smtp_password);
        //是否显示发送的调试信息(默认不输出调试信息)
        if ($debug) {
            $smtp->debug = TRUE;
        }
        //发送邮件
        @$state = $smtp->sendmail($this->_smtp_email_to, $this->_smtp_email_from, $this->_mail_subject, $this->_mail_body, $this->_mail_type);
        //发送状态
        if ($state == "") {
            echo "对不起，邮件发送失败！请检查邮箱填写是否有误。";
            exit();
        } else {
            echo "恭喜！邮件发送成功！！";
            exit();
        }

    }
}
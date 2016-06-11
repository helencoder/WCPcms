<?php
/**
 * Author: helen
 * CreateTime: 2016/06/07 16:30
 * Description: 自动回复控制器
 */
namespace Home\Controller\Weixin;

use Home\Controller\CommonController;

class ReplayController extends CommonController
{
    /**
     * 自动回复
     */
    public function index()
    {
        //获取微信发送确认的参数。
        $signature = $_GET['signature'];
        $timestamp = $_GET['timestamp'];
        $nonce = $_GET['nonce'];
        $echostr = $_GET['echostr'];
        $token = 'weixin';              //此处需要各家商场自主设置，或者统一要求即可
        //$token = 'zainanjing6tocken';
        $array = array($token, $timestamp, $nonce);
        sort($array);
        $str = sha1(implode($array));
        if ($str == $signature && $echostr) {
            echo $echostr;
            exit;
        } elseif(IS_POST) {
            $this->replyMsg();
        }
    }
    /**
     * 消息回复
     */
    protected function replyMsg()
    {
        //1,获取到微信推送过来post数据（xml格式）
        $postArr = $GLOBALS['HTTP_RAW_POST_DATA'];
        $this->writeContent($postArr);
        //2,处理消息类型，并设置回复类型和内容
        $postObj = simplexml_load_string($postArr);
        if (strtolower($postObj->MsgType) == 'event' && strtolower($postObj->Event) == 'click') {

        } else {
            
        }
    }
}
<?php
/**
 * Author：helen
 * CreateTime: 2016/08/20 10:15
 * Description：开放平台接口控制器
 */
namespace Home\Controller\Test;

use Home\Controller\CommonController;

class CapisController extends CommonController
{
    public function getCapisRes(){
        $appid = '';
        $appsecret = '';
        $resuqestName = 'get_access_token';
        $args = array(
            'grant_type'    => 'client_credential',
            'appid'         => $appid,
            'secret'     => $appsecret
        );
        $capis = aj_data_response($resuqestName, $args);
        dump($capis);
        //dump($capis->access_token);
    }
}
<?php
/**
 * Author：helen
 * CreateTime: 2016/06/28 22:24
 * Description：JSSDK封装类  微信页面授权--(JS-SDK使用权限签名算法)
 */
class JSSDK{
    private $appId;
    private $appSecret;
    public function __construct($appId, $appSecret)
    {
        $this->appId = $appId;
        $this->appSecret = $appSecret;
    }
    /*
     * 获取access_token
     * (需要缓存，可利用数据库存储,不要频繁刷新获取)
     * http请求方式: GET  https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=APPID&secret=APPSECRET
     * 接口请求参数
     *  参数	     是否必须	       说明
        grant_type	是	获取access_token填写client_credential
        appid	    是	第三方用户唯一凭证
        secret	    是	第三方用户唯一凭证密钥，即appsecret
     * 接口返回说明
     * {"access_token":"ACCESS_TOKEN","expires_in":7200}    access_token	获取到的凭证  expires_in	凭证有效时间，单位：秒
     * 接口错误说明
     * {"errcode":40013,"errmsg":"invalid appid"}
     * */
    private function getAccessToken(){
        $appId = $this->appId;
        $appSecret = $this->appSecret;
        $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$appId.'&secret='.$appSecret;
        $res = $this->api_request($url);
        if(isset($res->access_token)){
            return array(
                'errcode'       =>0,
                'errmsg'        =>'success',
                'access_token'  =>$res->access_token,
                'expires_in'    =>$res->expires_in
            );
        }else{
            return array(
                'errcode'       =>$res->errcode,
                'errmsg'        =>$res->errmsg,
                'access_token'  =>null,
                'expires_in'    =>null
            );
        }
    }
    /*
     * 获取jsapi_ticket
     * （有效期7200秒，开发者必须在自己的服务全局缓存jsapi_ticket）
     * 请求方式：https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=ACCESS_TOKEN&type=jsapi
     * 接口返回值：JSON
     * {
            "errcode":0,
            "errmsg":"ok",
            "ticket":"bxLdikRXVbTPdHSM05e5u5sUoXNKd8-41ZO3MhKoyN5OfkWITDGgnr2fwJ0m9E8NYzWKVZvdVtaUgWvsdshFKA",
            "expires_in":7200
        }
     * */
    private function getJsApiTicket(){
        $access_token_data = $this->getAccessToken();
        if($access_token_data['errcode']==0){
            $access_token = $access_token_data['access_token'];
            $url = 'https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token='.$access_token.'&type=jsapi';
            $res = $this->api_request($url);
            if($res->errcode==0){
                return array(
                    'errcode'     =>$res->errcode,
                    'errmsg'      =>$res->errmsg,
                    'ticket'      =>$res->ticket,
                    'expires_in'  =>$res->expires_in
                );
            }else{
                return array(
                    'errcode'     =>$res->errcode,
                    'errmsg'      =>$res->errmsg,
                    'ticket'      =>null,
                    'expires_in'  =>null
                );
            }
        }else{
            return array(
                'errcode'         =>$access_token_data['errcode'],
                'errmsg'          =>$access_token_data['errmsg'],
                'ticket'          =>null,
                'expires_in'      =>null
            );
        }
    }
    /*
     * 签名算法
     * 签名生成规则如下：参与签名的字段包括noncestr（随机字符串）, 有效的jsapi_ticket, timestamp（时间戳）, url（当前网页的URL，不包含#及其后面部分） 。
     * 1、对所有待签名参数按照字段名的ASCII 码从小到大排序（字典序）后，
     * 2、使用URL键值对的格式（即key1=value1&key2=value2…）拼接成字符串string1。
     * 这里需要注意的是所有参数名均为小写字符。对string1作sha1加密，字段名和字段值都采用原始值，不进行URL 转义。
     * */
    /*
     * 获取随机字符串
     * mt_rand() 使用 Mersenne Twister 算法返回随机整数。
     * mt_rand(min,max)如果没有提供可选参数 min 和 max，mt_rand() 返回 0 到 RAND_MAX 之间的伪随机数。
     * 想要 5 到 15（包括 5 和 15）之间的随机数，用 mt_rand(5, 15)。
     * 此函数rand()快四倍
     * */
    /*
     * 1.签名用的noncestr和timestamp必须与wx.config中的nonceStr和timestamp相同。
     * 2.签名用的url必须是调用JS接口页面的完整URL。
     * 3.出于安全考虑，开发者必须在服务器端实现签名的逻辑。
     * 注意：
     * 确保你获取用来签名的url是动态获取的，动态页面可参见实例代码中php的实现方式。
     * 如果是html的静态页面在前端通过ajax将url传到后台签名，前端需要用js获取当前页面除去'#'hash部分的链接（可用location.href.split('#')[0]获取,而且需要encodeURIComponent），
     * 因为页面一旦分享，微信客户端会在你的链接末尾加入其它参数，如果不是动态获取当前链接，将导致分享后的页面签名失败。
     * */
    public function getSignPackage()
    {
        $jsapiTicket_data = $this->getJsApiTicket();
        $nonceStr = $this->getNonceStr();
        $timestamp = time();
        $url = $this->getUrl();
        if($jsapiTicket_data['errcode']==0){
            $jsapiTicket = $jsapiTicket_data['ticket'];
            // 这里参数的顺序要按照 key 值 ASCII 码升序排序
            $string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";
            $signature = sha1($string);
            return  array(
                "appId"         => $this->appId,
                "nonceStr"      => $nonceStr,
                "timestamp"     => $timestamp,
                "url"           => $url,
                "signature"     => $signature,
                "rawString"     => $string,
                "errcode"       => $jsapiTicket_data['errcode'],
                "errmsg"        => $jsapiTicket_data['errmsg']
            );
        }else{
            return  array(
                "appId"         => $this->appId,
                "nonceStr"      => $nonceStr,
                "timestamp"     => $timestamp,
                "url"           => $url,
                "signature"     => null,
                "rawString"     => null,
                "errcode"       => $jsapiTicket_data['errcode'],
                "errmsg"        => $jsapiTicket_data['errmsg']
            );
        }
    }
    /*
     * 获取nonceStr
     * */
    private function getNonceStr($length = 16)
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $nonceStr = "";
        for ($i = 0; $i < $length; $i++) {
            $nonceStr .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $nonceStr;
    }
    /*
     * 获取url
     * url（当前网页的URL，不包含#及其后面部分）
     * */
    private function getUrl(){
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        return $url;
    }
    /*
     * 微信API调用方法
     * */
    private function api_request($url,$data=null){
        //初始化cURL方法
        $ch = curl_init();
        //设置cURL参数（基本参数）
        $opts = array(
            //在局域网内访问https站点时需要设置以下两项，关闭ssl验证！
            //此两项正式上线时需要更改（不检查和验证认证）
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_TIMEOUT => 500,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_URL => $url,
        );
        curl_setopt_array($ch, $opts);
        //post请求参数
        if (!empty($data)) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }
        //执行cURL操作
        $output = curl_exec($ch);
        if (curl_errno($ch)) {    //cURL操作发生错误处理。
            var_dump(curl_error($ch));
            die;
        }
        //关闭cURL
        curl_close($ch);
        $res = json_decode($output);
        return ($res);   //返回json数据
    }
}
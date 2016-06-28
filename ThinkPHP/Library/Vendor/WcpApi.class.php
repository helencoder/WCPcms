<?php
/**
 * Author：helen
 * CreateTime: 2016/06/28 22:29
 * Description：微信公众平台接口API
 */
class WcpApi{
    /**
     * @FunctionDescription:验证开发者服务器url有效性
     * @Param:token(令牌 用户手动输入的配置信息)
     * @Return:echostr（随机字符串）
     * @Description:
     * @Author:helen zheng
     */
    public function valid($token){
        $echostr = $_GET['echostr'];
        if($this->checkSignature($token)){
            echo $echostr;
            exit;
        }
    }
    /**
     * @FunctionDescription:检验signature函数
     * @Param:token(令牌 用户手动输入的配置信息)
     * @Return:true/false
     * @Description:微信服务器发送get请求将signature、timestamp、nonce、echostr四个参数发送到开发者提供的url，利用接收到的参数进行验证。
     * @Author:helen zheng
     */
    function checkSignature($token){
        /*获取微信发送确认的参数。*/
        $signature = $_GET['signature'];    /*微信加密签名，signature结合了开发者填写的token参数和请求中的timestamp参数、nonce参数。*/
        $timestamp = $_GET['timestamp'];    /*时间戳 */
        $nonce = $_GET['nonce'];            /*随机数 */
        $echostr = $_GET['echostr'];        /*随机字符串*/
        /*加密/校验流程*/
        /*1. 将token、timestamp、nonce三个参数进行字典序排序*/
        $array = array($token,$timestamp,$nonce);
        sort($array,SORT_STRING);
        /*2. 将三个参数字符串拼接成一个字符串进行sha1加密*/
        $str = sha1( implode($array) );
        /*3. 开发者获得加密后的字符串可与signature对比，标识该请求来源于微信*/
        if( $str==$signature && $echostr ){
            return ture;
        }else{
            return false;
        }
    }
    /**
     * @FunctionDescription:获取access_token
     * @Param:AppID（第三方用户唯一凭证 ）,AppSecret（第三方用户唯一凭证密钥）
     * @Return:access_token（ string（length=117））
     * @Description:access_token的存储至少要保留512个字符空间。access_token的有效期目前为2个小时，需定时刷新，重复获取将导致上次获取的access_token失效。
     * @Author:helen zheng
     */
    public function getToken($appid,$appsecret){
        $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$appid.'&secret='.$appsecret;
        $result = $this->request_get($url);
        $res = $this->resultProcess($result);
        if($res==$result){  /*接口返回值*/
            return($result);
        }else{  /*接口调用错误信息*/
            return($res);
        }
    }
    /**
     * @FunctionDescription:获取微信服务器的IP地址列表
     * @Param:access_token(公众号的access_token )
     * @Return:
     * @Description:安全验证
     * @Author:helen zheng
     */
    public function getWeixinIP($access_token){
        $url = 'https://api.weixin.qq.com/cgi-bin/getcallbackip?access_token='.$access_token;
        $result = $this->request_get($url);
        $res = $this->resultProcess($result);
        if($res==$result){  /*接口返回值*/
            return($result);
        }else{  /*接口调用错误信息*/
            return($res);
        }
    }
    /**
     * @FunctionDescription:接收消息响应(回复)函数（可与自定义回复接口、语义理解接口、客服接口结合）
     * @Param:
     * @Return:接收消息类型
     * @Description:?当普通微信用户向公众账号发消息时，微信服务器将POST消息的XML数据包到开发者填写的URL上。
     * @Author:helen zheng
     */
    public function responseMsg(){
        /*1,获取到微信推送过来post数据（xml格式）*/
        $postArr = $GLOBALS['HTTP_RAW_POST_DATA'];
        /*2,处理消息类型，并设置回复类型和内容*/
        $postObj = simplexml_load_string($postArr);
        /*判断用户发送消息的类型(普通消息、事件推送)*/
        $MsgType = strtolower($postObj->MsgType);
        $Event = strtolower($postObj->Event);
        if(isset($Event)){  /*事件推送*/
            switch($Event){
                case 'subscribe'            : /*return '订阅事件（扫描带参数二维码事件(用户未关注)）';*/
                    $template = '<xml>
                                    <ToUserName><![CDATA[toUser]]></ToUserName>
                                    <FromUserName><![CDATA[FromUser]]></FromUserName>
                                    <CreateTime>123456789</CreateTime>
                                    <MsgType><![CDATA[event]]></MsgType>
                                    <Event><![CDATA[subscribe]]></Event>
                                </xml>';
                    break;
                case 'unsubscribe'          : /*return '取消订阅事件';*/
                    $template ='<xml>
                                    <ToUserName><![CDATA[toUser]]></ToUserName>
                                    <FromUserName><![CDATA[FromUser]]></FromUserName>
                                    <CreateTime>123456789</CreateTime>
                                    <MsgType><![CDATA[event]]></MsgType>
                                    <Event><![CDATA[unsubscribe]]></Event>
                                </xml>';
                    break;
                case 'scan'                 : /*return '扫描带参数二维码事件(用户已关注)';*/
                    $template = '<xml>
                                    <ToUserName><![CDATA[toUser]]></ToUserName>
                                    <FromUserName><![CDATA[FromUser]]></FromUserName>
                                    <CreateTime>123456789</CreateTime>
                                    <MsgType><![CDATA[event]]></MsgType>
                                    <Event><![CDATA[subscribe]]></Event>
                                    <EventKey><![CDATA[qrscene_123123]]></EventKey>
                                    <Ticket><![CDATA[TICKET]]></Ticket>
                                </xml>';
                    break;
                case 'location'             : /*return '上报地理位置事件';*/
                    $template = '<xml>
                                    <ToUserName><![CDATA[toUser]]></ToUserName>
                                    <FromUserName><![CDATA[fromUser]]></FromUserName>
                                    <CreateTime>123456789</CreateTime>
                                    <MsgType><![CDATA[event]]></MsgType>
                                    <Event><![CDATA[LOCATION]]></Event>
                                    <Latitude>23.137466</Latitude>
                                    <Longitude>113.352425</Longitude>
                                    <Precision>119.385040</Precision>
                                </xml>';
                    break;
                case 'click'                : /*return '自定义菜单事件（点击菜单拉取消息时的事件推送）';*/
                    $template = '<xml>
                                    <ToUserName><![CDATA[toUser]]></ToUserName>
                                    <FromUserName><![CDATA[FromUser]]></FromUserName>
                                    <CreateTime>123456789</CreateTime>
                                    <MsgType><![CDATA[event]]></MsgType>
                                    <Event><![CDATA[CLICK]]></Event>
                                    <EventKey><![CDATA[EVENTKEY]]></EventKey>
                                </xml>';
                    break;
                case 'view'                 : /*return '自定义菜单事件（点击菜单跳转链接时的事件推送）';*/
                    $template = '<xml>
                                    <ToUserName><![CDATA[toUser]]></ToUserName>
                                    <FromUserName><![CDATA[FromUser]]></FromUserName>
                                    <CreateTime>123456789</CreateTime>
                                    <MsgType><![CDATA[event]]></MsgType>
                                    <Event><![CDATA[VIEW]]></Event>
                                    <EventKey><![CDATA[www.qq.com]]></EventKey>
                                </xml>';
                    break;
                case 'scancode_push'        : /*return '自定义菜单事件（扫码推事件的事件推送 ）'*/
                    $template = '<xml>
                                    <ToUserName><![CDATA[gh_e136c6e50636]]></ToUserName>
                                    <FromUserName><![CDATA[oMgHVjngRipVsoxg6TuX3vz6glDg]]></FromUserName>
                                    <CreateTime>1408090502</CreateTime>
                                    <MsgType><![CDATA[event]]></MsgType>
                                    <Event><![CDATA[scancode_push]]></Event>
                                    <EventKey><![CDATA[6]]></EventKey>
                                    <ScanCodeInfo>
                                        <ScanType><![CDATA[qrcode]]></ScanType>
                                        <ScanResult><![CDATA[1]]></ScanResult>
                                    </ScanCodeInfo>
                                </xml>';
                    break;
                case 'scancode_waitmsg'     : /*return '自定义菜单事件（扫码推事件且弹出“消息接收中”提示框的事件推送  ）'*/
                    $template = '<xml>
                                    <ToUserName><![CDATA[gh_e136c6e50636]]></ToUserName>
                                    <FromUserName><![CDATA[oMgHVjngRipVsoxg6TuX3vz6glDg]]></FromUserName>
                                    <CreateTime>1408090606</CreateTime>
                                    <MsgType><![CDATA[event]]></MsgType>
                                    <Event><![CDATA[scancode_waitmsg]]></Event>
                                    <EventKey><![CDATA[6]]></EventKey>
                                    <ScanCodeInfo>
                                        <ScanType><![CDATA[qrcode]]></ScanType>
                                        <ScanResult><![CDATA[2]]></ScanResult>
                                    </ScanCodeInfo>
                                </xml>';
                    break;
                case 'pic_sysphoto'         : /*return '自定义菜单事件（弹出系统拍照发图的事件推送  ）'*/
                    $template = '<xml>
                                    <ToUserName><![CDATA[gh_e136c6e50636]]></ToUserName>
                                    <FromUserName><![CDATA[oMgHVjngRipVsoxg6TuX3vz6glDg]]></FromUserName>
                                    <CreateTime>1408090651</CreateTime>
                                    <MsgType><![CDATA[event]]></MsgType>
                                    <Event><![CDATA[pic_sysphoto]]></Event>
                                    <EventKey><![CDATA[6]]></EventKey>
                                    <SendPicsInfo>
                                        <Count>1</Count>
                                        <PicList>
                                            <item>
                                                <PicMd5Sum><![CDATA[1b5f7c23b5bf75682a53e7b6d163e185]]></PicMd5Sum>
                                            </item>
                                        </PicList>
                                    </SendPicsInfo>
                                </xml>';
                    break;
                case 'pic_photo_or_album'   : /*return '自定义菜单事件（弹出拍照或者相册发图的事件推送 ）'*/
                    $template = '<xml>
                                    <ToUserName><![CDATA[gh_e136c6e50636]]></ToUserName>
                                    <FromUserName><![CDATA[oMgHVjngRipVsoxg6TuX3vz6glDg]]></FromUserName>
                                    <CreateTime>1408090816</CreateTime>
                                    <MsgType><![CDATA[event]]></MsgType>
                                    <Event><![CDATA[pic_photo_or_album]]></Event>
                                    <EventKey><![CDATA[6]]></EventKey>
                                    <SendPicsInfo>
                                        <Count>1</Count>
                                        <PicList>
                                            <item>
                                                <PicMd5Sum><![CDATA[5a75aaca956d97be686719218f275c6b]]></PicMd5Sum>
                                            </item>
                                        </PicList>
                                    </SendPicsInfo>
                                </xml>';
                    break;
                case 'pic_weixin'           : /*return '自定义菜单事件（弹出微信相册发图器的事件推送 ）'*/
                    $template = '<xml>
                                    <ToUserName><![CDATA[gh_e136c6e50636]]></ToUserName>
                                    <FromUserName><![CDATA[oMgHVjngRipVsoxg6TuX3vz6glDg]]></FromUserName>
                                    <CreateTime>1408090816</CreateTime>
                                    <MsgType><![CDATA[event]]></MsgType>
                                    <Event><![CDATA[pic_weixin]]></Event>
                                    <EventKey><![CDATA[6]]></EventKey>
                                    <SendPicsInfo>
                                        <Count>1</Count>
                                        <PicList>
                                            <item>
                                                <PicMd5Sum><![CDATA[5a75aaca956d97be686719218f275c6b]]></PicMd5Sum>
                                            </item>
                                        </PicList>
                                    </SendPicsInfo>
                                </xml>';
                    break;
                case 'location_select'      : /*return '自定义菜单事件（弹出地理位置选择器的事件推送）'*/
                    $template = '<xml>
                                    <ToUserName><![CDATA[gh_e136c6e50636]]></ToUserName>
                                    <FromUserName><![CDATA[oMgHVjngRipVsoxg6TuX3vz6glDg]]></FromUserName>
                                    <CreateTime>1408091189</CreateTime>
                                    <MsgType><![CDATA[event]]></MsgType>
                                    <Event><![CDATA[location_select]]></Event>
                                    <EventKey><![CDATA[6]]></EventKey>
                                    <SendLocationInfo>
                                        <Location_X><![CDATA[23]]></Location_X>
                                        <Location_Y><![CDATA[113]]></Location_Y>
                                        <Scale><![CDATA[15]]></Scale>
                                        <Label><![CDATA[ 广州市海珠区客村艺苑路 106号]]></Label>
                                        <Poiname><![CDATA[]]></Poiname>
                                    </SendLocationInfo>
                                </xml>';
                    break;
                default                     : /*return '未知事件类型';*/
                    break;
            }
        }else{  /*普通消息(自动回复扩展)*/
            switch($MsgType){
                case 'text'       : /*return '文本信息';*/
                    $Content = '您发送的为文本，内容为:'.$postObj->Content;
                    break;
                case 'image'      : /*return '图片消息';*/
                    $Content = '您发送的为图片，图片链接为:'.$postObj->PicUrl;
                    break;
                case 'voice'      : /*return '语音消息';*/
                    $Content = '您发送的为语音，媒体ID为:'.$postObj->MediaId;
                    break;
                case 'video'      : /*return '视频消息';*/
                    $Content = '您发送的为视频，媒体ID为:'.$postObj->MediaId;
                    break;
                case 'shortvideo' : /*return '小视频消息';*/
                    $Content = '您发送的为小视频，媒体ID为:'.$postObj->MediaId;
                    break;
                case 'location'   : /*return '地理位置消息';*/
                    $Content = '您发送的为地理位置消息，位置为: '.$postObj->Label.'纬度为: '.$postObj->Location_X.'经度为: '.$postObj->Location_Y;
                    break;
                case 'link'       : /*return '链接消息';*/
                    $Content = '您发送的为链接消息，标题为: '.$postObj->Title.'内容为: '.$postObj->Description.'链接地址为: '.$postObj->Url;
                    break;
                default           : /*return '未知消息类型';*/
                    $Content = '抱歉，请重新输入！';
                    break;
            }
        }
        /*响应消息*/
        $FromUserName = $postObj->ToUserName;
        $ToUserName   = $postObj->FromUserName;
        $MsgType = 'text';  /*暂时响应均利用文本消息的形式*/
        $CreateTime = time();
        $template = "<xml>
                            <ToUserName><![CDATA[%s]]></ToUserName>
                            <FromUserName><![CDATA[%s]]></FromUserName>
                            <CreateTime>%s</CreateTime>
                            <MsgType><![CDATA[%s]]></MsgType>
                            <Content><![CDATA[%s]]></Content>
                            </xml>";
        $info = sprintf($template,$ToUserName,$FromUserName,$CreateTime,$MsgType,$Content);
        echo $info;
    }
    /**
     * @FunctionDescription:发送（回复）消息
     * @Param:回复消息类型(回复图文消息，需添加第二个参数 类型为array 四个字段（title、description、picUrl、url） )
     * @Return:
     * @Description:根据回复消息选定的类型进行特定类型的回复
     * @Author:helen zheng
     */
    public function transmitMsg($MsgType,$array=null){
        /*1,获取到微信推送过来post数据（xml格式）*/
        $postArr = $GLOBALS['HTTP_RAW_POST_DATA'];
        /*2,处理消息类型，并设置回复类型和内容*/
        $postObj = simplexml_load_string($postArr);
        /*判断用户发送消息的类型(普通消息、事件推送)*/
        /*响应消息*/
        $FromUserName = $postObj->ToUserName;
        $ToUserName   = $postObj->FromUserName;
        $CreateTime = time();
        switch($MsgType){   /*回复消息*/
            case 'text'       : /*return '文本信息';*/
                $Content  = '';
                $template = '<xml>
                                <ToUserName><![CDATA[%s]]></ToUserName>
                                <FromUserName><![CDATA[%s]]></FromUserName>
                                <CreateTime>%s</CreateTime>
                                <MsgType><![CDATA[%s]]></MsgType>
                                <Content><![CDATA[%s]]></Content>
                            </xml>';
                $info = sprintf($template,$ToUserName,$FromUserName,$CreateTime,$MsgType,$Content);
                break;
            case 'image'      : /*return '图片消息';*/
                $MediaId  = '';
                $template = '<xml>
                                <ToUserName><![CDATA[%s]]></ToUserName>
                                <FromUserName><![CDATA[%s]]></FromUserName>
                                <CreateTime>%s</CreateTime>
                                <MsgType><![CDATA[%s]]></MsgType>
                                <Image>
                                    <MediaId><![CDATA[%s]]></MediaId>
                                </Image>
                            </xml>';
                $info = sprintf($template,$ToUserName,$FromUserName,$CreateTime,$MsgType,$MediaId);
                break;
            case 'voice'      : /*return '语音消息';*/
                $MediaId  = '';
                $template = '<xml>
                                <ToUserName><![CDATA[%s]]></ToUserName>
                                <FromUserName><![CDATA[%s]]></FromUserName>
                                <CreateTime>%s</CreateTime>
                                <MsgType><![CDATA[%s]]></MsgType>
                                <Voice>
                                    <MediaId><![CDATA[%s]]></MediaId>
                                </Voice>
                            </xml>';
                $info = sprintf($template,$ToUserName,$FromUserName,$CreateTime,$MsgType,$MediaId);
                break;
            case 'video'      : /*return '视频消息';*/
                $MediaId     = '';
                $Title       = '';
                $Description = '';
                $template = '<xml>
                                <ToUserName><![CDATA[%s]]></ToUserName>
                                <FromUserName><![CDATA[%s]]></FromUserName>
                                <CreateTime>%s</CreateTime>
                                <MsgType><![CDATA[%s]]></MsgType>
                                <Video>
                                    <MediaId><![CDATA[%s]]></MediaId>
                                    <Title><![CDATA[%s]]></Title>
                                    <Description><![CDATA[%s]]></Description>
                                </Video>
                            </xml>';
                $info = sprintf($template,$ToUserName,$FromUserName,$CreateTime,$MsgType,$MediaId,$Title,$Description);
                break;
            case 'music'      : /*return '音乐消息';*/
                $Title        = '';
                $Description  = '';
                $MusicUrl     = '';
                $HQMusicUrl   = '';
                $ThumbMediaId = '';
                $template = '<xml>
                                <ToUserName><![CDATA[%s]]></ToUserName>
                                <FromUserName><![CDATA[%s]]></FromUserName>
                                <CreateTime>%s</CreateTime>
                                <MsgType><![CDATA[%s]]></MsgType>
                                <Music>
                                    <Title><![CDATA[%s]]></Title>
                                    <Description><![CDATA[%s]]></Description>
                                    <MusicUrl><![CDATA[%s]]></MusicUrl>
                                    <HQMusicUrl><![CDATA[%s]]></HQMusicUrl>
                                    <ThumbMediaId><![CDATA[%s]]></ThumbMediaId>
                                </Music>
                            </xml>';
                $info = sprintf($template,$ToUserName,$FromUserName,$CreateTime,$MsgType,$Title,$Description,$MusicUrl,$HQMusicUrl,$ThumbMediaId);
            case 'news'       : /*return '图文消息'(根据传入的数据可发送多条图文消息);*/
                $template = "<xml>
                            <ToUserName><![CDATA[%s]]></ToUserName>
                            <FromUserName><![CDATA[%s]]></FromUserName>
                            <CreateTime>%s</CreateTime>
                            <MsgType><![CDATA[%s]]></MsgType>
                            <ArticleCount>".count($array)."</ArticleCount>
                            <Articles>";
                foreach($array as $key=>$value){
                    $template .="<item>
                                <Title><![CDATA[".$value['title']."]]></Title>
                                <Description><![CDATA[".$value['description']."]]></Description>
                                <PicUrl><![CDATA[".$value['picUrl']."]]></PicUrl>
                                <Url><![CDATA[".$value['url']."]]></Url>
                                </item>";
                }
                $template .="</Articles>
                            </xml> ";
                $info = sprintf( $template, $ToUserName, $FromUserName, $CreateTime, $MsgType );
            default           : return '未知消息类型，请重新输入';
        }
        echo $info;
    }
    /**
     * @FunctionDescription:客服接口
     * @Description:当用户主动发消息给公众号的时候（包括发送信息、点击自定义菜单、订阅事件、扫描二维码事件、支付成功事件、用户维权），微信将会把消息数据推送给开发者，
     * @Description:开发者在一段时间内（目前修改为48小时）可以调用客服消息接口，通过POST一个JSON数据包来发送消息给普通用户，在48小时内不限制发送次数。
     * @Author:helen zheng
     */
    /**
     * @FunctionDescription:添加客服帐号(post)
     * @Param:access_token、custom_service_data(kf_account（完整客服账号，格式为：账号前缀@公众号微信号 ）、nickname（客服昵称，最长6个汉字或12个英文字符 ）、password（客服账号登录密码）)
     * @Return:0 （ok）
     * @Description:每个公众号最多添加10个客服账号
     * @Author:helen zheng
     */
    public function customerServiceAccountAdd($access_token,$custom_service_data){
        $url = 'https://api.weixin.qq.com/customservice/kfaccount/add?access_token='.$access_token;
        $result = $this->request_post($url,$custom_service_data);
        $res = $this->resultProcess($result);
        if($res==$result){  /*接口返回值*/
            return($result);
        }else{  /*接口调用错误信息*/
            return($res);
        }
    }
    /**
     * @FunctionDescription:修改客服账号（post）
     * @Param:access_token、custom_service_data(kf_account（完整客服账号，格式为：账号前缀@公众号微信号 ）、nickname（客服昵称，最长6个汉字或12个英文字符 ）、password（客服账号登录密码）)
     * @Return:0 （ok）
     * @Description:
     * @Author:helen zheng
     */
    public function customerServiceAccountUpdate($access_token,$custom_service_data){
        $url = 'https://api.weixin.qq.com/customservice/kfaccount/update?access_token='.$access_token;
        $result = $this->request_post($url,$custom_service_data);
        $res = $this->resultProcess($result);
        if($res==$result){  /*接口返回值*/
            return($result);
        }else{  /*接口调用错误信息*/
            return($res);
        }
    }
    /**
     * @FunctionDescription:删除客服帐号(get)
     * @Param:
     * @Return:
     * @Description:
     * @Author:helen zheng
     */
    public function customerServiceAccountDelete($access_token){
        $url = 'https://api.weixin.qq.com/customservice/kfaccount/del?access_token='.$access_token;
        $result = $this->request_get($url);
        $res = $this->resultProcess($result);
        if($res==$result){  /*接口返回值*/
            return($result);
        }else{  /*接口调用错误信息*/
            return($res);
        }
    }
    /**
     * @FunctionDescription:设置客服帐号的头像(post)
     * @Param:access_token,data(kf_account(客服账号),img_data(图片))
     * @Return:0 (ok)
     * @Description:调用本接口来上传图片作为客服人员的头像，头像图片文件必须是jpg格式，推荐使用640*640大小的图片以达到最佳效果。
     * @Author:helen zheng
     */
    public function customerServiceAccountImg($access_token,$kf_account,$img_data){
        $url = 'http://api.weixin.qq.com/customservice/kfaccount/uploadheadimg?access_token='.$access_token.'&kf_account='.$kf_account;
        $result = $this->request_post($url,$img_data);
        $res = $this->resultProcess($result);
        if($res==$result){  /*接口返回值*/
            return($result);
        }else{  /*接口调用错误信息*/
            return($res);
        }
    }
    /**
     * @FunctionDescription:获取所有客服账号(get)
     * @Param:access_token
     * @Return:
     * @Description:通过本接口，获取公众号中所设置的客服基本信息，包括客服工号、客服昵称、客服登录账号。
     * @Author:helen zheng
     */
    public function customerServiceAccountList($access_token){
        $url = 'https://api.weixin.qq.com/cgi-bin/customservice/getkflist?access_token='.$access_token;
        $result = $this->request_get($url);
        $res = $this->resultProcess($result);
        if($res==$result){  /*接口返回值*/
            return($result);
        }else{  /*接口调用错误信息*/
            return($res);
        }
    }
    /**
     * @FunctionDescription:客服接口-发消息(post)
     * @Param:access_token、data(touser、msgtype、content、media_id 、thumb_media_id )
     * @Return:
     * @Description:发送消息类型：文本消息、图片消息、语音消息、视频消息、音乐消息、图文消息（点击跳转到外链/图文消息页面 图文消息条数限制在8条以内，注意，如果图文数超过8，则将会无响应。 ）、发送卡券
     * @Author:helen zheng
     */
    public function customerServiceSend($access_token,$data){
        $url = 'https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token='.$access_token;
        $result = $this->request_post($url,$data);
        $res = $this->resultProcess($result);
        if($res==$result){  /*接口返回值*/
            return($result);
        }else{  /*接口调用错误信息*/
            return($res);
        }
    }
    /**
     * @FunctionDescription:高级群发接口
     * @Description:对于认证订阅号，群发接口每天可成功调用1次，此次群发可选择发送给全部用户或某个分组；
     * @Description:对于认证服务号虽然开发者使用高级群发接口的每日调用限制为100次，但是用户每月只能接收4条，无论在公众平台网站上，还是使用接口群发，用户每月只能接收4条群发消息，多于4条的群发将对该用户发送失败；
     * @Author:helen zheng
     */
    /**
     * @FunctionDescription:上传图文消息内的图片获取URL【订阅号与服务号认证后均可用】(post)
     * @Param:  access_token 	是 	调用接口凭证
    media 	        是 	form-data中媒体文件标识，有filename、filelength、content-type等信息
     * @Return: url (上传图片的URL，可用于后续群发中，放置到图文消息中)。
     * @Description:本接口所上传的图片不占用公众号的素材库中图片数量的5000个的限制。图片仅支持jpg/png格式，大小必须在1MB以下。
     * @Author:helen zheng
     */
    public function uploadImg($access_token,$img_data){
        $url = 'https://api.weixin.qq.com/cgi-bin/media/uploadimg?access_token='.$access_token;
        $result = $this->request_post($url,$img_data);
        $res = $this->resultProcess($result);
        if($res==$result){  /*接口返回值*/
            return($result);
        }else{  /*接口调用错误信息*/
            return($res);
        }
    }
    /**
     * @FunctionDescription:上传图文消息素材【订阅号与服务号认证后均可用】(post)
     * @Param:  Articles 	        是 	图文消息，一个图文消息支持1到8条图文
    thumb_media_id 	    是 	图文消息缩略图的media_id，可以在基础支持-上传多媒体文件接口中获得
    author 	            否 	图文消息的作者
    title 	            是 	图文消息的标题
    content_source_url 	否 	在图文消息页面点击“阅读原文”后的页面
    content 	        是 	图文消息页面的内容，支持HTML标签。具备微信支付权限的公众号，可以使用a标签，其他公众号不能使用
    digest 	            否 	图文消息的描述
    show_cover_pic 	    否 	是否显示封面，1为显示，0为不显示
     * @Return: type 	    媒体文件类型，分别有图片（image）、语音（voice）、视频（video）和缩略图（thumb），次数为news，即图文消息
    media_id 	媒体文件/图文消息上传后获取的唯一标识
    created_at 	媒体文件上传时间
     * @Description:
     * @Author:helen zheng
     */
    public function uploadNews($access_token,$news_data){
        $url = 'https://api.weixin.qq.com/cgi-bin/media/uploadnews?access_token='.$access_token;
        $result = $this->request_post($url,$news_data);
        $res = $this->resultProcess($result);
        if($res==$result){  /*接口返回值*/
            return($result);
        }else{  /*接口调用错误信息*/
            return($res);
        }
    }
    /**
     * @FunctionDescription:根据分组进行群发【订阅号与服务号认证后均可用】(post)
     * @Param:  filter 	        是 	用于设定图文消息的接收者
    is_to_all 	    否 	用于设定是否向全部用户发送，值为true或false，选择true该消息群发给所有用户，选择false可根据group_id发送给指定群组的用户
    group_id 	    否 	群发到的分组的group_id，参加用户管理中用户分组接口，若is_to_all值为true，可不填写group_id
    mpnews 	        是 	用于设定即将发送的图文消息
    media_id 	    是 	用于群发的消息的media_id
    msgtype 	    是 	群发的消息类型，图文消息为mpnews，文本消息为text，语音为voice，音乐为music，图片为image，视频为video，卡券为wxcard
    title 	        否 	消息的标题
    description 	否 	消息的描述
    thumb_media_id 	是 	视频缩略图的媒体ID
     * @Return: type 	      媒体文件类型，分别有图片（image）、语音（voice）、视频（video）和缩略图（thumb），图文消息为news
    errcode 	  错误码
    errmsg 	      错误信息
    msg_id 	      消息发送任务的ID
    msg_data_id   消息的数据ID，该字段只有在群发图文消息时，才会出现。可以用于在图文分析数据接口中，获取到对应的图文消息的数据，是图文分析数据接口中的msgid字段中的前半部分，详见图文分析数据接口中的msgid字段的介绍。
     * @Description:
     * @Author:helen zheng
     */
    public function sendallByGroups($access_token,$data){
        $url = 'https://api.weixin.qq.com/cgi-bin/message/mass/sendall?access_token='.$access_token;
        $result = $this->request_post($url,$data);
        $res = $this->resultProcess($result);
        if($res==$result){  /*接口返回值*/
            return($result);
        }else{  /*接口调用错误信息*/
            return($res);
        }
    }
    /**
     * @FunctionDescription:根据OpenID列表群发【订阅号不可用，服务号认证后可用】(post)
     * @Param:  touser 	        是 	填写图文消息的接收者，一串OpenID列表，OpenID最少2个，最多10000个
    mpnews 	        是 	用于设定即将发送的图文消息
    media_id 	    是 	用于群发的图文消息的media_id
    msgtype 	    是 	群发的消息类型，图文消息为mpnews，文本消息为text，语音为voice，音乐为music，图片为image，视频为video，卡券为wxcard
    title 	        否 	消息的标题
    description 	否 	消息的描述
    thumb_media_id 	是 	视频缩略图的媒体ID
     * @Return: type 	        媒体文件类型，分别有图片（image）、语音（voice）、视频（video）和缩略图（thumb），次数为news，即图文消息
    errcode 	    错误码
    errmsg 	        错误信息
    msg_id 	        消息发送任务的ID
    msg_data_id 	消息的数据ID，，该字段只有在群发图文消息时，才会出现。可以用于在图文分析数据接口中，获取到对应的图文消息的数据，是图文分析数据接口中的msgid字段中的前半部分，详见图文分析数据接口中的msgid字段的介绍。
     * @Description:
     * @Author:helen zheng
     */
    public function sendallByOpenID($access_token,$data){
        $url = 'https://api.weixin.qq.com/cgi-bin/message/mass/send?access_token='.$access_token;
        $result = $this->request_post($url,$data);
        $res = $this->resultProcess($result);
        if($res==$result){  /*接口返回值*/
            return($result);
        }else{  /*接口调用错误信息*/
            return($res);
        }
    }
    /**
     * @FunctionDescription:删除群发【订阅号与服务号认证后均可用】(post)
     * @Param: msg_id 	是 	发送出去的消息ID
     * @Return: 0 (ok)
     * @Description:群发只有在刚发出的半小时内可以删除，发出半小时之后将无法被删除。
     * @Author:helen zheng
     */
    public function sendallDelete($access_token,$msg_id){
        $url = 'https://api.weixin.qq.com/cgi-bin/message/mass/delete?access_token='.$access_token;
        $result = $this->request_post($url,$msg_id);
        $res = $this->resultProcess($result);
        if($res==$result){  /*接口返回值*/
            return($result);
        }else{  /*接口调用错误信息*/
            return($res);
        }
    }
    /**
     * @FunctionDescription:预览接口【订阅号与服务号认证后均可用】(post)
     * @Param:  touser 	    接收消息用户对应该公众号的openid，该字段也可以改为towxname，以实现对微信号的预览
    msgtype 	群发的消息类型，图文消息为mpnews，文本消息为text，语音为voice，音乐为music，图片为image，视频为video，卡券为wxcard
    media_id 	用于群发的消息的media_id
    content 	发送文本消息时文本的内容
     * @Return: msg_id 	    消息ID
     * @Description:开发者可通过该接口发送消息给指定用户，在手机端查看消息的样式和排版。为了满足第三方平台开发者的需求，在保留对openID预览能力的同时，增加了对指定微信号发送预览的能力，但该能力每日调用次数有限制（100次），请勿滥用。
     * @Author:helen zheng
     */
    public function preview($access_token,$data){
        $url = 'https://api.weixin.qq.com/cgi-bin/message/mass/preview?access_token='.$access_token;
        $result = $this->request_post($url,$data);
        $res = $this->resultProcess($result);
        if($res==$result){  /*接口返回值*/
            return($result);
        }else{  /*接口调用错误信息*/
            return($res);
        }
    }
    /**
     * @FunctionDescription:查询群发消息发送状态【订阅号与服务号认证后均可用】(post)
     * @Param:  msg_id 	    群发消息后返回的消息id
     * @Return: msg_id 	    群发消息后返回的消息id
    msg_status 	消息发送后的状态，SEND_SUCCESS表示发送成功
     * @Description:
     * @Author:helen zheng
     */
    public function sendallStatus($access_token,$msg_id){
        $url = 'https://api.weixin.qq.com/cgi-bin/message/mass/get?access_token='.$access_token;
        $result = $this->request_post($url,$msg_id);
        $res = $this->resultProcess($result);
        if($res==$result){  /*接口返回值*/
            return($result);
        }else{  /*接口调用错误信息*/
            return($res);
        }
    }
    /**
     * @FunctionDescription:模板消息接口(post)
     * @Description:模板消息仅用于公众号向用户发送重要的服务通知，只能用于符合其要求的服务场景中.
     * @Description:只有认证后的服务号才可以申请模板消息的使用权限并获得该权限；
     * @Author:helen zheng
     */
    /**
     * @FunctionDescription:设置所属行业(post)
     * @Param:  industry_id1 	是 	公众号模板消息所属行业编号
    industry_id2 	是 	公众号模板消息所属行业编号
     * @Return:
     * @Description:设置行业可在MP中完成，每月可修改行业1次，账号仅可使用所属行业中相关的模板
     * @Author:helen zheng
     */
    public function setIndustry($access_token,$data){
        $url = 'https://api.weixin.qq.com/cgi-bin/template/api_set_industry?access_token='.$access_token;
        $result = $this->request_post($url,$data);
        $res = $this->resultProcess($result);
        if($res==$result){  /*接口返回值*/
            return($result);
        }else{  /*接口调用错误信息*/
            return($res);
        }
    }
    /**
     * @FunctionDescription:获得模板ID(post)
     * @Param: template_id_short 	是 	模板库中模板的编号，有“TM**”和“OPENTMTM**”等形式
     * @Return:template_id
     * @Description:
     * @Author:helen zheng
     */
    public function getTemplateId($access_token,$template_id_short){
        $url = 'https://api.weixin.qq.com/cgi-bin/template/api_add_template?access_token='.$access_token;
        $result = $this->request_post($url,$template_id_short);
        $res = $this->resultProcess($result);
        if($res==$result){  /*接口返回值*/
            return($result);
        }else{  /*接口调用错误信息*/
            return($res);
        }
    }
    /**
     * @FunctionDescription:发送模板消息(post)
     * @Param:
     * @Return: msgid
     * @Description:
     * @Author:helen zheng
     */
    public function sendTemplateMsg($access_token,$data){
        $url = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token='.$access_token;
        $result = $this->request_post($url,$data);
        $res = $this->resultProcess($result);
        if($res==$result){  /*接口返回值*/
            return($result);
        }else{  /*接口调用错误信息*/
            return($res);
        }
    }
    /**
     * @FunctionDescription:获取自动回复规则(get)
     * @Param:
     * @Return: is_add_friend_reply_open 	    关注后自动回复是否开启，0代表未开启，1代表开启
    is_autoreply_open 	            消息自动回复是否开启，0代表未开启，1代表开启
    add_friend_autoreply_info 	    关注后自动回复的信息
    type 	                        自动回复的类型。关注后自动回复和消息自动回复的类型仅支持文本（text）、图片（img）、语音（voice）、视频（video），关键词自动回复则还多了图文消息（news）
    content 	                    对于文本类型，content是文本内容，对于图文、图片、语音、视频类型，content是mediaID
    message_default_autoreply_info 	消息自动回复的信息
    keyword_autoreply_info 	        关键词自动回复的信息
    rule_name 	                    规则名称
    create_time 	                创建时间
    reply_mode 	                    回复模式，reply_all代表全部回复，random_one代表随机回复其中一条
    keyword_list_info 	            匹配的关键词列表
    match_mode 	                    匹配模式，contain代表消息中含有该关键词即可，equal表示消息内容必须和关键词严格相同
    news_info 	                    图文消息的信息
    title 	                        图文消息的标题
    digest 	                        摘要
    author 	                        作者
    show_cover 	                    是否显示封面，0为不显示，1为显示
    cover_url 	                    封面图片的URL
    content_url 	                正文的URL
    source_url 	                    原文的URL，若置空则无查看原文入口
     * @Description:开发者可以通过该接口，获取公众号当前使用的自动回复规则，包括关注后自动回复、消息自动回复（60分钟内触发一次）、关键词自动回复。
     * @Author:helen zheng
     */
    public function getCurrentAutoreplyInfo($access_token){
        $url = 'https://api.weixin.qq.com/cgi-bin/get_current_autoreply_info?access_token='.$access_token;
        $result = $this->request_get($url);
        $res = $this->resultProcess($result);
        if($res==$result){  /*接口返回值*/
            return($result);
        }else{  /*接口调用错误信息*/
            return($res);
        }
    }
    /**
     * @FunctionDescription:素材管理
     * @Description:对多媒体文件、多媒体消息的获取和调用等操作，是通过media_id来进行的。
     * @Description:素材管理接口对所有认证的订阅号和服务号开放（注：自定义菜单接口和素材管理接口向第三方平台旗下未认证订阅号开放）。
     * @Description:图片大小不超过2M，支持bmp/png/jpeg/jpg/gif格式，语音大小不超过5M，长度不超过60秒，支持mp3/wma/wav/amr格式
     * @Author:helen zheng
     */
    /**
     * @FunctionDescription:新增临时素材（本接口即为原“上传多媒体文件”接口。）（post）
     * @Param:  access_token 	是 	调用接口凭证
    type 	        是 	媒体文件类型，分别有图片（image）、语音（voice）、视频（video）和缩略图（thumb）
    media 	        是 	form-data中媒体文件标识，有filename、filelength、content-type等信息
     * @Return: type 	    媒体文件类型，分别有图片（image）、语音（voice）、视频（video）和缩略图（thumb，主要用于视频与音乐格式的缩略图）
    media_id 	媒体文件上传后，获取时的唯一标识
    created_at 	媒体文件上传时间戳
     * @Description:对于临时素材，每个素材（media_id）会在开发者上传或粉丝发送到微信服务器3天后自动删除。media_id是可复用的。
     * @Description:上传的临时多媒体文件有格式和大小限制，如下：
    图片（image）: 1M，支持JPG格式
    语音（voice）：2M，播放长度不超过60s，支持AMR\MP3格式
    视频（video）：10MB，支持MP4格式
    缩略图（thumb）：64KB，支持JPG格式
     * @Author:helen zheng
     */
    public function mediaUpload($access_token,$type,$data){
        $url = 'https://api.weixin.qq.com/cgi-bin/media/upload?access_token='.$access_token.'&type='.$type;
        $result = $this->request_post($url,$data);
        $res = $this->resultProcess($result);
        if($res==$result){  /*接口返回值*/
            return($result);
        }else{  /*接口调用错误信息*/
            return($res);
        }
    }
    /**
     * @FunctionDescription:获取临时素材 (本接口即为原“下载多媒体文件”接口。)(get)
     * @Param:  access_token 	是 	调用接口凭证
    media_id 	    是 	媒体文件ID
     * @Return:
     * @Description:使用本接口获取临时素材（即下载临时的多媒体文件）。请注意，视频文件不支持https下载，调用该接口需http协议。
     * @Author:helen zheng
     */
    public function mediaGet($access_token,$media_id){
        $url = 'https://api.weixin.qq.com/cgi-bin/media/get?access_token='.$access_token.'&media_id='.$media_id;
        $result = $this->request_get($url);
        $res = $this->resultProcess($result);
        if($res==$result){  /*接口返回值*/
            return($result);
        }else{  /*接口调用错误信息*/
            return($res);
        }
    }
    /**
     * @FunctionDescription:新增永久图文素材(post)
     * @Param:  title 	            是 	标题
    thumb_media_id 	    是 	图文消息的封面图片素材id（必须是永久mediaID）
    author 	            是 	作者
    digest 	            是 	图文消息的摘要，仅有单图文消息才有摘要，多图文此处为空
    show_cover_pic 	    是 	是否显示封面，0为false，即不显示，1为true，即显示
    content 	        是 	图文消息的具体内容，支持HTML标签，必须少于2万字符，小于1M，且此处会去除JS
    content_source_url 	是 	图文消息的原文地址，即点击“阅读原文”后的URL
     * @Return: media_id 返回的即为新增的图文消息素材的media_id。
     * @Description:永久素材的数量是有上限的，请谨慎新增。图文消息素材和图片素材的上限为5000，其他类型为1000.素材的格式大小等要求与公众平台官网一致。
     * @Author:helen zheng
     */
    public function addPermanentGraphicMaterial($access_token,$news_data){
        $url = 'https://api.weixin.qq.com/cgi-bin/material/add_news?access_token='.$access_token;
        $result = $this->request_post($url,$news_data);
        $res = $this->resultProcess($result);
        if($res==$result){  /*接口返回值*/
            return($result);
        }else{  /*接口调用错误信息*/
            return($res);
        }
    }
    /**
     * @FunctionDescription:新增其他类型永久素材(post)
     * @Param:  access_token 	是 	调用接口凭证
    type 	        是 	媒体文件类型，分别有图片（image）、语音（voice）、视频（video）和缩略图（thumb）
    media 	        是 	form-data中媒体文件标识，有filename、filelength、content-type等信息
     * @Return:
     * @Description:通过POST表单来调用接口，表单id为media，包含需要上传的素材内容，有filename、filelength、content-type等信息。
     * @Description:请注意：图片素材将进入公众平台官网素材管理模块中的默认分组。
     * @Author:helen zheng
     */
    public function addPermanentOtherMaterial($access_token,$data){
        $url = 'https://api.weixin.qq.com/cgi-bin/material/add_material?access_token=ACCESS_TOKEN';
        $result = $this->request_post($url,$data);
        $res = $this->resultProcess($result);
        if($res==$result){  /*接口返回值*/
            return($result);
        }else{  /*接口调用错误信息*/
            return($res);
        }
    }
    /**
     * @FunctionDescription:获取永久素材(post)
     * @Param:  access_token 	是 	调用接口凭证
    media_id 	    是 	要获取的素材的media_id
     * @Return: title 	            图文消息的标题
    thumb_media_id 	    图文消息的封面图片素材id（必须是永久mediaID）
    show_cover_pic 	    是否显示封面，0为false，即不显示，1为true，即显示
    author 	            作者
    digest 	            图文消息的摘要，仅有单图文消息才有摘要，多图文此处为空
    content 	        图文消息的具体内容，支持HTML标签，必须少于2万字符，小于1M，且此处会去除JS
    url 	            图文页的URL
    content_source_url 	图文消息的原文地址，即点击“阅读原文”后的URL
     * @Description:
     * @Author:helen zheng
     */
    public function getPermanentMaterial($access_token,$media_id){
        $url = 'https://api.weixin.qq.com/cgi-bin/material/get_material?access_token='.$access_token;
        $result = $this->request_post($url,$media_id);
        $res = $this->resultProcess($result);
        if($res==$result){  /*接口返回值*/
            return($result);
        }else{  /*接口调用错误信息*/
            return($res);
        }
    }
    /**
     * @FunctionDescription:删除永久素材(post)
     * @Param:  access_token 	是 	调用接口凭证
    media_id 	    是 	要获取的素材的media_id
     * @Return:
     * @Description:
     * @Author:helen zheng
     */
    public function deletePermanentMaterial($access_token,$media_id){
        $url = 'https://api.weixin.qq.com/cgi-bin/material/del_material?access_token='.$access_token;
        $result = $this->request_post($url,$media_id);
        $res = $this->resultProcess($result);
        if($res==$result){  /*接口返回值*/
            return($result);
        }else{  /*接口调用错误信息*/
            return($res);
        }
    }
    /**
     * @FunctionDescription:修改永久图文素材(post)
     * @Param:  media_id 	        是 	要修改的图文消息的id
    index 	            是 	要更新的文章在图文消息中的位置（多图文消息时，此字段才有意义），第一篇为0
    title 	            是 	标题
    thumb_media_id 	    是 	图文消息的封面图片素材id（必须是永久mediaID）
    author 	            是 	作者
    digest 	            是 	图文消息的摘要，仅有单图文消息才有摘要，多图文此处为空
    show_cover_pic 	    是 	是否显示封面，0为false，即不显示，1为true，即显示
    content 	        是 	图文消息的具体内容，支持HTML标签，必须少于2万字符，小于1M，且此处会去除JS
    content_source_url 	是 	图文消息的原文地址，即点击“阅读原文”后的URL
     * @Return: 0 (ok)
     * @Description:
     * @Author:helen zheng
     */
    public function updatePermanentGraphicMaterial($access_token,$news_data){
        $url = 'https://api.weixin.qq.com/cgi-bin/material/update_news?access_token='.$access_token;
        $result = $this->request_post($url,$news_data);
        $res = $this->resultProcess($result);
        if($res==$result){  /*接口返回值*/
            return($result);
        }else{  /*接口调用错误信息*/
            return($res);
        }
    }
    /**
     * @FunctionDescription:获取素材总数(get)
     * @Param:
     * @Return: voice_count 	语音总数量
    video_count 	视频总数量
    image_count 	图片总数量
    news_count 	    图文总数量
     * @Description:开发者可以根据本接口来获取永久素材的列表.1.永久素材的总数，也会计算公众平台官网素材管理中的素材
    2.图片和图文消息素材（包括单图文和多图文）的总数上限为5000，其他素材的总数上限为1000
    3.调用该接口需https协议
     * @Author:helen zheng
     */
    public function getPermanentMaterialCount($access_token){
        $url = 'https://api.weixin.qq.com/cgi-bin/material/get_materialcount?access_token='.$access_token;
        $result = $this->request_get($url);
        $res = $this->resultProcess($result);
        if($res==$result){  /*接口返回值*/
            return($result);
        }else{  /*接口调用错误信息*/
            return($res);
        }
    }
    /**
     * @FunctionDescription:获取素材列表
     * @Param:  type 	是 	素材的类型，图片（image）、视频（video）、语音 （voice）、图文（news）
    offset 	是 	从全部素材的该偏移位置开始返回，0表示从第一个素材 返回
    count 	是 	返回素材的数量，取值在1到20之间
     * @Return: total_count 	    该类型的素材的总数
    item_count 	        本次调用获取的素材的数量
    title 	            图文消息的标题
    thumb_media_id 	    图文消息的封面图片素材id（必须是永久mediaID）
    show_cover_pic 	    是否显示封面，0为false，即不显示，1为true，即显示
    author 	            作者
    digest 	            图文消息的摘要，仅有单图文消息才有摘要，多图文此处为空
    content 	        图文消息的具体内容，支持HTML标签，必须少于2万字符，小于1M，且此处会去除JS
    url 	            图文页的URL，或者，当获取的列表是图片素材列表时，该字段是图片的URL
    content_source_url 	图文消息的原文地址，即点击“阅读原文”后的URL
    update_time 	    这篇图文消息素材的最后更新时间
    name 	            文件名称
     * @Description:开发者可以分类型获取永久素材的列表。
     * @Author:helen zheng
     */
    public function getPermanentMaterialList($access_token,$data){
        $url = 'https://api.weixin.qq.com/cgi-bin/material/batchget_material?access_token='.$access_token;
        $result = $this->request_post($url,$data);
        $res = $this->resultProcess($result);
        if($res==$result){  /*接口返回值*/
            return($result);
        }else{  /*接口调用错误信息*/
            return($res);
        }
    }
    /**
     * @FunctionDescription:用户管理
     * @Description:开发者可以使用接口，对公众平台的分组进行查询、创建、修改、删除等操作，也可以使用接口在需要时移动用户到某个分组。
     * @Author:helen zheng
     */
    /**
     * @FunctionDescription:创建分组(post)
     * @Param:  access_token 	调用接口凭证
    name 	        分组名字（30个字符以内）
     * @Return: id 	            分组id，由微信分配
    name 	        分组名字，UTF8编码
     * @Description:一个公众账号，最多支持创建100个分组。
     * @Author:helen zheng
     */
    public function createGroups($access_token,$group_data){
        $url = 'https://api.weixin.qq.com/cgi-bin/groups/create?access_token='.$access_token;
        $result = $this->request_post($url,$group_data);
        $res = $this->resultProcess($result);
        if($res==$result){  /*接口返回值*/
            return($result);
        }else{  /*接口调用错误信息*/
            return($res);
        }
    }
    /**
     * @FunctionDescription:查询所有分组(get)
     * @Param:  access_token 	调用接口凭证
     * @Return: groups 	        公众平台分组信息列表
    id 	            分组id，由微信分配
    name 	        分组名字，UTF8编码
    count 	        分组内用户数量
     * @Description:
     * @Author:helen zheng
     */
    public function getGroups($access_token){
        $url = 'https://api.weixin.qq.com/cgi-bin/groups/get?access_token='.$access_token;
        $result = $this->request_get($url);
        $res = $this->resultProcess($result);
        if($res==$result){  /*接口返回值*/
            return($result);
        }else{  /*接口调用错误信息*/
            return($res);
        }
    }
    /**
     * @FunctionDescription:查询用户所在分组(post)
     * @Param:  access_token 	调用接口凭证
    openid 	        用户的OpenID
     * @Return: groupid 	    用户所属的groupid
     * @Description:
     * @Author:helen zheng
     */
    public function getGroupId($access_token,$openid){
        $url = 'https://api.weixin.qq.com/cgi-bin/groups/getid?access_token='.$access_token;
        $result = $this->request_post($url,$openid);
        $res = $this->resultProcess($result);
        if($res==$result){  /*接口返回值*/
            return($result);
        }else{  /*接口调用错误信息*/
            return($res);
        }
    }
    /**
     * @FunctionDescription:修改分组名(post)
     * @Param:  access_token 	调用接口凭证
    id 	            分组id，由微信分配
    name 	        分组名字（30个字符以内）
     * @Return:
     * @Description:
     * @Author:helen zheng
     */
    public function updateGroupsName($access_token,$group_data){
        $url = 'https://api.weixin.qq.com/cgi-bin/groups/update?access_token='.$access_token;
        $result = $this->request_post($url,$group_data);
        $res = $this->resultProcess($result);
        if($res==$result){  /*接口返回值*/
            return($result);
        }else{  /*接口调用错误信息*/
            return($res);
        }
    }
    /**
     * @FunctionDescription:移动用户分组(post)
     * @Param:  access_token 	调用接口凭证
    openid 	        用户唯一标识符
    to_groupid 	    分组id
     * @Return:
     * @Description:
     * @Author:helen zheng
     */
    public function updateGroupsUser($access_token,$user_data){
        $url = 'https://api.weixin.qq.com/cgi-bin/groups/members/update?access_token='.$access_token;
        $result = $this->request_post($url,$user_data);
        $res = $this->resultProcess($result);
        if($res==$result){  /*接口返回值*/
            return($result);
        }else{  /*接口调用错误信息*/
            return($res);
        }
    }
    /**
     * @FunctionDescription:批量移动用户分组(post)
     * @Param:  access_token 	调用接口凭证
    openid_list 	用户唯一标识符openid的列表（size不能超过50）
    to_groupid 	    分组id
     * @Return:
     * @Description:
     * @Author:helen zheng
     */
    public function batchupdateGroupsUser($access_token,$user_data){
        $url = 'https://api.weixin.qq.com/cgi-bin/groups/members/batchupdate?access_token='.$access_token;
        $result = $this->request_post($url,$user_data);
        $res = $this->resultProcess($result);
        if($res==$result){  /*接口返回值*/
            return($result);
        }else{  /*接口调用错误信息*/
            return($res);
        }
    }
    /**
     * @FunctionDescription:删除分组(post)
     * @Param:  access_token 	调用接口凭证
    group 	        分组
    id 	            分组的id
     * @Return:
     * @Description:本接口是删除一个用户分组，删除分组后，所有该分组内的用户自动进入默认分组。
     * @Author:helen zheng
     */
    public function deleteGroups($access_token,$group_data){
        $url = 'https://api.weixin.qq.com/cgi-bin/groups/delete?access_token='.$access_token;
        $result = $this->request_post($url,$group_data);
        $res = $this->resultProcess($result);
        if($res==$result){  /*接口返回值*/
            return($result);
        }else{  /*接口调用错误信息*/
            return($res);
        }
    }
    /**
     * @FunctionDescription:设置用户备注名(post)
     * @Param:  access_token 	调用接口凭证
    openid 	        用户标识
    remark 	        新的备注名，长度必须小于30字符
     * @Return:
     * @Description:开发者可以通过该接口对指定用户设置备注名，该接口暂时开放给微信认证的服务号。
     * @Author:helen zheng
     */
    public function updateUserRemark($access_token,$user_data){
        $url = 'https://api.weixin.qq.com/cgi-bin/user/info/updateremark?access_token='.$access_token;
        $result = $this->request_post($url,$user_data);
        $res = $this->resultProcess($result);
        if($res==$result){  /*接口返回值*/
            return($result);
        }else{  /*接口调用错误信息*/
            return($res);
        }
    }
    /**
     * @FunctionDescription:获取用户基本信息（包括UnionID机制）(get)
     * @Param:  access_token 	是 	调用接口凭证
    openid 	        是 	普通用户的标识，对当前公众号唯一
    lang 	        否 	返回国家地区语言版本，zh_CN 简体，zh_TW 繁体，en 英语
     * @Return: subscribe 	    用户是否订阅该公众号标识，值为0时，代表此用户没有关注该公众号，拉取不到其余信息。
    openid 	        用户的标识，对当前公众号唯一
    nickname 	    用户的昵称
    sex 	        用户的性别，值为1时是男性，值为2时是女性，值为0时是未知
    city 	        用户所在城市
    country 	    用户所在国家
    province 	    用户所在省份
    language 	    用户的语言，简体中文为zh_CN
    headimgurl 	    用户头像，最后一个数值代表正方形头像大小（有0、46、64、96、132数值可选，0代表640*640正方形头像），用户没有头像时该项为空。若用户更换头像，原有头像URL将失效。
    subscribe_time 	用户关注时间，为时间戳。如果用户曾多次关注，则取最后关注时间
    unionid 	    只有在用户将公众号绑定到微信开放平台帐号后，才会出现该字段。详见：获取用户个人信息（UnionID机制）
    remark 	        公众号运营者对粉丝的备注，公众号运营者可在微信公众平台用户管理界面对粉丝添加备注
    groupid 	    用户所在的分组ID
     * @Description:在关注者与公众号产生消息交互后，公众号可获得关注者的OpenID（加密后的微信号，每个用户对每个公众号的OpenID是唯一的。对于不同公众号，同一用户的openid不同）。
     * @Description:公众号可通过本接口来根据OpenID获取用户基本信息，包括昵称、头像、性别、所在城市、语言和关注时间。
     * @Description:如果开发者有在多个公众号，或在公众号、移动应用之间统一用户帐号的需求，需要前往微信开放平台（open.weixin.qq.com）绑定公众号后，才可利用UnionID机制来满足上述需求。
     * @Author:helen zheng
     */
    public function getUserInfo($access_token,$openid){
        $url = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$access_token.'&openid='.$openid.'&lang=zh_CN';
        $result = $this->request_get($url);
        $res = $this->resultProcess($result);
        if($res==$result){  /*接口返回值*/
            return($result);
        }else{  /*接口调用错误信息*/
            return($res);
        }
    }
    /**
     * @FunctionDescription:批量获取用户基本信息(post)
     * @Param:  openid 	是 	用户的标识，对当前公众号唯一
     * @Return: 同上
     * @Description:开发者可通过该接口来批量获取用户基本信息。最多支持一次拉取100条。
     * @Author:helen zheng
     */
    public function batchgetUserInfo($access_token,$user_list){
        $url = 'https://api.weixin.qq.com/cgi-bin/user/info/batchget?access_token='.$access_token;
        $result = $this->request_post($url,$user_list);
        $res = $this->resultProcess($result);
        if($res==$result){  /*接口返回值*/
            return($result);
        }else{  /*接口调用错误信息*/
            return($res);
        }
    }
    /**
     * @FunctionDescription:获取用户列表(get)
     * @Param:  access_token 	是 	调用接口凭证
    next_openid 	是 	第一个拉取的OPENID，不填默认从头开始拉取
     * @Return: total 	        关注该公众账号的总用户数
    count 	        拉取的OPENID个数，最大值为10000
    data 	        列表数据，OPENID的列表
    next_openid 	拉取列表的最后一个用户的OPENID
     * @Description:公众号可通过本接口来获取帐号的关注者列表，关注者列表由一串OpenID（加密后的微信号，每个用户对每个公众号的OpenID是唯一的）组成。
     * @Description:一次拉取调用最多拉取10000个关注者的OpenID，可以通过多次拉取的方式来满足需求。
     * @Author:helen zheng
     */
    public function getUserList($access_token,$next_openid=null){
        $url = 'https://api.weixin.qq.com/cgi-bin/user/get?access_token='.$access_token.'&next_openid='.$next_openid;
        $result = $this->request_get($url);
        $res = $this->resultProcess($result);
        if($res==$result){  /*接口返回值*/
            return($result);
        }else{  /*接口调用错误信息*/
            return($res);
        }
    }
    /**
     * @FunctionDescription:自定义菜单接口
     * @Description:自定义菜单最多包括3个一级菜单，每个一级菜单最多包含5个二级菜单。一级菜单最多4个汉字，二级菜单最多7个汉字.
     * @Description:创建自定义菜单后，由于微信客户端缓存，需要24小时微信客户端才会展现出来
     * @Author:helen zheng
     */
    /**
     * @FunctionDescription:自定义菜单创建接口（post）
     * @Param:menu_data( button(一级菜单数组)、sub_button[二级菜单数组]、type(菜单的响应动作类型 )、name (菜单标题，不超过16个字节，子菜单不超过40个字节 ) )
     * @Param:menu_data(key (click等点击类型必须 、菜单KEY值，用于消息接口推送，不超过128字节 )、url(view类型必须 网页链接，用户点击菜单可打开链接，不超过256字节 )、media_id (media_id类型和view_limited类型必须 调用新增永久素材接口返回的合法media_id ) )
     * @Return:0 （ok）
     * @Description:按钮类型：click：点击推事件;view：跳转URL;scancode_push：扫码推事件;scancode_waitmsg：扫码推事件且弹出“消息接收中”提示框;pic_sysphoto：弹出系统拍照发图
     * @Description:按钮类型：pic_photo_or_album：弹出拍照或者相册发图;pic_weixin：弹出微信相册发图器;location_select：弹出地理位置选择器;media_id：下发消息（除文本消息）;view_limited：跳转图文消息URL
     * @Author:helen zheng
     */
    public function customMenuEdit($menu_data,$access_token){
        $url = 'https://api.weixin.qq.com/cgi-bin/menu/create?access_token='.$access_token;
        $result = $this->request_post($url,$menu_data);
        $res = $this->resultProcess($result);
        if($res==$result){  /*接口返回值*/
            return($result);
        }else{  /*接口调用错误信息*/
            return($res);
        }
    }
    /**
     * @FunctionDescription:自定义菜单查询接口（get）
     * @Param:access_token
     * @Return:自定义菜单信息
     * @Description:查询自定义菜单的结构。
     * @Author:helen zheng
     */
    public function customMenuSearch($access_token){
        $url = 'https://api.weixin.qq.com/cgi-bin/menu/get?access_token='.$access_token;
        $result = $this->request_get($url);
        $res = $this->resultProcess($result);
        if($res==$result){  /*接口返回值*/
            return($result);
        }else{  /*接口调用错误信息*/
            return($res);
        }
    }
    /**
     * @FunctionDescription:自定义菜单删除接口（get）
     * @Param:access_token
     * @Return:0 （ok）
     * @Description:删除当前使用的自定义菜单。
     * @Author:helen zheng
     */
    public function customMenuDelete($access_token){
        $url = 'https://api.weixin.qq.com/cgi-bin/menu/delete?access_token='.$access_token;
        $result = $this->request_get($url);
        $res = $this->resultProcess($result);
        if($res==$result){  /*接口返回值*/
            return($result);
        }else{  /*接口调用错误信息*/
            return($res);
        }
    }
    /**
     * @FunctionDescription:获取自定义菜单配置接口(get)
     * @Param:access_token
     * @Return:is_menu_open(菜单是否开启，0代表未开启，1代表开启 )、selfmenu_info(菜单信息 )、button (菜单按钮 )、type (菜单的类型)、name (菜单名称 )、value、url、key等字段
     * @Return:news_info(图文消息的信息 )、title(图文消息的标题 )、digest(摘要 )、author (作者)、show_cover (是否显示封面，0为不显示，1为显示 )、cover_url( 封面图片的URL )、content_url( 正文的URL )、source_url（ 原文的URL，若置空则无查看原文入口）
     * @Description:本接口将会提供公众号当前使用的自定义菜单的配置，如果公众号是通过API调用设置的菜单，则返回菜单的开发配置，而如果公众号是在公众平台官网通过网站功能发布菜单，则本接口返回运营者设置的菜单配置。
     * @Author:helen zheng
     */
    public function customMenuList($access_token){
        $url = 'https://api.weixin.qq.com/cgi-bin/get_current_selfmenu_info?access_token='.$access_token;
        $result = $this->request_get($url);
        $res = $this->resultProcess($result);
        if($res==$result){  /*接口返回值*/
            return($result);
        }else{  /*接口调用错误信息*/
            return($res);
        }
    }
    /**
     * @FunctionDescription:账号管理
     * @Description:生成带参数的二维码、长链接转短链接接口、微信认证事件推送
     * @Author:helen zheng
     */
    /**
     * @FunctionDescription:创建二维码ticket
     * @Param:  expire_seconds 	该二维码有效时间，以秒为单位。 最大不超过2592000（即30天），此字段如果不填，则默认有效期为30秒。
    action_name 	二维码类型，QR_SCENE为临时,QR_LIMIT_SCENE为永久,QR_LIMIT_STR_SCENE为永久的字符串参数值
    action_info 	二维码详细信息
    scene_id 	    场景值ID，临时二维码时为32位非0整型，永久二维码时最大值为100000（目前参数只支持1--100000）
    scene_str 	    场景值ID（字符串形式的ID），字符串类型，长度限制为1到64，仅永久二维码支持此字段
     * @Return: ticket 	        获取的二维码ticket，凭借此ticket可以在有效时间内换取二维码。
    expire_seconds 	该二维码有效时间，以秒为单位。 最大不超过2592000（即30天）。
    url 	        二维码图片解析后的地址，开发者可根据该地址自行生成需要的二维码图片
     * @Description:
     * @Author:helen zheng
     */
    public function createQrcodeTicket($access_token,$qrcode_data){
        $url = 'https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token='.$access_token;
        $result = $this->request_post($url,$qrcode_data);
        $res = $this->resultProcess($result);
        if($res==$result){  /*接口返回值*/
            return($result);
        }else{  /*接口调用错误信息*/
            return($res);
        }
    }
    /**
     * @FunctionDescription:通过ticket换取二维码
     * @Param:ticket
     * @Return:
     * @Description:获取二维码ticket后，开发者可用ticket换取二维码图片。TICKET记得进行UrlEncode
     * @Author:helen zheng
     */
    public function getQrcode($ticket){
        $url = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket='.$ticket;
        $result = $this->downloadFile($url);
        $res = $this->resultProcess($result);
        if($res==$result){  /*接口返回值*/
            return($result);
        }else{  /*接口调用错误信息*/
            return($res);
        }
    }
    /**
     * @FunctionDescription:长链接转短链接接口（post）
     * @Param:  access_token 	是 	调用接口凭证
    action 	        是 	此处填long2short，代表长链接转短链接
    long_url 	    是 	需要转换的长链接，支持http://、https://、weixin://wxpay 格式的url
     * @Return: short_url 	    短链接。
     * @Description:将一条长链接转成短链接。主要使用场景： 开发者用于生成二维码的原链接（商品、支付二维码等）太长导致扫码速度和成功率下降，将原长链接通过此接口转成短链接再生成二维码将大大提升扫码速度和成功率。
     * @Description:
     * @Author:helen zheng
     */
    public function shortUrl($access_token,$data){
        $url = 'https://api.weixin.qq.com/cgi-bin/shorturl?access_token='.$access_token;
        $result = $this->request_post($url,$data);
        $res = $this->resultProcess($result);
        if($res==$result){  /*接口返回值*/
            return($result);
        }else{  /*接口调用错误信息*/
            return($res);
        }
    }
    /**
     * @FunctionDescription:数据统计
     * @Description:用户分析数据接口、图文分析数据接口、消息分析数据接口、接口分析数据接口
     * @Author:helen zheng
     */
    /**
     * @FunctionDescription:获取用户增减数据(post)
     * @Param:  access_token 	是 	调用接口凭证
    begin_date 	    是 	获取数据的起始日期，begin_date和end_date的差值需小于“最大时间跨度”（比如最大时间跨度为1时，begin_date和end_date的差值只能为0，才能小于1），否则会报错
    end_date 	    是 	获取数据的结束日期，end_date允许设置的最大值为昨日
     * @Return: ref_date 	    数据的日期
    user_source 	用户的渠道，数值代表的含义如下：0代表其他（包括带参数二维码） 3代表扫二维码 17代表名片分享 35代表搜号码（即微信添加朋友页的搜索） 39代表查询微信公众帐号 43代表图文页右上角菜单
    new_user 	    新增的用户数量
    cancel_user 	取消关注的用户数量，new_user减去cancel_user即为净增用户数量
    cumulate_user 	总用户量
     * @Description:最大时间跨度（7）
     * @Author:helen zheng
     */
    public function getUserSummary($access_token,$begin_date,$end_date){
        $url = 'https://api.weixin.qq.com/datacube/getusersummary?access_token='.$access_token;
        $data = array(
            "begin_date"=>$begin_date,
            "end_date"=>$end_date
        );
        $data = json_encode($data);
        $result = $this->request_post($url,$data);
        $res = $this->resultProcess($result);
        if($res==$result){  /*接口返回值*/
            return($result);
        }else{  /*接口调用错误信息*/
            return($res);
        }
    }
    /**
     * @FunctionDescription:获取累计用户数据(post)
     * @Param:同上
     * @Return:同上
     * @Description:最大时间跨度（7）
     * @Author:helen zheng
     */
    public function getUserCumulate($access_token,$begin_date,$end_date){
        $url = 'https://api.weixin.qq.com/datacube/getusercumulate?access_token='.$access_token;
        $data = array(
            "begin_date"=>$begin_date,
            "end_date"=>$end_date
        );
        $data = json_encode($data);
        $result = $this->request_post($url,$data);
        $res = $this->resultProcess($result);
        if($res==$result){  /*接口返回值*/
            return($result);
        }else{  /*接口调用错误信息*/
            return($res);
        }
    }
    /**
     * @FunctionDescription:获取图文群发每日数据(post)
     * @Param:  access_token 	是 	调用接口凭证
    begin_date 	    是 	获取数据的起始日期，begin_date和end_date的差值需小于“最大时间跨度”（比如最大时间跨度为1时，begin_date和end_date的差值只能为0，才能小于1），否则会报错
    end_date 	    是 	获取数据的结束日期，end_date允许设置的最大值为昨日
     * @Return: ref_date 	        数据的日期，需在begin_date和end_date之间
    ref_hour 	        数据的小时，包括从000到2300，分别代表的是[000,100)到[2300,2400)，即每日的第1小时和最后1小时
    stat_date 	        统计的日期，在getarticletotal接口中，ref_date指的是文章群发出日期， 而stat_date是数据统计日期
    msgid 	            请注意：这里的msgid实际上是由msgid（图文消息id，这也就是群发接口调用后返回的msg_data_id）和index（消息次序索引）组成， 例如12003_3， 其中12003是msgid，即一次群发的消息的id； 3为index，假设该次群发的图文消息共5个文章（因为可能为多图文），3表示5个中的第3个
    title 	            图文消息的标题
    int_page_read_user 	图文页（点击群发图文卡片进入的页面）的阅读人数
    int_page_read_count 图文页的阅读次数
    ori_page_read_user 	原文页（点击图文页“阅读原文”进入的页面）的阅读人数，无原文页时此处数据为0
    ori_page_read_count 原文页的阅读次数
    share_scene 	    分享的场景   1代表好友转发 2代表朋友圈 3代表腾讯微博 255代表其他
    share_user 	        分享的人数
    share_count 	    分享的次数
    add_to_fav_user 	收藏的人数
    add_to_fav_count 	收藏的次数
    target_user 	    送达人数，一般约等于总粉丝数（需排除黑名单或其他异常情况下无法收到消息的粉丝）
    user_source 	    在获取图文阅读分时数据时才有该字段，代表用户从哪里进入来阅读该图文。0:会话;1.好友;2.朋友圈;3.腾讯微博;4.历史消息页;5.其他
     * @Description:最大时间跨度(1)
     * @Author:helen zheng
     */
    public function getArticleSummary($access_token,$begin_date,$end_date){
        $url = 'https://api.weixin.qq.com/datacube/getarticlesummary?access_token='.$access_token;
        $data = array(
            "begin_date"=>$begin_date,
            "end_date"=>$end_date
        );
        $data = json_encode($data);
        $result = $this->request_post($url,$data);
        $res = $this->resultProcess($result);
        if($res==$result){  /*接口返回值*/
            return($result);
        }else{  /*接口调用错误信息*/
            return($res);
        }
    }
    /**
     * @FunctionDescription:获取图文群发总数据(post)
     * @Param:同上
     * @Return:同上
     * @Description:最大时间跨度(1)
     * @Author:helen zheng
     */
    public function getArticleTotal($access_token,$begin_date,$end_date){
        $url = 'https://api.weixin.qq.com/datacube/getarticletotal?access_token='.$access_token;
        $data = array(
            "begin_date"=>$begin_date,
            "end_date"=>$end_date
        );
        $data = json_encode($data);
        $result = $this->request_post($url,$data);
        $res = $this->resultProcess($result);
        if($res==$result){  /*接口返回值*/
            return($result);
        }else{  /*接口调用错误信息*/
            return($res);
        }
    }
    /**
     * @FunctionDescription:获取图文统计数据(post)
     * @Param:同上
     * @Return:同上
     * @Description:最大时间跨度(3)
     * @Author:helen zheng
     */
    public function getUserRead($access_token,$begin_date,$end_date){
        $url = 'https://api.weixin.qq.com/datacube/getuserread?access_token='.$access_token;
        $data = array(
            "begin_date"=>$begin_date,
            "end_date"=>$end_date
        );
        $data = json_encode($data);
        $result = $this->request_post($url,$data);
        $res = $this->resultProcess($result);
        if($res==$result){  /*接口返回值*/
            return($result);
        }else{  /*接口调用错误信息*/
            return($res);
        }
    }
    /**
     * @FunctionDescription:获取图文统计分时数据(post)
     * @Param:同上
     * @Return:同上
     * @Description:最大时间跨度(1)
     * @Author:helen zheng
     */
    public function getUserReadHour($access_token,$begin_date,$end_date){
        $url = 'https://api.weixin.qq.com/datacube/getuserreadhour?access_token='.$access_token;
        $data = array(
            "begin_date"=>$begin_date,
            "end_date"=>$end_date
        );
        $data = json_encode($data);
        $result = $this->request_post($url,$data);
        $res = $this->resultProcess($result);
        if($res==$result){  /*接口返回值*/
            return($result);
        }else{  /*接口调用错误信息*/
            return($res);
        }
    }
    /**
     * @FunctionDescription:获取图文分享转发数据(post)
     * @Param:同上
     * @Return:同上
     * @Description:最大时间跨度(7)
     * @Author:helen zheng
     */
    public function getUserShare($access_token,$begin_date,$end_date){
        $url = 'https://api.weixin.qq.com/datacube/getusershare?access_token='.$access_token;
        $data = array(
            "begin_date"=>$begin_date,
            "end_date"=>$end_date
        );
        $data = json_encode($data);
        $result = $this->request_post($url,$data);
        $res = $this->resultProcess($result);
        if($res==$result){  /*接口返回值*/
            return($result);
        }else{  /*接口调用错误信息*/
            return($res);
        }
    }
    /**
     * @FunctionDescription:获取图文分享转发分时数据(post)
     * @Param:同上
     * @Return:同上
     * @Description:最大时间跨度(1)
     * @Author:helen zheng
     */
    public function getUserShareHour($access_token,$begin_date,$end_date){
        $url = 'https://api.weixin.qq.com/datacube/getusersharehour?access_token='.$access_token;
        $data = array(
            "begin_date"=>$begin_date,
            "end_date"=>$end_date
        );
        $data = json_encode($data);
        $result = $this->request_post($url,$data);
        $res = $this->resultProcess($result);
        if($res==$result){  /*接口返回值*/
            return($result);
        }else{  /*接口调用错误信息*/
            return($res);
        }
    }
    /**
     * @FunctionDescription:获取消息发送概况数据(post)
     * @Param:  access_token 	是 	调用接口凭证
    begin_date 	    是 	获取数据的起始日期，begin_date和end_date的差值需小于“最大时间跨度”（比如最大时间跨度为1时，begin_date和end_date的差值只能为0，才能小于1），否则会报错
    end_date 	    是 	获取数据的结束日期，end_date允许设置的最大值为昨日
     * @Return: ref_date 	        数据的日期，需在begin_date和end_date之间
    ref_hour 	        数据的小时，包括从000到2300，分别代表的是[000,100)到[2300,2400)，即每日的第1小时和最后1小时
    msg_type 	        消息类型，代表含义如下：1代表文字 2代表图片 3代表语音 4代表视频 6代表第三方应用消息（链接消息）
    msg_user 	        上行发送了（向公众号发送了）消息的用户数
    msg_count 	        上行发送了消息的消息总数
    count_interval 	    当日发送消息量分布的区间，0代表 “0”，1代表“1-5”，2代表“6-10”，3代表“10次以上”
    int_page_read_count 图文页的阅读次数
    ori_page_read_user 	原文页（点击图文页“阅读原文”进入的页面）的阅读人数，无原文页时此处数据为0
     * @Description:最大时间跨度(7)
     * @Author:helen zheng
     */
    public function getUpStreamMsg($access_token,$begin_date,$end_date){
        $url = 'https://api.weixin.qq.com/datacube/getupstreammsg?access_token='.$access_token;
        $data = array(
            "begin_date"=>$begin_date,
            "end_date"=>$end_date
        );
        $data = json_encode($data);
        $result = $this->request_post($url,$data);
        $res = $this->resultProcess($result);
        if($res==$result){  /*接口返回值*/
            return($result);
        }else{  /*接口调用错误信息*/
            return($res);
        }
    }
    /**
     * @FunctionDescription:获取消息分送分时数据(post)
     * @Param:同上
     * @Return:同上
     * @Description:最大时间跨度(1)
     * @Author:helen zheng
     */
    public function getUpstreamMsgHour($access_token,$begin_date,$end_date){
        $url = 'https://api.weixin.qq.com/datacube/getupstreammsghour?access_token='.$access_token;
        $data = array(
            "begin_date"=>$begin_date,
            "end_date"=>$end_date
        );
        $data = json_encode($data);
        $result = $this->request_post($url,$data);
        $res = $this->resultProcess($result);
        if($res==$result){  /*接口返回值*/
            return($result);
        }else{  /*接口调用错误信息*/
            return($res);
        }
    }
    /**
     * @FunctionDescription:获取消息发送周数据(post)
     * @Param:同上
     * @Return:同上
     * @Description:最大时间跨度(30)
     * @Author:helen zheng
     */
    public function getUpstreamMsgWeek($access_token,$begin_date,$end_date){
        $url = 'https://api.weixin.qq.com/datacube/getupstreammsgweek?access_token='.$access_token;
        $data = array(
            "begin_date"=>$begin_date,
            "end_date"=>$end_date
        );
        $data = json_encode($data);
        $result = $this->request_post($url,$data);
        $res = $this->resultProcess($result);
        if($res==$result){  /*接口返回值*/
            return($result);
        }else{  /*接口调用错误信息*/
            return($res);
        }
    }
    /**
     * @FunctionDescription:获取消息发送月数据(post)
     * @Param:同上
     * @Return:同上
     * @Description:最大时间跨度(30)
     * @Author:helen zheng
     */
    public function getUpstreamMsgMonth($access_token,$begin_date,$end_date){
        $url = 'https://api.weixin.qq.com/datacube/getupstreammsgmonth?access_token='.$access_token;
        $data = array(
            "begin_date"=>$begin_date,
            "end_date"=>$end_date
        );
        $data = json_encode($data);
        $result = $this->request_post($url,$data);
        $res = $this->resultProcess($result);
        if($res==$result){  /*接口返回值*/
            return($result);
        }else{  /*接口调用错误信息*/
            return($res);
        }
    }
    /**
     * @FunctionDescription:获取消息发送分布数据(post)
     * @Param:同上
     * @Return:同上
     * @Description:最大时间跨度(15)
     * @Author:helen zheng
     */
    public function getUpstreamMsgDist($access_token,$begin_date,$end_date){
        $url = 'https://api.weixin.qq.com/datacube/getupstreammsgdist?access_token='.$access_token;
        $data = array(
            "begin_date"=>$begin_date,
            "end_date"=>$end_date
        );
        $data = json_encode($data);
        $result = $this->request_post($url,$data);
        $res = $this->resultProcess($result);
        if($res==$result){  /*接口返回值*/
            return($result);
        }else{  /*接口调用错误信息*/
            return($res);
        }
    }
    /**
     * @FunctionDescription:获取消息发送分布周数据(post)
     * @Param:同上
     * @Return:同上
     * @Description:最大时间跨度(30)
     * @Author:helen zheng
     */
    public function getUpstreamMsgDistWeek($access_token,$begin_date,$end_date){
        $url = 'https://api.weixin.qq.com/datacube/getupstreammsgdistweek?access_token='.$access_token;
        $data = array(
            "begin_date"=>$begin_date,
            "end_date"=>$end_date
        );
        $data = json_encode($data);
        $result = $this->request_post($url,$data);
        $res = $this->resultProcess($result);
        if($res==$result){  /*接口返回值*/
            return($result);
        }else{  /*接口调用错误信息*/
            return($res);
        }
    }
    /**
     * @FunctionDescription:获取消息发送分布月数据(post)
     * @Param:同上
     * @Return:同上
     * @Description:最大时间跨度(30)
     * @Author:helen zheng
     */
    public function getUpstreamMsgDistMonth($access_token,$begin_date,$end_date){
        $url = 'https://api.weixin.qq.com/datacube/getupstreammsgdistmonth?access_token='.$access_token;
        $data = array(
            "begin_date"=>$begin_date,
            "end_date"=>$end_date
        );
        $data = json_encode($data);
        $result = $this->request_post($url,$data);
        $res = $this->resultProcess($result);
        if($res==$result){  /*接口返回值*/
            return($result);
        }else{  /*接口调用错误信息*/
            return($res);
        }
    }
    /**
     * @FunctionDescription:获取接口分析数据(post)
     * @Param:  access_token 	是 	调用接口凭证
    begin_date 	    是 	获取数据的起始日期，begin_date和end_date的差值需小于“最大时间跨度”（比如最大时间跨度为1时，begin_date和end_date的差值只能为0，才能小于1），否则会报错
    end_date 	    是 	获取数据的结束日期，end_date允许设置的最大值为昨日
     * @Return: ref_date 	    数据的日期
    ref_hour 	    数据的小时
    callback_count 	通过服务器配置地址获得消息后，被动回复用户消息的次数
    fail_count 	    上述动作的失败次数
    total_time_cost 总耗时，除以callback_count即为平均耗时
    max_time_cost 	最大耗时
     * @Description:最大时间跨度(30)
     * @Author:helen zheng
     */
    public function getInterfaceSummary($access_token,$begin_date,$end_date){
        $url = 'https://api.weixin.qq.com/datacube/getinterfacesummary?access_token='.$access_token;
        $data = array(
            "begin_date"=>$begin_date,
            "end_date"=>$end_date
        );
        $data = json_encode($data);
        $result = $this->request_post($url,$data);
        $res = $this->resultProcess($result);
        if($res==$result){  /*接口返回值*/
            return($result);
        }else{  /*接口调用错误信息*/
            return($res);
        }
    }
    /**
     * @FunctionDescription:获取接口分析分时数据(post)
     * @Param:
     * @Return:
     * @Description:最大时间跨度(1)
     * @Author:helen zheng
     */
    public function getInterfaceSummaryHour($access_token,$begin_date,$end_date){
        $url = 'https://api.weixin.qq.com/datacube/getinterfacesummaryhour?access_token='.$access_token;
        $data = array(
            "begin_date"=>$begin_date,
            "end_date"=>$end_date
        );
        $data = json_encode($data);
        $result = $this->request_post($url,$data);
        $res = $this->resultProcess($result);
        if($res==$result){  /*接口返回值*/
            return($result);
        }else{  /*接口调用错误信息*/
            return($res);
        }
    }
    /**
     * @FunctionDescription:接口调用结果处理函数（判断接口调用成功与否并处理）
     * @Param:接口调用返回值（json）
     * @Return:结果处理后信息（json或string）
     * @Description:假如接口调用成功，则本函数正常返回值；假如接口调用失败，返回错误信息。
     * @Author:helen zheng
     */
    function resultProcess($res){
        if(!empty($res->errcode)){
            return ($this->errorMsg($res->errcode));
        }else{
            return $res;
        }
    }
    /**
     * @FunctionDescription:微信全局返回码中文说明
     * @Param:微信返回码
     * @Return:微信返回码对应的中文说明
     * @Description:
     * @Author:helen zheng
     */
    function errorMsg($errcode) {
        switch ($errcode) {
            case -1    : return '系统繁忙，请稍候再试。';
            case 0     : return '请求成功。';
            case 40001 : return '获取access_token时AppSecret错误，或者access_token无效。';
            case 40002 : return '不合法的凭证类型。';
            case 40003 : return '不合法的OpenID，请开发者确认OpenID（该用户）是否已关注公众号，或是否是其他公众号的OpenID。';
            case 40004 : return '不合法的媒体文件类型';
            case 40005 : return '不合法的文件类型';
            case 40006 : return '不合法的文件大小';
            case 40007 : return '不合法的媒体文件id ';
            case 40008 : return '不合法的消息类型 ';
            case 40009 : return '不合法的图片文件大小';
            case 40010 : return '不合法的语音文件大小';
            case 40011 : return '不合法的视频文件大小';
            case 40012 : return '不合法的缩略图文件大小';
            case 40013 : return '不合法的APPID';
            case 40014 : return '不合法的access_token ';
            case 40015 : return '不合法的菜单类型 ';
            case 40016 : return '不合法的按钮个数 ';
            case 40017 : return '不合法的按钮个数';
            case 40018 : return '不合法的按钮名字长度';
            case 40019 : return '不合法的按钮KEY长度 ';
            case 40020 : return '不合法的按钮URL长度 ';
            case 40021 : return '不合法的菜单版本号';
            case 40022 : return '不合法的子菜单级数';
            case 40023 : return '不合法的子菜单按钮个数';
            case 40024 : return '不合法的子菜单按钮类型';
            case 40025 : return '不合法的子菜单按钮名字长度';
            case 40026 : return '不合法的子菜单按钮KEY长度 ';
            case 40027 : return '不合法的子菜单按钮URL长度 ';
            case 40028 : return '不合法的自定义菜单使用用户';
            case 40029 : return '不合法的oauth_code';
            case 40030 : return '不合法的refresh_token';
            case 40031 : return '不合法的openid列表 ';
            case 40032 : return '不合法的openid列表长度 ';
            case 40033 : return '不合法的请求字符，不能包含\uxxxx格式的字符 ';
            case 40035 : return '不合法的参数';
            case 40038 : return '不合法的请求格式';
            case 40039 : return '不合法的URL长度 ';
            case 40050 : return '不合法的分组id';
            case 40051 : return '分组名字不合法';
            case 41001 : return '缺少access_token参数';
            case 41002 : return '缺少appid参数';
            case 41003 : return '缺少refresh_token参数';
            case 41004 : return '缺少secret参数';
            case 41005 : return '缺少多媒体文件数据';
            case 41006 : return '缺少media_id参数';
            case 41007 : return '缺少子菜单数据';
            case 41008 : return '缺少oauth code';
            case 41009 : return '缺少openid';
            case 42001 : return 'access_token超时';
            case 42002 : return 'refresh_token超时';
            case 42003 : return 'oauth_code超时';
            case 43001 : return '需要GET请求';
            case 43002 : return '需要POST请求';
            case 43003 : return '需要HTTPS请求';
            case 43004 : return '需要接收者关注';
            case 43005 : return '需要好友关系';
            case 44001 : return '多媒体文件为空';
            case 44002 : return 'POST的数据包为空';
            case 44003 : return '图文消息内容为空';
            case 44004 : return '文本消息内容为空';
            case 45001 : return '多媒体文件大小超过限制';
            case 45002 : return '消息内容超过限制';
            case 45003 : return '标题字段超过限制';
            case 45004 : return '描述字段超过限制';
            case 45005 : return '链接字段超过限制';
            case 45006 : return '图片链接字段超过限制';
            case 45007 : return '语音播放时间超过限制';
            case 45008 : return '图文消息超过限制';
            case 45009 : return '接口调用超过限制';
            case 45010 : return '创建菜单个数超过限制';
            case 45015 : return '回复时间超过限制';
            case 45016 : return '系统分组，不允许修改';
            case 45017 : return '分组名字过长';
            case 45018 : return '分组数量超过上限';
            case 46001 : return '不存在媒体数据';
            case 46002 : return '不存在的菜单版本';
            case 46003 : return '不存在的菜单数据';
            case 46004 : return '不存在的用户';
            case 47001 : return '解析JSON/XML内容错误';
            case 48001 : return 'api功能未授权';
            case 50001 : return '用户未授权该api';
            default    : return '未知错误';
        }
    }
    /**
     * @FunctionDescription:接口调用的get方法
     * @Param:请求的url地址
     * @Return:（json）
     * @Description:利用cURL发送get请求，获取数据
     * @Author:helen zheng
     */
    /*接口调用的get方法*/
    function request_get($url){
        //初始化cURL方法
        $ch = curl_init();
        //设置cURL参数
        $opts = array(
            //在局域网内访问https站点时需要设置以下两项，关闭ssl验证！
            //此两项正式上线时需要更改（不检查和验证认证）
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_URL            => $url,
        );
        curl_setopt_array($ch,$opts);
        //执行cURL操作
        $output = curl_exec($ch);
        if(curl_errno($ch)){    //cURL发生错误处理操作
            var_dump(curl_error($ch));
            die;
        }
        //关闭cURL
        curl_close($ch);
        $res = json_decode($output);
        return($res);    //返回json数据
    }
    /**
     * @FunctionDescription:接口调用的post方法
     * @Param:请求的url地址，post数据（json格式）
     * @Return:（json）
     * @Description:利用cURL发送get请求，获取数据
     * @Author:helen zheng
     */
    function request_post($url,$data){
        //初始化cURL方法
        $ch = curl_init();
        //设置cURL参数
        $opts = array(
            //在局域网内访问https站点时需要设置以下两项，关闭ssl验证！
            //此两项正式上线时需要更改（不检查和验证认证）
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_URL            => $url,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $data
        );
        curl_setopt_array($ch,$opts);
        //执行cURL操作
        $output = curl_exec($ch);
        if(curl_errno($ch)){    //cURL操作发生错误处理。
            var_dump(curl_error($ch));
            die;
        }
        //关闭cURL
        curl_close($ch);
        $res = json_decode($output);
        return($res);   //返回json数据
    }
    /**
     * @FunctionDescription:下载多媒体文件方法
     * @Param:url
     * @Return:多媒体信息 array
     * @Description:
     * @Author:helen zheng
     */
    function downloadFile($url){
        //初始化cURL方法
        $ch = curl_init();
        //设置cURL参数
        $opts = array(
            //在局域网内访问https站点时需要设置以下两项，关闭ssl验证！
            //此两项正式上线时需要更改（不检查和验证认证）
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_URL            => $url,
            CURLOPT_HEADER         => 0,
            CURLOPT_NOBODY         => 0
        );
        curl_setopt_array($ch,$opts);
        //执行cURL操作
        $output = curl_exec($ch);
        $httpinfo = curl_getinfo($ch);
        if(curl_errno($ch)){
            var_dump(curl_error($ch));
        }
        //关闭cURL
        curl_close($ch);
        return array_merge(array('body'=>$output),array('header'=>$httpinfo));
    }
}
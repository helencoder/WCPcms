<?php
/**
 * Author：helen
 * CreateTime: 2016/06/30 22:02
 * Description：gzip网页压缩
 */
namespace Home\Controller\Content;

use Home\Controller\CommonController;

class GzipController extends CommonController
{
    public function test()
    {
        //header("Content-Encoding: gzip");
        //ob_start('ob_gzip');
        ob_start('ob_gzhandler');
    }

    // 示例1
    function ob_gzip ($content) // $content 就是要压缩的页面内容，或者说饼干原料
    {
        if (! headers_sent() &&     // 如果页面头部信息还没有输出
            extension_loaded("zlib") &&     // 而且zlib扩展已经加载到PHP中
            strstr($_SERVER["HTTP_ACCEPT_ENCODING"], "gzip"))     // 而且浏览器说它可以接受GZIP的页面
        {
            $content = gzencode($content . " \n//此页已压缩", 9); // 此页已压缩”的注释标签，然后用zlib提供的gzencode()函数执行级别为9的压缩，这个参数值范围是0-9，0表示无压缩，9表示最大压缩，当然压缩程度越高越费CPU。

            // 然后用header()函数给浏览器发送一些头部信息，告诉浏览器这个页面已经用GZIP压缩过了！
            header("Content-Encoding: gzip");
            header("Vary: Accept-Encoding");
            header("Content-Length: " . strlen($content));
        }
        return $content; // 返回压缩的内容，或者说把压缩好的饼干送回工作台。
    }

    // 示例2
    #ob_gzhandler 为php内置函数，具体参考手册
    //ob_start('ob_gzhandler');


}
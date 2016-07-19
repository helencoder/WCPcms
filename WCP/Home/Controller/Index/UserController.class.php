<?php
/**
 * Author: helen
 * CreateTime: 2016/4/19 10:34
 * description: 网站用户控制器
 */
namespace Home\Controller\Index;

use Home\Controller\CommonController;

class UserController extends CommonController
{
    // 网站用户记录增加测试
    public function addWebsiteUser()
    {
        /*$websiteUserTable = D('website_user');
        $count = $websiteUserTable->getCount();
        dump($count);*/
        $className = $this->getClassName();
        dump($className);

        $args = $this->verifyArgs();
        dump($args);

    }

    /**
     * 获取当前类的类名
     */
    function getClassName()
    {
        $class = __CLASS__;
        $classArr = explode('\\', $class);
        return $classArr[count($classArr) - 1];
    }

    /**
     * 获取参数数组、用于正则等验证(包括大写字母数组、小写字母数组、数字数组)
     * 方法：
     *      chr()函数可把ASCII码转换成普通字符
     *      1.0-9的ASCII码为48-57
     *      2.a-z的ASCII码为97-122
     *      3.A-Z的ASCII码为65-90
     */
    function verifyArgs()
    {
        $args = array(
            'lowerCase' => array(),
            'upperCase' => array(),
            'number' => array()
        );
        // 处理方式1 (直接字符串增加)
        /*for ($i = 'a', $j = 'A'; $i < 'z' , $j < 'Z'; $i++, $j++) {
            array_push($args['lowerCase'], $i);
            array_push($args['upperCase'], $j);
        }
        array_push($args['lowerCase'], 'z');
        array_push($args['upperCase'], 'Z');*/

        // 处理方式2 (ASCII码转换)
        for ($i = 65, $j = 97; $i < 91 ,$j < 123; $i++, $j++) {
            array_push($args['upperCase'], chr($i));
            array_push($args['lowerCase'], chr($j));
        }

        // 处理数字数组(此处貌似有更好方法)
        for ($k = 0; $k <= 9; $k++) {
            array_push($args['number'], $k);
        }

        return $args;
    }
}
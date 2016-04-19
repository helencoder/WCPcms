<?php
return array(
    //'配置项'=>'配置值'

    /* 数据库设置 */
    'DB_TYPE' => 'mysql',     // 数据库类型
    'DB_HOST' => 'localhost', // 服务器地址
    'DB_NAME' => 'wcp',          // 数据库名
    'DB_USER' => 'root',      // 用户名
    'DB_PWD' => '901230',          // 密码
    'DB_PORT' => '3306',        // 端口
    'DB_PREFIX' => 'wcp_',    // 数据库表前缀

    /* 模板相关配置 (路径)*/
    'TMPL_PARSE_STRING' => array(

        '__Public__' => __ROOT__ . '/Public',          //Public文件夹路径
        '__JS__' => __ROOT__ . '/Public/JS',       //JS文件夹路径
        '__CSS__' => __ROOT__ . '/Public/CSS',      //CSS文件夹路径
        '__Images__' => __ROOT__ . '/Public/Images',   //Images文件夹路径
        '__Ext__' => __ROOT__ . '/Public/Ext'       //Ext文件夹路径(存储第三方类等)
    ),

    //开启多级控制器
    'CONTROLLER_LEVEL' => 2,

    'URL_MODEL' => 2,       // URL访问模式,可选参数0、1、2、3,代表以下四种模式：
    // 0 (普通模式); 1 (PATHINFO 模式); 2 (REWRITE  模式); 3 (兼容模式)  默认为PATHINFO 模式

);
<?php
return array(
    //'配置项'=>'配置值'
    //加载其他配置文件
    'LOAD_EXT_CONFIG' => 'db',

    // 设置禁止访问的模块列表
    'MODULE_DENY_LIST'      =>  array('Common','Runtime'),

    // 设置可访问目录
    'MODULE_ALLOW_LIST'    =>    array('Admin',"Game"),

    /* 模板相关配置 */
    'TMPL_PARSE_STRING' => array(
        '__COMMON__'   => __ROOT__.'/Public/Common',    //静态Common样式文件
        '__COMMON__GAMEPLATFORM__'   => __ROOT__.'/Public/GamePlatform/Common',    //静态Common样式文件
        '__ADMIN__GAMEPLATFORM__'   => __ROOT__.'/Public/GamePlatform/Admin',    //静态Admin样式文件
        '__GAME__GAMEPLATFORM__' => __ROOT__.'/Public/GamePlatform/Game', //静态Game样式文件
    ),

);
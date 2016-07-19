<?php
return array(
    //'配置项'=>'配置值'
    //加载其他配置文件
    'LOAD_EXT_CONFIG'   => 'db',

    // 设置禁止访问的模块列表
    'MODULE_DENY_LIST'  =>  array('Common','Runtime'),

    // 设置可访问目录
    'MODULE_ALLOW_LIST' =>    array('Admin'),

    /* 模板相关配置 (路径)*/
    'TMPL_PARSE_STRING' => array(

        '__Public__'    => __ROOT__ . '/Public',          //Public文件夹路径
        '__JS__'        => __ROOT__ . '/Public/JS',       //JS文件夹路径
        '__CSS__'       => __ROOT__ . '/Public/CSS',      //CSS文件夹路径
        '__Images__'    => __ROOT__ . '/Public/Images',   //Images文件夹路径
        '__Ext__'       => __ROOT__ . '/Public/Ext'       //Ext文件夹路径(存储第三方类等)
    ),

    //开启多级控制器
    'CONTROLLER_LEVEL'  => 2,

    'URL_MODEL'         => 2,       // URL访问模式,可选参数0、1、2、3,代表以下四种模式：
    // 0 (普通模式); 1 (PATHINFO 模式); 2 (REWRITE  模式); 3 (兼容模式)  默认为PATHINFO 模式

    // 设置默认的模板主题
    //'DEFAULT_THEME'    	=>  'Template',

    /*'TMPL_ENGINE_TYPE'  => 'Smarty',

    'TMPL_ENGINE_CONFIG' => array(
            'caching'           => false,
            'template_dir'      => TMPL_PATH,
            'compile_dir'       => CACHE_PATH,
            'cache_dir'         => TEMP_PATH ,
            'left_delimiter'    => '{%',
            'right_delimiter'   => '%}',
            'debugging'         => false,
    )*/

);
<?php
/**
 * Author：helen
 * CreateTime: 2016/07/31 17:12
 * Description：浏览计数器(利用静态变量)
 */
class Counter {
    public static $counter = 0;

    function __construct()
    {
        self::$counter++;
    }
}
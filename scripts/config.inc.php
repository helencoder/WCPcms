<?php
/**
 * Author：helen
 * CreateTime: 2016/07/31 17:47
 * Description：单例模式（配置类）
 */
class Config {
    static private $_instance = NULL;
    private $_settings = array();

    private function __construct() {}
    private function __clone() {}

    static function getInstance() {
        if (self::$_instance == NULL) {
            self::$_instance = new Config();
        }
        return self::$_instance;
    }

    function set($index, $value) {
        $this->_settings[$index] = $value;
    }

    function get($index) {
        return $this->_settings[$index];
    }
}
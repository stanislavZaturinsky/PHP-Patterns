<?php
namespace woo\base;

class MemApplicationRegistry extends Registry {
    private static $instance = null;
    private $values = [];
    private $id;

    private function __construct() {}

    static function instance() {
        if(is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    protected function get($key) {
        return \apc_fetch($key);
    }

    protected function set($key, $val) {
        return \apc_store($key, $val);
    }

    static function getDSN() {
        return self::instance()->get('dsn');
    }

    static function setDSN($dsn) {
        self::instance()->set('dsn', $dsn);
    }
}
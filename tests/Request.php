<?php
namespace tests\eduluz1976;

class Request {


    protected static $items = [];


    public static function get($name) {
        if (isset(self::$items[$name])) {
            return self::$items[$name];
        }
    }

    public static function set($name, $value) {
        self::$items[$name] = $value;
    }


}

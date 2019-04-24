<?php
namespace Rabbitmq;

class Autoloader
{
    protected static $_autoloadRootPath = '';

    public static function setRootPath($root_path) {
        self::$_autoloadRootPath = $root_path;
    }

    public static function loadByNamespace($name) {
        var_dump($name);
    }

}

spl_autoload_register('\Rabbitmq\Autoloader::loadByNamespace');

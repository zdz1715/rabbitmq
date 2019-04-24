<?php
namespace Rabbitmq;

class Autoloader
{
    /**
     * 根目录
     * @var string
     */
    protected static $_autoloadRootPath = '';

    /**
     * @param $root_path
     */
    public static function setRootPath($root_path) {
        self::$_autoloadRootPath = $root_path;
    }

    /**
     * @param $name
     * @return bool
     */
    public static function loadByNamespace($name) {
        self::setRootPath(__DIR__. DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR);
        $class_path = str_replace('\\', DIRECTORY_SEPARATOR, $name);
        if (strpos($name, 'PhpAmqpLib\\') === 0) {
            $class_file = __DIR__. DIRECTORY_SEPARATOR . $class_path .'.php';
        } else if(strpos($name, 'Rabbitmq\\') === 0){
            $class_file =self::$_autoloadRootPath . substr($class_path, strlen('Rabbitmq\\')) .'.php';
        } else {
            if (self::$_autoloadRootPath) {
                $class_file = self::$_autoloadRootPath . "$class_path.php";
            }
        }
        if (is_file($class_file)) {
            require_once($class_file);
            if (class_exists($name, false)) {
                return true;
            }
        }

        return false;
    }

}

spl_autoload_register('\Rabbitmq\Autoloader::loadByNamespace');

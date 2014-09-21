<?php
namespace tniessen\tinyIt;

/**
 * PHP class autoloader
 */
class AutoLoader
{
    protected static $paths = array();

    public static function addPath($path)
    {
        $path = realpath($path);
        if($path) {
            self::$paths[] = $path;
        }
    }

    public static function load($class)
    {
        $classPath = str_replace('\\', '/', "$class.php");
        foreach(self::$paths as $path) {
            if(is_file($path . '/' . $classPath)) {
                require_once $path . '/' . $classPath;
                return;
            }
        }
    }

    public static function register()
    {
        spl_autoload_register(array('tniessen\\tinyIt\\AutoLoader', 'load'));
    }
}

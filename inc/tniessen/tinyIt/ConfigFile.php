<?php
namespace tniessen\tinyIt;

class ConfigFile
{
    /**
     * Returns the path to the configuration file.
     *
     * @return string
     */
    public static function path()
    {
        return Application::path('config.php');
    }

    /**
     * Saves the configuration file.
     *
     * @param string $contents
     * @return bool whether the operation was successful
     */
    public static function save($contents)
    {
        $ret = file_put_contents(self::path(), $contents);
        return ($ret === false) ? false : true;
    }

    /**
     * Loads the configuration file.
     *
     * @return bool whether the configuration file was loaded successfully
     */
    public static function load()
    {
        $loaded = defined('TI_CONFIG_LOADED');
        if(!$loaded) {
            $cfgpath = self::path();
            $found = file_exists($cfgpath);
            if($found) {
                require $cfgpath;
                $loaded = defined('TI_CONFIG_LOADED');
            }
        }
        return $loaded;
    }

}

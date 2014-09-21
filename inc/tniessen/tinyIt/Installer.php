<?php
namespace tniessen\tinyIt;

/**
 * Provides essential functions to check the installation status and perform
 * necessary installation steps.
 */
class Installer
{
    /**
     * The application has been installed completely.
     */
    const INSTALLED     = 0;
    /**
     * No configuration file exists.
     */
    const CREATE_CONFIG = 1;
    /**
     * The database has not been initialized yet.
     */
    const INIT_DATABASE = 2;

    private static $status = null;

    /**
     * Determines the installation status.
     *
     * The status is determined using the following steps:
     * - If the configuration file (see {@link ConfigFile}) does not exist, the
     *   installation status is `CREATE_CONFIG`.
     * - If the `.installation` file (which will be created unless the
     *   configuration file exists) does exist, the status is `INIT_DATABASE`.
     * - If none of the above conditions are met, the status is `INSTALLED`.
     *
     * @param bool $cached whether a cached status may be used
     * @return int
     * @see INSTALLED
     * @see CREATE_CONFIG
     * @see INIT_DATABASE
     */
    public static function getStatus($cached = true)
    {
        if(self::$status === null) {
            $cfgpath = ConfigFile::path();
            $iipath  = Application::path('.installation');
            if(!file_exists($cfgpath)) {
                self::$status = self::CREATE_CONFIG;
                touch($iipath);
            } else if(file_exists($iipath)) {
                self::$status = self::INIT_DATABASE;
            } else {
                self::$status = self::INSTALLED;
            }
        }
        return self::$status;
    }

    /**
     * Completes the installation.
     *
     * This function will delete the `.installation` file which is used to
     * mark the application as *not installed*.
     */
    public static function completeInstallation()
    {
        $iipath  = Application::path('.installation');
        unlink($iipath);
    }

    /**
     * Generates PHP code representing the given configuration.
     */
    public static function createConfigCode($server, $port, $username,
                                            $password, $dbname,
                                            $tblprefix,
                                            $adminpath,
                                            $requrlkey)
    {
        $code  = "<?php\n";
        $code .= "define('TI_CONFIG_LOADED', true);\n";
        $code .= "/*\n * DATABASE\n */\n";
        $code .= "define('TI_DB_HOST', '" . addslashes($server) . "');\n";
        $code .= "define('TI_DB_PORT', " . intval($port) . ");\n";
        $code .= "define('TI_DB_USER', '" . addslashes($username) . "');\n";
        $code .= "define('TI_DB_PASS', '" . addslashes($password) . "');\n";
        $code .= "define('TI_DB_NAME', '" . addslashes($dbname) . "');\n";
        $code .= "define('TI_DB_TBLPREFIX', '" . addslashes($tblprefix) . "');\n";
        $code .= "/*\n * SERVER\n */\n";
        $code .= "define('TI_ADMIN_PATH', '" . addslashes($adminpath) . "');\n";
        $code .= "/*\n * INTERNAL\n */\n";
        $code .= "define('TI_REQURLKEY', '" . addslashes($requrlkey) . "');\n";
        $code .= "define('TI_ROUTING_ENABLED', true);\n";
        return $code;
    }

    /**
     * Saves the configuration file.
     *
     * @param string $code
     */
    public static function saveConfigFile($code)
    {
        return ConfigFile::save($code);
    }

    /**
     * Generates code for a `.htaccess` file.
     *
     * The generated file handles rewrites on apache servers.
     */
    public static function createHtaccessCode($requrlkey)
    {
        $code  = "RewriteEngine On\n";
        $code .= "RewriteCond %{QUERY_STRING} !$requrlkey\n";
        $code .= "RewriteRule ^(.*)$ index.php?$requrlkey=$1 [QSA,L]\n";
        return $code;
    }

    /**
     * Saves the `.htaccess` file.
     *
     * @param string $code
     */
    public static function saveHtaccessFile($code)
    {
        $ret = file_put_contents(Application::path('.htaccess'), $code);
        return ($ret === false) ? false : true;
    }

    /**
     * Tests whether all permissions which are required during installation are
     * available.
     *
     * @return bool
     */
    public static function testRequiredPermissions()
    {
        $cfgpath = ConfigFile::path();
        $iipath  = Application::path('.installation');
        return self::testWritePermissions($cfgpath) &&
               self::testWritePermissions($iipath);
    }

    /**
     * Checks whether a file can be written to.
     *
     * @param string $file
     * @return bool
     */
    private static function testWritePermissions($file)
    {
        if(file_exists($file)) {
            return is_writable($file);
        }
        $dir = dirname($file);
        return file_exists($dir) && is_dir($dir) && is_writable($dir);
    }
}

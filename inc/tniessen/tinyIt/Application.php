<?php
namespace tniessen\tinyIt;

/**
 * Main application class.
 */
class Application
{
    /**
     * The root directory of this application. Should be set by the `init`
     * function.
     *
     * @var string
     */
    public static $rootDir;

    private static $dbConn;

    /**
     * Initializes the application.
     *
     * Sets the application's root directory, loads the configuration file
     * and initializes the authorization.
     *
     * @param string $rootDir Root directory of this application
     */
    public static function init($rootDir)
    {
        self::$rootDir = $rootDir;
        if(ConfigFile::load()) {
            Security\Authorization::init();
        }
    }

    /**
     * Determines the base URL of this application.
     *
     * The path of the returned {@link URL} will always end with a slash.
     *
     * @return URL
     */
    public static function getBaseURL()
    {
        $url = URL::getReal();
        $url->path = $url->pathDirname();
        $url->query = null;
        return $url;
    }

    /**
     * Returns the path of a file specified by its path relative to the
     * root directory of the application.
     *
     * @param string $file File name or path
     * @return string
     */
    public static function path($file)
    {
        return self::$rootDir . '/' . $file;
    }

    /**
     * Starts the application.
     *
     * Starts to process the request. The request will be processed using these
     * steps:
     * - If `TI_REQURLKEY` is not defined, this function will call enableWebUI.
     *   This should not happen after the installation succeeded.
     * - If the request path starts with `TI_ADMIN_PATH` and a matching file in
     *   the `assets` folder exists, it will be served.
     * - If the request path starts with `TI_ADMIN_PATH`, enableWebUI will be
     *   called in order to provide the administration interface.
     * - If the requested path is considered equal to the base path of this
     *   application, this function will process the request as specified with
     *   the `home_action` option.
     * - Finally, this function uses {@link Database\LinksTableAdapter::resolvePath}
     *   to find the target the given path should redirect to.
     * - If the previous step fails, a 404 error will be issued and a related
     *   page will be shown.
     */
    public static function start()
    {
        $currURL = URL::getCurrent();
        if(!defined('TI_REQURLKEY')) {
            $path = isset($_GET['_webuipath']) ? $_GET['_webuipath'] : '';
            return self::enableWebUI($path, $_GET);
        }

        $router = Router::fromGeneratedURL($currURL, TI_REQURLKEY);

        $match = $router->match(TI_ADMIN_PATH . '/assets/%%');
        if($match !== false) {
            $assetdir = realpath(Application::$rootDir . '/assets/');
            $filepath = realpath($assetdir . '/' . $match[0]);
            if($filepath !== false) {
                if(strpos($filepath, $assetdir) !== 0) {
                    die('Attack attempt: directory traversal attack');
                }
                if(file_exists($filepath) && is_file($filepath)) {
                    header('Content-Type: ' . MimeContentTypes::getForFile($filepath));
                    readfile($filepath);
                    exit;
                }
            }
        }

        $match = $router->match(TI_ADMIN_PATH . '/%%?');
        if($match !== false) {
            return self::enableWebUI($match[0], $router->getParameters());
        }

        $dbc = self::dbConnection();
        $path = implode('/', $router->getPathElements());
        if($path === '') {
            $opts = $dbc->options()->getOptions(array(
                'home_action', 'home_target'
            ));
            $target = WebUI\Page::getURL('');
            switch($opts['home_action']) {
                case 'redirect':
                    $target = URL::parse($opts['home_target'], 'http');
                    break;
            }
            $target->redirectTo();
        } else {
            $link = $dbc->links()->resolvePath($path);
            if(!$link) {
                $nfp = WebUI\NotFoundPage::getInstance($path, array());
                self::startWebUI($nfp, array());
                exit;
            }
            header('Location: ' . $link->resolved, 302);
        }
    }

    /**
     * Enables the WebUI by delegating processing to the page returned by
     * {@link WebUI\CurrentPage::getInstance}.
     *
     * @param string $pathstr
     * @param array params
     */
    public static function enableWebUI($pathstr, $params)
    {
        $pathstr = rtrim($pathstr, '/');
        if($pathstr !== '') {
            $path = explode('/', $pathstr);
        } else {
            $path = array();
        }
        $page = WebUI\CurrentPage::getInstance($path, $params);
        self::startWebUI($page, $params);
    }

    private static function startWebUI($page, $params)
    {
        $page->init($params);
        $page->render();
    }

    /**
     * Returns a database connection.
     *
     * If no connection has been established yet, a new connection will be
     * created. If connecting fails, this function terminates the response
     * with an error message.
     *
     * @return Database\DatabaseConnection
     */
    public static function dbConnection()
    {
        if(self::$dbConn === null) {
            try {
                self::$dbConn = new Database\DatabaseConnection(TI_DB_HOST, TI_DB_PORT);
                self::$dbConn->init(TI_DB_NAME, TI_DB_TBLPREFIX);
                self::$dbConn->connect(TI_DB_USER, TI_DB_PASS);
            } catch(\PDOException $e) {
                die('Cannot connect to database. Please contact an administrator.');
            }
        }
        return self::$dbConn;
    }

}

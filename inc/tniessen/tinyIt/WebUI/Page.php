<?php
namespace tniessen\tinyIt\WebUI;

use tniessen\tinyIt\Application;
use tniessen\tinyIt\Installer;
use tniessen\tinyIt\Security\Authorization;
use tniessen\tinyIt\Security\Permission;
use tniessen\tinyIt\URL;

abstract class Page
{
    /**
     * Initializes the page.
     *
     * Initialization usually includes:
     * - Verification of availability, authorization and permissions
     * - Processing of submitted data, e.g. form data
     * - Preparation of data for use in the `render` function
     *
     * @param array $params
     */
    public function init($params)
    {
        // No default initialization code
    }

    /**
     * Renders the page.
     *
     * @see Page::renderTemplate
     */
    abstract public function render();

    /**
     * Renders a template using a WebRenderer.
     *
     * Using this function to render templates provides multiple benefits,
     * including the availability of `$page` and `$pageURL()` in the template.
     * `$pageURL` is a shorthand for Page::getURL and URL::build. It does
     * **not** escape the returned URL.
     * `$page` refers to this page (`$this`).
     *
     * @param string $name
     * @param array $options
     */
    final protected function renderTemplate($name, $options = array())
    {
        $renderer = self::initWebRenderer($options);
        $renderer->provide('page', $this);
        $renderer->render($name);
    }

    private static function initWebRenderer($options)
    {
        $renderer = new WebRenderer($options);
        $renderer->provide('pageURL', function($path, $params = array()) {
            return Page::getURL($path, $params)->build();
        });
        $renderer->provide('theUser', Authorization::user());
        $renderer->provide('theNonce', Authorization::getNonce());
        return $renderer;
    }

    final public static function requirePermission($perm)
    {
        if(!self::hasPermission($perm)) {
            self::initWebRenderer(array(
                'missingPermission' => $perm
            ))->render('nopermission');
            exit;
        }
    }

    final public static function hasPermission($perm)
    {
        return Permission::currentUserCan($perm);
    }

    /**
     * Requires an authorized session.
     *
     * If the client does not hold an authorized session, this function
     * will redirect the client to the `login` page.
     *
     * This function will call Page::requireInstallationComplete prior to other
     * actions.
     */
    final public static function requireLogin()
    {
        self::requireInstallationComplete();
        if(!Authorization::loggedIn()) {
            self::redirectTo('login');
            exit;
        }
    }

    /**
     * Requires an authorized session and a valid `nonce` GET parameter.
     *
     * If the client did not send a valid nonce along with the request, this
     * function will redirect the client to another page.
     *
     * This function will call Page::requireLogin prior to any other actions.
     *
     * @param string $redirectTo
     */
    final public static function requireNonce($redirectTo = 'home')
    {
        self::requireLogin();
        $data = \tniessen\tinyIt\HttpParams::_GET();
        $okay = $data->has('nonce') && Authorization::isNonce($data->get('nonce'));
        if(!$okay) {
            self::redirectTo($redirectTo);
            exit;
        }
    }

    /**
     * Requires the installation to be complete.
     *
     * If the installation has not been completed, this function will redirect
     * the client to the `installation` page.
     *
     * @see \tniessen\tinyIt\Installer::getStatus
     */
    final public static function requireInstallationComplete()
    {
        if(Installer::getStatus() !== Installer::INSTALLED) {
            self::redirectTo('installation');
            exit;
        }
    }

    /**
     * Returns a Page instance.
     *
     * The default implementation creates a new instance of the class this
     * function is called on. However, classes inheriting from Page might
     * override this function to return other objects depending the passed
     * parameters.
     *
     * @param array $path
     * @param array $params
     * @return Page
     */
    public static function getInstance($path, $params)
    {
        return new static();
    }

    /**
     * Returns a URL pointing to a page.
     *
     * @param mixed $path
     * @param array $params
     * @return \tniessen\tinyIt\URL
     */
    final public static function getURL($path, $params = array())
    {
        if(is_array($path)) {
            $path = implode('/', $path);
        }

        $url = Application::getBaseURL();
        if(defined('TI_ADMIN_PATH')) {
            $url->path .= TI_ADMIN_PATH . '/';
        }
        if(defined('TI_ROUTING_ENABLED')) {
            $url->path .= $path;
        } else {
            $params['_webuipath'] = $path;
        }
        $url->query = URL::buildQuery($params);
        return $url;
    }

    /**
     * Redirects the client to a page.
     *
     * @see \tniessen\tinyIt\URL::redirectTo
     */
    final public static function redirectTo($path, $params = array())
    {
        self::getURL($path, $params)->redirectTo();
    }

    final public static function nextPath($path)
    {
        if(count($path) < 2) return array();
        return array_slice($path, 1);
    }
}

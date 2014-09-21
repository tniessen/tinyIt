<?php
namespace tniessen\tinyIt\WebUI;

use tniessen\tinyIt\Security\Authorization;

class LogoutPage extends Page
{
    private $errorMessage;

    public function init($params)
    {
        Page::requireNonce();
        Authorization::logout();
        self::redirectTo('login');
        exit;
    }

    public function render()
    {
        die('init() not called');
    }
}

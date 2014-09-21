<?php
namespace tniessen\tinyIt\WebUI\Settings;

use tniessen\tinyIt\Application;
use tniessen\tinyIt\WebUI\Page;
use tniessen\tinyIt\WebUI\NotFoundPage;

abstract class OwnSettingsPage extends Page
{
    public static function getInstance($path, $params)
    {
        if(!count($path)) {
            self::redirectTo('settings/own/account');
            exit;
        }
        $sname = $path[0];
        $path = self::nextPath($path);
        switch($sname) {
            case 'account':
                $page = Own\AccountSettingsPage::getInstance($path, $params);
                break;
            default:
                $page = NotFoundPage::getInstance($path, $params);
        }
        return $page;
    }
}

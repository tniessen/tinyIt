<?php
namespace tniessen\tinyIt\WebUI;

use tniessen\tinyIt\Application;

abstract class SettingsPage extends Page
{
    public static function getInstance($path, $params)
    {
        if(!count($path)) {
            self::redirectTo('settings/own');
            exit;
        }
        $sname = $path[0];
        $path = self::nextPath($path);
        switch($sname) {
            case 'own':
                $page = Settings\OwnSettingsPage::getInstance($path, $params);
                break;
            case 'site':
                $page = Settings\SiteSettingsPage::getInstance($path, $params);
                break;
            default:
                $page = NotFoundPage::getInstance($path, $params);
        }
        return $page;
    }
}

<?php
namespace tniessen\tinyIt\WebUI\Settings;

use tniessen\tinyIt\Application;
use tniessen\tinyIt\WebUI\Page;
use tniessen\tinyIt\WebUI\NotFoundPage;

abstract class SiteSettingsPage extends Page
{
    public static function getInstance($path, $params)
    {
        Page::requirePermission('settings.change_site_settings');

        if(!count($path)) {
            self::redirectTo('settings/site/general');
            exit;
        }
        $sname = $path[0];
        $path = self::nextPath($path);
        switch($sname) {
            case 'general':
                $page = Site\GeneralSettingsPage::getInstance($path, $params);
                break;
            case 'links':
                $page = Site\LinksSettingsPage::getInstance($path, $params);
                break;
            case 'users':
                $page = Site\UsersSettingsPage::getInstance($path, $params);
                break;
            default:
                $page = NotFoundPage::getInstance($path, $params);
        }
        return $page;
    }
}

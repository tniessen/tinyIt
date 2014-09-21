<?php
namespace tniessen\tinyIt\WebUI;

use tniessen\tinyIt\Installer;

abstract class GroupsPage extends Page
{
    public static function getInstance($path, $params)
    {
        if(!count($path)) {
            self::redirectTo('groups/list');
            exit;
        }
        $sname = $path[0];
        $path = self::nextPath($path);
        switch($sname) {
            case 'add-group':
                $page = Groups\AddGroupPage::getInstance($path, $params);
                break;
            case 'list':
                $page = Groups\GroupListPage::getInstance($path, $params);
                break;
            case 'details':
                $page = Groups\GroupDetailsPage::getInstance($path, $params);
                break;
            case 'permissions':
                $page = Groups\GroupPermissionsPage::getInstance($path, $params);
                break;
            default:
                $page = NotFoundPage::getInstance($path, $params);
        }
        return $page;
    }
}

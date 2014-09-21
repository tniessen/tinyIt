<?php
namespace tniessen\tinyIt\WebUI;

abstract class UsersPage extends Page
{
    public static function getInstance($path, $params)
    {
        if(!count($path)) {
            self::redirectTo('users/list');
            exit;
        }
        $sname = $path[0];
        $path = self::nextPath($path);
        switch($sname) {
            case 'list':
                $page = Users\UserListPage::getInstance($path, $params);
                break;
            case 'details':
                $page = Users\UserDetailsPage::getInstance($path, $params);
                break;
            default:
                $page = NotFoundPage::getInstance($path, $params);
        }
        return $page;
    }
}

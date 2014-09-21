<?php
namespace tniessen\tinyIt\WebUI;

abstract class CurrentPage extends Page
{
    public static function getInstance($path, $params)
    {
        if(!count($path)) {
            self::redirectTo('home');
            exit;
        }
        $pname = $path[0];
        $path = self::nextPath($path);
        switch($pname) {
            case 'home':
                $page = HomePage::getInstance($path, $params);
                break;
            case 'register':
                $page = RegistrationPage::getInstance($path, $params);
                break;
            case 'login':
                $page = LoginPage::getInstance($path, $params);
                break;
            case 'logout':
                $page = LogoutPage::getInstance($path, $params);
                break;
            case 'switch-user':
                $page = SwitchUserPage::getInstance($path, $params);
                break;
            case 'links':
                $page = LinksPage::getInstance($path, $params);
                break;
            case 'users':
                $page = UsersPage::getInstance($path, $params);
                break;
            case 'groups':
                $page = GroupsPage::getInstance($path, $params);
                break;
            case 'settings':
                $page = SettingsPage::getInstance($path, $params);
                break;
            case 'tools':
                $page = ToolsPage::getInstance($path, $params);
                break;
            case 'installation':
                $page = InstallationPage::getInstance($path, $params);
                break;
            default:
                $page = NotFoundPage::getInstance($path, $params);
        }
        return $page;
    }
}

<?php
namespace tniessen\tinyIt\WebUI;

use tniessen\tinyIt\Installer;

abstract class InstallationPage extends Page
{
    public static function redirectToCorrectStep()
    {
        $status = Installer::getStatus();
        if($status === Installer::INSTALLED) {
            self::redirectTo('home');
        } else if($status === Installer::CREATE_CONFIG) {
            self::redirectTo('installation/config');
        } else if($status === Installer::INIT_DATABASE) {
            self::redirectTo('installation/database');
        } else {
            die("Invalid installer status: $status");
        }
    }

    public static function getInstance($path, $params)
    {
        if(!count($path)) {
            self::redirectTo('installation/start');
            exit;
        }

        $sname = $path[0];
        $path = self::nextPath($path);
        switch($sname) {
            case 'start':
                $page = Installation\StartPage::getInstance($path, $params);
                break;
            case 'config':
                $page = Installation\ConfigPage::getInstance($path, $params);
                break;
            case 'database':
                $page = Installation\DatabasePage::getInstance($path, $params);
                break;
            default:
                self::redirectToCorrectStep();
                exit;
        }
        return $page;
    }
}

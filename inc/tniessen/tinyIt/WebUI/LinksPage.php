<?php
namespace tniessen\tinyIt\WebUI;

use tniessen\tinyIt\Installer;

abstract class LinksPage extends Page
{
    public static function getInstance($path, $params)
    {
        if(!count($path)) {
            self::redirectTo('links/list');
            exit;
        }
        $sname = $path[0];
        $path = self::nextPath($path);
        switch($sname) {
            case 'shorten':
                $page = Links\ShortenLinkPage::getInstance($path, $params);
                break;
            case 'add-wildcard':
                $page = Links\AddWildcardPage::getInstance($path, $params);
                break;
            case 'list':
                $page = Links\LinkListPage::getInstance($path, $params);
                break;
            case 'details':
                $page = Links\LinkDetailsPage::getInstance($path, $params);
                break;
            case 'resolve':
                $page = Links\ResolveLinkPage::getInstance($path, $params);
                break;
            default:
                $page = NotFoundPage::getInstance($path, $params);
        }
        return $page;
    }
}

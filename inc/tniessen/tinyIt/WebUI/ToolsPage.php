<?php
namespace tniessen\tinyIt\WebUI;

use tniessen\tinyIt\Application;

abstract class ToolsPage extends Page
{
    public static function getInstance($path, $params)
    {
        $sname = count($path) ? $path[0] : '';
        $path = self::nextPath($path);
        switch($sname) {
            case 'qr-code':
                $page = Tools\QRCodePage::getInstance($path, $params);
                break;
            default:
                $page = NotFoundPage::getInstance($path, $params);
        }
        return $page;
    }
}

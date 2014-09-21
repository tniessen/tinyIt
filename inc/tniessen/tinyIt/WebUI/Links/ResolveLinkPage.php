<?php
namespace tniessen\tinyIt\WebUI\Links;

use tniessen\tinyIt\Application;
use tniessen\tinyIt\WebUI\Page;

class ResolveLinkPage extends Page
{
    public function init($params)
    {
        $linkId = 0;
        if(isset($params['path'])) {
            $dbc = Application::dbConnection();
            $link = $dbc->links()->resolvePath($params['path']);
            if($link) {
                $linkId = $link->id;
            }
        }

        self::redirectTo('links/details', array(
            'link' => $linkId
        ));
        exit;
    }

    public function render()
    {
        die('init() not called');
    }
}

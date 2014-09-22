<?php
namespace tniessen\tinyIt\WebUI;

use tniessen\tinyIt\Application;

class NotFoundPage extends Page
{
    public function init($params)
    {
        header('HTTP/1.0 404 Not Found');
    }

    public function render()
    {
        $this->renderTemplate('notfound');
    }
}

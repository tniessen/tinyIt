<?php
namespace tniessen\tinyIt\WebUI;

class HomePage extends Page
{
    public function init($params)
    {
        Page::requireLogin();
    }

    public function render()
    {
        $this->renderTemplate('home');
    }
}

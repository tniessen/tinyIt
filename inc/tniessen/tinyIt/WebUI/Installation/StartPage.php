<?php
namespace tniessen\tinyIt\WebUI\Installation;

use tniessen\tinyIt\WebUI\Page;
use tniessen\tinyIt\WebUI\InstallationPage;
use tniessen\tinyIt\Installer;

class StartPage extends Page
{
    public function init($params)
    {
        if(Installer::getStatus() !== Installer::CREATE_CONFIG) {
            InstallationPage::redirectToCorrectStep();
            exit;
        }
    }

    public function render()
    {
        $this->renderTemplate('installation/start');
    }

}

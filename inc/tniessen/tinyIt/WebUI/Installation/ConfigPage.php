<?php
namespace tniessen\tinyIt\WebUI\Installation;

use tniessen\tinyIt\WebUI\Page;
use tniessen\tinyIt\WebUI\InstallationPage;
use tniessen\tinyIt\WebUI\WebRenderer;
use tniessen\tinyIt\Database\DatabaseConnection;
use tniessen\tinyIt\Installer;

class ConfigPage extends Page
{
    private $errorMessage;
    private $currentParams;

    public function init($params)
    {
        if(Installer::getStatus() !== Installer::CREATE_CONFIG) {
            InstallationPage::redirectToCorrectStep();
            exit;
        }

        $postData = \tniessen\tinyIt\HttpParams::_POST();
        if($postData && !$postData->isEmpty()) {
            $this->currentParams = $postData;
            $this->tryProcessPostData($postData);
        }
    }

    private function tryProcessPostData($postData)
    {
        $reqfields = array(
            // Database
            'server', 'port', 'username', 'password', 'dbname',
            // Server
            'adminpath',
            // Advanced
            'requrlkey'
        );
        if(!$postData->hasValues($reqfields)) {
            $this->errorMessage = 'Please specify at least:
                                   <ul>
                                     <li>MySQL host, port, username, password and database name</li>
                                     <li>WebUI path</li>
                                     <li>Secret .htaccess key</li>
                                   </ul>';
            return;
        }

        $fields = $reqfields;
        $fields[] = 'tblprefix';
        extract($postData->filter($fields));

        $result = DatabaseConnection::test($server, $port, $dbname, $username, $password);
        if($result !== true) {
            $this->errorMessage = 'Unable to connect to database:
                                   <pre>' .
                                     WebRenderer::escapeHtml($result->getMessage()) .
                                  '</pre>';
            return;
        }

        $result = Installer::testRequiredPermissions();
        if(!$result) {
            $this->errorMessage = 'Insufficient file system permissions';
            return;
        }

        $htaccessCode = Installer::createHtaccessCode($requrlkey);
        Installer::saveHtaccessFile($htaccessCode);

        $configCode = Installer::createConfigCode($server, $port, $username,
                                                  $password, $dbname,
                                                  $tblprefix,
                                                  $adminpath,
                                                  $requrlkey);
        Installer::saveConfigFile($configCode);

        define('TI_ADMIN_PATH', $adminpath);
        define('TI_URLBASEPATH', $urlbasepath);
        define('TI_ROUTING_ENABLED', true);
        $this->redirectTo('installation', 'database');
        exit;
    }

    public function render()
    {
        $opts = array();
        if($this->errorMessage !== null) {
            $opts['errorMessage'] = $this->errorMessage;
        }
        if($this->currentParams !== null) {
            foreach($this->currentParams->values() as $field => $value) {
                $opts['current:' . $field] = $value;
            }
        }
        $this->renderTemplate('installation/config', $opts);
    }

}

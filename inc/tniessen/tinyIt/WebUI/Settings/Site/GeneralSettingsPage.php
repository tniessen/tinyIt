<?php
namespace tniessen\tinyIt\WebUI\Settings\Site;

use tniessen\tinyIt\WebUI\Page;
use tniessen\tinyIt\WebUI\SettingsPage;
use tniessen\tinyIt\WebUI\WebRenderer;
use tniessen\tinyIt\Application;

class GeneralSettingsPage extends Page
{
    private $errorMessage;
    private $currentParams;
    private $dbSettings;

    public function init($params)
    {
        self::requireLogin();

        $postData = \tniessen\tinyIt\HttpParams::_POST();
        if($postData && !$postData->isEmpty()) {
            $this->currentParams = $postData;
            $this->tryProcessPostData($postData);
        }

        $dbc = Application::dbConnection();
        $this->dbSettings = $dbc->options()->getOptions(array(
            'home_action',
            'home_target'
        ));
    }

    private function tryProcessPostData($postData)
    {
        $reqfields = array(
            'home_action'
        );
        if(!$postData->hasValues($reqfields)) {
            $this->errorMessage = 'Please fill out all required fields.';
            return;
        }

        $fields = $reqfields;
        $fields[] = 'home_target';
        extract($postData->filter($fields));

        if($home_action === 'redirect' && !$home_target) {
            $this->errorMessage = 'Please enter a valid target URL to use as the home page.';
            return;
        }

        $dbc = Application::dbConnection();
        $dbc->options()->setOptions(array(
            'home_action' => $home_action,
            'home_target' => $home_target
        ));

        $this->currentParams = null;
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
        if($this->dbSettings !== null) {
            foreach($this->dbSettings as $field => $value) {
                $opts['setting:' . $field] = $value;
            }
        }
        $this->renderTemplate('settings/site/general', $opts);
    }

}

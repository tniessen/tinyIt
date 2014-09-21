<?php
namespace tniessen\tinyIt\WebUI\Settings\Site;

use tniessen\tinyIt\WebUI\Page;
use tniessen\tinyIt\WebUI\SettingsPage;
use tniessen\tinyIt\Application;

class LinksSettingsPage extends Page
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
            'linkgen_chars',
            'linkgen_length',
            'custom_links_regex'
        ));
    }

    private function tryProcessPostData($postData)
    {
        $reqfields = array(
            // Generated links
            'linkgen_chars', 'linkgen_length',
            // Custom links (paths)
            'custom_links_regex'
        );
        if(!$postData->hasValues($reqfields)) {
            $this->errorMessage = 'Please fill out all required fields.';
            return;
        }

        $fields = $reqfields;
        extract($postData->filter($fields));

        $linkgen_length = intval($linkgen_length);
        if($linkgen_length < 3 || $linkgen_length > 10) {
            $this->errorMessage = 'Generated path length should be between three and ten.';
            return;
        }

        $dbc = Application::dbConnection();
        $dbc->options()->setOptions(array(
            'linkgen_chars' => $linkgen_chars,
            'linkgen_length' => $linkgen_length,
            'custom_links_regex' => $custom_links_regex
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
        $this->renderTemplate('settings/site/links', $opts);
    }

}

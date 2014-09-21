<?php
namespace tniessen\tinyIt\WebUI\Settings\Site;

use tniessen\tinyIt\WebUI\Page;
use tniessen\tinyIt\WebUI\SettingsPage;
use tniessen\tinyIt\Application;

class UsersSettingsPage extends Page
{
    private $errorMessage;
    private $currentParams;
    private $dbSettings;

    private $availableGroups;

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
            'allow_registration',
            'registration_user_group',
            'allow_name_changes'
        ));

        $this->availableGroups = $dbc->groups()->getGroups(0, 100);
    }

    private function tryProcessPostData($postData)
    {
        $reqfields = array(
            // Nothing
        );
        if(!$postData->hasValues($reqfields)) {
            $this->errorMessage = 'Please fill out all required fields.';
            return;
        }

        $fields = $reqfields;
        $fields[] = 'allow_registration';
        $fields[] = 'registration_user_group';
        $fields[] = 'allow_name_changes';
        extract($postData->filter($fields));

        $allow_registration = !!$allow_registration;
        $registration_user_group = intval($registration_user_group);
        $allow_name_changes = !!$allow_name_changes;

        $dbc = Application::dbConnection();
        $dbc->options()->setOptions(array(
            'allow_registration' => $allow_registration,
            'registration_user_group' => $registration_user_group,
            'allow_name_changes' => $allow_name_changes
        ));

        $this->currentParams = null;
    }

    public function render()
    {
        $opts = array(
            'availableGroups' => $this->availableGroups
        );
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
        $this->renderTemplate('settings/site/users', $opts);
    }

}

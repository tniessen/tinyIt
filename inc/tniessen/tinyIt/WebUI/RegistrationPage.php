<?php
namespace tniessen\tinyIt\WebUI;

use tniessen\tinyIt\Application;
use tniessen\tinyIt\Security\Authorization;

class RegistrationPage extends Page
{
    private $errorMessage;
    private $currentParams;

    public function init($params)
    {
        if(Authorization::loggedIn()) {
            self::redirectTo('home');
            exit;
        }

        $dbc = Application::dbConnection();
        if(!$dbc->options()->getOption('allow_registration')) {
            self::redirectTo('login');
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
            'username', 'password', 'rptpassword', 'email'
        );
        if(!$postData->hasValues($reqfields)) {
            $this->errorMessage = 'Please submit username, password and e-mail.';
            return;
        }

        $fields = $reqfields;
        extract($postData->filter($fields));

        $usernamepattern = '/^[a-z][-a-z0-9_.]*$/i';
        if(!preg_match($usernamepattern, $username)) {
            $this->errorMessage = 'The submitted username is invalid.';
            return;
        }

        if($password !== $rptpassword) {
            $this->errorMessage = 'The passwords do not match.';
            return;
        }

        $dbc = Application::dbConnection();
        $existing = $dbc->users()->getUserByName($username);
        if($existing) {
            $this->errorMessage = 'This username is already taken.';
            return;
        }

        $uid = $dbc->users()->addUser(
            $username,
            $username,
            $email,
            $password
        );

        $defGroup = $dbc->options()->getOption('registration_user_group');
        if($defGroup) {
            $dbc->users()->setGroup($uid, $defGroup);
        }

        $this->redirectTo('login');
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
        $this->renderTemplate('registration', $opts);
    }
}

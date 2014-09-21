<?php
namespace tniessen\tinyIt\WebUI;

use tniessen\tinyIt\Application;
use tniessen\tinyIt\Security\Authorization;

class LoginPage extends Page
{
    private $errorMessage;

    public function init($params)
    {
        if(Authorization::loggedIn()) {
            self::redirectTo('home');
            exit;
        }

        $postData = \tniessen\tinyIt\HttpParams::_POST();
        if($postData && !$postData->isEmpty()) {
            $this->tryProcessPostData($postData);
        }
    }

    private function tryProcessPostData($postData)
    {
        $reqfields = array(
            'username', 'password'
        );
        if(!$postData->hasValues($reqfields)) {
            $this->errorMessage = 'Please submit username and password.';
            return;
        }

        $fields = $reqfields;
        extract($postData->filter($fields));

        $uid = Authorization::login($username, $password);
        if($uid === false) {
            $this->errorMessage = 'Incorrect user / password';
            return;
        }

        $this->redirectTo('home');
        exit;
    }

    public function render()
    {
        $opts = array();
        if($this->errorMessage !== null) {
            $opts['errorMessage'] = $this->errorMessage;
        }
        $dbc = Application::dbConnection();
        $opts['allowRegistration'] = $dbc->options()->getOption('allow_registration');
        $this->renderTemplate('login', $opts);
    }
}

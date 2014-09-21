<?php
namespace tniessen\tinyIt\WebUI\Installation;

use tniessen\tinyIt\WebUI\Page;
use tniessen\tinyIt\WebUI\InstallationPage;
use tniessen\tinyIt\WebUI\WebRenderer;
use tniessen\tinyIt\Installer;
use tniessen\tinyIt\Application;

class DatabasePage extends Page
{
    private $errorMessage;
    private $currentParams;

    public function init($params)
    {
        if(Installer::getStatus() !== Installer::INIT_DATABASE) {
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
            // Initial user
            'username', 'password', 'rptpassword', 'email'
        );
        if(!$postData->hasValues($reqfields)) {
            $this->errorMessage = 'Please specify at least:
                                   <ul>
                                     <li>Administrator username, password and email</li>
                                   </ul>';
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
            $this->errorMessage = 'The submitted passwords do not match.';
            return;
        }

        $dbc = Application::dbConnection();
        $dbc->installDatabase();
        $dbc->setDefaultOptions();

        $uid = $dbc->users()->addUser(
            // User name
            $username,
            // Display name
            $username,
            // Mail address
            $email,
            // Password
            $password
        );

        $dbc->users()->setFlags($uid, \tniessen\tinyIt\Database\UsersTableAdapter::FLAG_ALMIGHTY);

        Installer::completeInstallation();

        $this->redirectTo('home');
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
        $this->renderTemplate('installation/database', $opts);
    }

}

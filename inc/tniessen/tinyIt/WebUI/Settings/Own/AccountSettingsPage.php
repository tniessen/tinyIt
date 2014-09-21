<?php
namespace tniessen\tinyIt\WebUI\Settings\Own;

use tniessen\tinyIt\WebUI\Page;
use tniessen\tinyIt\WebUI\SettingsPage;
use tniessen\tinyIt\Application;
use tniessen\tinyIt\Security\Authorization;

class AccountSettingsPage extends Page
{
    private $errorMessage;
    private $settings;
    private $userInfo;

    public function init($params)
    {
        self::requireLogin();

        $dbc = Application::dbConnection();
        $this->settings = $dbc->options()->getOptions(array(
            'allow_name_changes'
        ));

        $this->userInfo = Authorization::user();

        $postData = \tniessen\tinyIt\HttpParams::_POST();
        if($postData && !$postData->isEmpty()) {
            $this->currentParams = $postData;
            $this->tryProcessPostData($postData);
            $this->userInfo = Authorization::user('reload');
        }
    }

    private function tryProcessPostData($postData)
    {
        $reqfields = array(
            'display_name',
            'email'
        );
        if(!$postData->hasValues($reqfields)) {
            $this->errorMessage = 'Please fill out all required fields.';
            return;
        }

        $fields = $reqfields;
        $fields[] = 'username';
        extract($postData->filter($fields));

        $dbc = Application::dbConnection();

        $uid = $this->userInfo->id;

        if($username && $this->userInfo->name !== $username) {
            if(!$this->settings['allow_name_changes']) {
                $this->errorMessage = 'Renaming users is currently forbidden.';
                return;
            }
            if(!self::hasPermission('user.change_name')) {
                $this->errorMessage = 'You are not permitted to change your user name.';
                return;
            }
            $usernamepattern = '/^[a-z][-a-z0-9_.]*$/i';
            if(!preg_match($usernamepattern, $username)) {
                $this->errorMessage = 'The submitted username is invalid.';
                return;
            }
            $conflict = $dbc->users()->getUserByName($username);
            if($conflict) {
                $this->errorMessage = 'A user with this name already exists.';
                return;
            }
            $success = $dbc->users()->renameUser($uid, $username);
            if(!$success) {
                $this->errorMessage = 'Error while renaming user.';
                return;
            }
        }

        if($this->userInfo->display_name !== $display_name) {
            if(!self::hasPermission('user.change_display_name')) {
                $this->errorMessage = 'You are not permitted to change your public name.';
                return;
            }
            $success = $dbc->users()->setDisplayName($uid, $display_name);
            if(!$success) {
                $this->errorMessage = 'Error while updating display name.';
                return;
            }
        }

        if($this->userInfo->email !== $email) {
            if(!self::hasPermission('user.change_email')) {
                $this->errorMessage = 'You are not permitted to change your email.';
                return;
            }
            $success = $dbc->users()->setEmail($uid, $email);
            if(!$success) {
                $this->errorMessage = 'Error while updating email.';
                return;
            }
        }
    }

    public function render()
    {
        $opts = array(
            'allowNameChange' => self::hasPermission('user.change_name') &&
                                 $this->settings['allow_name_changes'],
            'allowDisplayNameChange' => self::hasPermission('user.change_display_name'),
            'allowEmailChange' => self::hasPermission('user.change_email')
        );
        if($this->errorMessage !== null) {
            $opts['errorMessage'] = $this->errorMessage;
        }
        $opts['userInfo'] = $this->userInfo;
        $this->renderTemplate('settings/own/account', $opts);
    }

}

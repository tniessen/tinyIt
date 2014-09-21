<?php
namespace tniessen\tinyIt\WebUI;

use tniessen\tinyIt\Application;
use tniessen\tinyIt\Security\Authorization;

class SwitchUserPage extends Page
{
    private $errorMessage;

    private $revert;
    private $confirmed;
    private $uid;
    private $userInfo;

    public function init($params)
    {
        self::requireNonce();
        self::requirePermission('session.switch_user');

        if($this->revert) {
            if(Authorization::switched()) {
                Authorization::switchBack();
            }
            self::redirectTo('home');
            exit;
        } else {
            if($this->uid === false) {
                self::redirectTo('home');
                exit;
            }

            $dbc = Application::dbConnection();
            $this->userInfo = $dbc->users()->getUser($this->uid);
            if($this->userInfo) {
                if($this->confirmed) {
                    if(Authorization::switched()) {
                        Authorization::switchBack();
                    }

                    $s = Authorization::switchUser($this->userInfo);
                    if($s) {
                        self::redirectTo('home');
                        exit;
                    }

                    $this->errorMessage = 'Switching failed.';
                }
            }
        }
    }

    public function render()
    {
        $opts = array(
            'userInfo' => $this->userInfo
        );
        if($this->errorMessage !== null) {
            $opts['errorMessage'] = $this->errorMessage;
        }
        $this->renderTemplate('switchuser', $opts);
    }

    public static function getInstance($path, $params)
    {
        $page = parent::getInstance($path, $params);
        $page->uid = isset($params['user']) ? $params['user'] : false;
        $page->confirmed = isset($params['confirmed']);
        $page->revert = isset($params['revert']);
        return $page;
    }
}

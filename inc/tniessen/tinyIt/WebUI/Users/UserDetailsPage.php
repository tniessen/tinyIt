<?php
namespace tniessen\tinyIt\WebUI\Users;

use tniessen\tinyIt\Application;
use tniessen\tinyIt\WebUI\Page;
use tniessen\tinyIt\Security\Authorization;

class UserDetailsPage extends Page
{
    private $userId;
    private $userInfo;

    private $groupInfo;
    private $availableGroups;

    private $canSwitchUser;

    private $errorMessage;

    private $deleteMode;

    public function init($params)
    {
        self::requireLogin();

        $dbc = Application::dbConnection();

        if($uid = $this->userId) {
            $this->userInfo = $dbc->users()->getUser($uid);
            if($this->userInfo) {
                if($this->userId !== Authorization::user()->id) {
                    if(self::hasPermission('session.switch_user')) {
                        $this->canSwitchUser = true;
                    }
                }
                if($this->deleteMode) {
                    self::requireNonce();
                    $allowed = self::hasPermission('user.delete_accounts');
                    $allowed |= ($uid === Authorization::user()->id)
                             && self::hasPermission('user.delete_self');
                    if($allowed) {
                        $dbc->links()->removeLinksByUser($uid);
                        if($dbc->users()->removeUser($uid)) {
                            self::redirectTo('users/list');
                            exit;
                        } else {
                            $this->errorMessage = 'Internal error while deleting user';
                        }
                    } else {
                        $this->errorMessage = 'You are not permitted to delete this user account.';
                    }
                } else if(isset($params['setGroup'])) {
                    $newgroup = intval($params['setGroup']);
                    $ok = true;
                    if($newgroup) {
                        $g = $dbc->groups()->getGroup($newgroup);
                        if(!$g) {
                            $ok = false;
                            $this->errorMessage = 'The selected group was not found.';
                        }
                    }
                    if($ok) {
                        $dbc->users()->setGroup($uid, $newgroup);
                        $this->userInfo = $dbc->users()->getUser($uid);
                    }
                }

                if($this->userInfo->group_id) {
                    $this->groupInfo = $dbc->groups()->getGroup($this->userInfo->group_id);
                }
                $this->availableGroups = $dbc->groups()->getGroups(0, 100);
            }
        }
    }

    public function render()
    {
        $opts = array(
            'userInfo' => $this->userInfo,
            'groupInfo' => $this->groupInfo,
            'availableGroups' => $this->availableGroups,
            'canSwitchUser' => $this->canSwitchUser
        );
        if($this->errorMessage !== null) {
            $opts['errorMessage'] = $this->errorMessage;
        }
        $this->renderTemplate('users/userdetails', $opts);
    }

    public static function getInstance($path, $params)
    {
        $page = parent::getInstance($path, $params);
        $page->userId = $params['user'];
        $page->deleteMode = isset($params['delete']) && $params['delete'];
        return $page;
    }
}

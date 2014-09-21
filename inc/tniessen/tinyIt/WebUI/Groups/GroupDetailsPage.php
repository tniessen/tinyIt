<?php
namespace tniessen\tinyIt\WebUI\Groups;

use tniessen\tinyIt\Application;
use tniessen\tinyIt\WebUI\Page;

class GroupDetailsPage extends Page
{
    private $groupId;
    private $groupInfo;

    private $availableGroups;

    private $errorMessage;

    private $deleteMode;
    private $editMode;

    public function init($params)
    {
        self::requireLogin();

        $dbc = Application::dbConnection();

        if($gid = $this->groupId) {
            $this->groupInfo = $dbc->groups()->getGroup($gid);
            if($this->groupInfo) {
                if($this->editMode) {
                    if(self::hasPermission('group.edit_groups')) {
                        $postData = \tniessen\tinyIt\HttpParams::_POST();
                        if($postData && !$postData->isEmpty()) {
                            $this->tryProcessEditPostData($postData);
                        }
                    } else {
                        $this->errorMessage = 'You are not permitted to edit this group.';
                    }
                } elseif($this->deleteMode) {
                    self::requireNonce();
                    if(self::hasPermission('group.delete_groups')) {
                        $moveToGroup = 0;
                        if(isset($params['setGroup'])) {
                            $moveToGroup = intval($params['setGroup']);
                        }
                        if(!$moveToGroup || ($dbc->groups()->getGroup($moveToGroup) && $moveToGroup != $gid)) {
                            $dbc->users()->moveUsersToGroup($gid, $moveToGroup);
                            if($dbc->groups()->removeGroup($gid)) {
                                self::redirectTo('groups/list');
                                exit;
                            } else {
                                $this->errorMessage = 'Internal error while deleting group';
                            }
                        } else {
                            $this->errorMessage = 'Invalid target group for affected users.';
                        }
                    } else {
                        $this->errorMessage = 'You are not permitted to delete this group.';
                    }
                }

                $this->groupInfo->nMembers = $dbc->users()->countGroupMembers($gid);
                $this->availableGroups = $dbc->groups()->getGroups(0, 100);
            }
        }
    }

    private function tryProcessEditPostData($postData)
    {
        $reqfields = array(
            'group_name'
        );
        if(!$postData->hasValues($reqfields)) {
            $this->errorMessage = 'Please fill out all required fields.';
            return;
        }

        $fields = $reqfields;
        extract($postData->filter($fields));

        $dbc = Application::dbConnection();
        $opts = $dbc->options()->getOptions();
        extract($opts);

        if($this->groupInfo->name !== $group_name) {
            $success = $dbc->groups()->renameGroup($this->groupInfo->id, $group_name);
            if(!$success) {
                $this->errorMessage = 'An internal error occurred while renaming the group. Please try again or ask an administrator for help.';
                return;
            }
        }

        self::redirectTo('groups/details', array('group' => $this->groupInfo->id));
        exit;
    }

    public function render()
    {
        $opts = array(
            'groupInfo' => $this->groupInfo,
            'availableGroups' => $this->availableGroups,
            'editMode' => $this->editMode
        );
        if($this->errorMessage !== null) {
            $opts['errorMessage'] = $this->errorMessage;
        }
        $this->renderTemplate('groups/groupdetails', $opts);
    }

    public static function getInstance($path, $params)
    {
        $page = parent::getInstance($path, $params);
        $page->groupId = $params['group'];
        $page->editMode = isset($params['edit']) && $params['edit'];
        $page->deleteMode = isset($params['delete']) && $params['delete'];
        return $page;
    }
}

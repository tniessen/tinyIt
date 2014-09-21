<?php
namespace tniessen\tinyIt\WebUI\Groups;

use tniessen\tinyIt\Application;
use tniessen\tinyIt\WebUI\Page;

class GroupPermissionsPage extends Page
{
    private $groupId;
    private $groupInfo;
    private $groupPermissions;

    private $errorMessage;

    private $deleteMode;
    private $editMode;

    public function init($params)
    {
        self::requireLogin();
        self::requirePermission('group.view_permissions');

        $dbc = Application::dbConnection();

        if($gid = $this->groupId) {
            $this->groupInfo = $dbc->groups()->getGroup($gid);
            if($this->groupInfo) {
                if($this->editMode) {
                    if(self::hasPermission('group.edit_permissions')) {
                        if(isset($params['revoke-permission'])) {
                            self::requireNonce();
                            $perm = $params['revoke-permission'];
                            $s = $dbc->permissions()->removePermission($gid, $perm);
                            if(!$s) {
                                $this->errorMessage = 'Revoking failed.';
                            }
                        } else if(isset($params['grant-permission'])) {
                            self::requireNonce();
                            $perm = $params['grant-permission'];
                            $dbc->permissions()->addPermission($gid, $perm);
                        }
                    } else {
                        $this->errorMessage = 'You are not permitted to edit the permissions of this group.';
                    }
                }

                $this->groupPermissions = $dbc->permissions()->getPermissions($gid);
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
            'groupPermissions' => $this->groupPermissions,
            'editMode' => $this->editMode
        );
        if($this->errorMessage !== null) {
            $opts['errorMessage'] = $this->errorMessage;
        }
        $this->renderTemplate('groups/grouppermissions', $opts);
    }

    public static function getInstance($path, $params)
    {
        $page = parent::getInstance($path, $params);
        $page->groupId = $params['group'];
        $page->editMode = isset($params['edit']) && $params['edit'];
        return $page;
    }
}

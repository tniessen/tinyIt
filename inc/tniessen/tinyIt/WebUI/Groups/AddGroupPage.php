<?php
namespace tniessen\tinyIt\WebUI\Groups;

use tniessen\tinyIt\Application;
use tniessen\tinyIt\WebUI\Page;

class AddGroupPage extends Page
{
    public function init($params)
    {
        Page::requireNonce();
        Page::requirePermission('group.add_groups');

        $dbc = Application::dbConnection();
        $group_id = $dbc->groups()->addGroup('New Group');

        self::redirectTo('groups/details', array(
            'group' => $group_id,
            'edit'  => 1
        ));
        exit;
    }

    public function render()
    {
        die('init() not called');
    }
}

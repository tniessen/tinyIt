<?php
namespace tniessen\tinyIt\Security;

/**
 * Provides a list of all available permissions with associated verbal
 * information.
 */
class Permissions
{
    public static $all = array(
        'link.shorten' => array(
            'title' => 'Shorten links',
            'desc' => 'Allows users to shorten links.',
            'cat' => 'Links'
        ),
        'link.custom' => array(
            'title' => 'Use custom paths',
            'desc' => 'Allows users to use custom paths when shortening links.',
            'cat' => 'Links'
        ),
        'link.override_wildcards' => array(
            'title' => 'Override wildcards',
            'desc' => 'Allows users to override wildcards when shortening links.',
            'cat' => 'Links'
        ),
        'link.add_wildcard' => array(
            'title' => 'Create wildcards',
            'desc' => 'Allows users to add wildcards.',
            'cat' => 'Links'
        ),
        'link.delete_links' => array(
            'title' => 'Delete links',
            'desc' => 'Allows users to delete any links.',
            'cat' => 'Links'
        ),
        'link.delete_own_links' => array(
            'title' => 'Delete own links',
            'desc' => 'Allows users to delete their own links.',
            'cat' => 'Links'
        ),
        'link.edit_links' => array(
            'title' => 'Edit links',
            'desc' => 'Allows users to modify any links.',
            'cat' => 'Links'
        ),
        'link.edit_own_links' => array(
            'title' => 'Edit own links',
            'desc' => 'Allows users to modify their own links.',
            'cat' => 'Links'
        ),
        'group.add_groups' => array(
            'title' => 'Create groups',
            'desc' => 'Allows users to create groups.',
            'cat' => 'Groups'
        ),
        'group.edit_groups' => array(
            'title' => 'Edit groups',
            'desc' => 'Allows users to change group information (e.g. name).',
            'cat' => 'Groups'
        ),
        'group.delete_groups' => array(
            'title' => 'Delete groups',
            'desc' => 'Allows users to remove groups.',
            'cat' => 'Groups'
        ),
        'group.edit_permissions' => array(
            'title' => 'Change group permissions',
            'desc' => 'Allows users to change permissions of groups.',
            'cat' => 'Groups'
        ),
        'group.view_permissions' => array(
            'title' => 'View group permissions',
            'desc' => 'Allows users to view permissions of groups.',
            'cat' => 'Groups'
        ),
        'user.set_group' => array(
            'title' => 'Set user groups',
            'desc' => 'Allows users to assign groups to users.',
            'cat' => 'Users'
        ),
        'user.change_name' => array(
            'title' => 'Change username',
            'desc' => 'Allows users to change their username.',
            'cat' => 'Users'
        ),
        'user.change_email' => array(
            'title' => 'Change email',
            'desc' => 'Allows users to change their e-mail address.',
            'cat' => 'Users'
        ),
        'user.change_display_name' => array(
            'title' => 'Change public name',
            'desc' => 'Allows users to change their public name.',
            'cat' => 'Users'
        ),
        'user.delete_accounts' => array(
            'title' => 'Delete user accounts',
            'desc' => 'Allows users to delete other user accounts.',
            'cat' => 'Users'
        ),
        'user.delete_self' => array(
            'title' => 'Delete own user account',
            'desc' => 'Allows users to delete their own accounts.',
            'cat' => 'Users'
        ),
        'settings.change_site_settings' => array(
            'title' => 'Change site settings',
            'desc' => 'Allows users to access and change site settings.',
            'cat' => 'Settings'
        ),
        'session.switch_user' => array(
            'title' => 'Switch to user accounts',
            'desc' => 'Allows users to switch to other user accounts, keeping their own permissions.',
            'cat' => 'Session'
        )
    );
}

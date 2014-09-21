<?php
namespace tniessen\tinyIt\Security;

use \tniessen\tinyIt\Database\UsersTableAdapter;
use \tniessen\tinyIt\Application;

class Permission
{
    private static $permissions = array();

    /**
     * Retrieves a user from the current authorization context which should be
     * used for permission checks.
     */
    public static function currentUser()
    {
        if(Authorization::switched()) {
            return Authorization::realUser();
        }
        return Authorization::user();
    }

    /**
     * Checks whether the currently authorized user has a permission.
     *
     * @param string $what
     * @return bool
     */
    public static function currentUserCan($what)
    {
        $user = self::currentUser();
        return self::userCan($user, $what);
    }

    /**
     * Checks whether a user has a permission.
     *
     * @param mixed $user
     * @param string $what
     * @return bool
     */
    public static function userCan($user, $what)
    {
        if(!is_object($user)) {
            $dbc = Application::dbConnection();
            $user = $dbc->users()->getUser($user);
        }

        if($user->flags & UsersTableAdapter::FLAG_ALMIGHTY) {
            // Grant all permissions
            return true;
        }

        // Permissions inherited from group
        if($user->group_id !== null) {
            return self::groupCan($user->group_id, $what);
        }

        return false;
    }

    /**
     * Checks whether the members of a group have a permission.
     *
     * @param int $group
     * @param string $what
     * @return bool
     */
    public static function groupCan($group, $what)
    {
        if(!isset(self::$permissions[$group])) {
            $dbc = Application::dbConnection();
            $perms = $dbc->permissions()->getPermissions($group);
            self::$permissions[$group] = $perms;
        } else {
            $perms = self::$permissions[$group];
        }
        return in_array($what, $perms, true);
    }
}

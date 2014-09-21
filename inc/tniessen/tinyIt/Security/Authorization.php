<?php
namespace tniessen\tinyIt\Security;

use \tniessen\tinyIt\Application;
use \tniessen\tinyIt\Cryptography;

class Authorization
{
    private static $userInfo;
    private static $realUserInfo;

    /**
     * Initializes the authorization module.
     */
    public static function init()
    {
        session_start();

        if(isset($_SESSION['tiUserId'])) {
            $id = $_SESSION['tiUserId'];
            if($id !== null) {
                self::user('reload');

                if(!self::loggedIn()) {
                    self::clearSessionData();
                }

                if(self::switched()) {
                    self::realUser('reload');
                }
            }
        }
    }

    /**
     * Modifies the current session to belong to another user.
     *
     * As this function could be a serious security threat, it must not be used
     * without proper permission checks.
     */
    public static function switchUser($newuser)
    {
        if(!self::loggedIn()) {
            return false;
        }

        if(!is_object($newuser)) {
            $dbc = Application::dbConnection();
            $newuser = $dbc->users()->getUser($newuser);
        }

        if(!$newuser || $newuser->id === self::user()->id) {
            return false;
        }

        $_SESSION['tiRealUserId'] = self::$userInfo->id;

        self::$userInfo = $newuser;
        $_SESSION['tiUserId'] = $newuser->id;

        return true;
    }

    public static function switchBack()
    {
        if(!self::switched()) {
            return false;
        }

        $_SESSION['tiUserId'] = $_SESSION['tiRealUserId'];
        $_SESSION['tiRealUserId'] = null;

        self::$userInfo = self::$realUserInfo;
        self::$realUserInfo = null;

        return true;
    }

    public static function switched()
    {
        return self::loggedIn() &&
               isset($_SESSION['tiRealUserId']) && $_SESSION['tiRealUserId'];
    }

    public static function realUser($mode = null)
    {
        if($mode === 'reload') {
            if(isset($_SESSION['tiUserId'])) {
                $dbc = Application::dbConnection();
                self::$realUserInfo = $dbc->users()->getUser($_SESSION['tiRealUserId']);
            }
        }
        return self::$realUserInfo;
    }

    /**
     * Retrieves the user object of the currently logged in user as loaded fro
     * the database.
     *
     * @param string $mode Set to `reload` to force a reload of user data.
     */
    public static function user($mode = null)
    {
        if($mode === 'reload') {
            if(isset($_SESSION['tiUserId'])) {
                $dbc = Application::dbConnection();
                self::$userInfo = $dbc->users()->getUser($_SESSION['tiUserId']);
            }
        }
        return self::$userInfo;
    }

    /**
     * Returns whether authorization succeeded and the user is therefore logged
     * in.
     *
     * @return boolean
     */
    public static function loggedIn()
    {
        return !!self::$userInfo;
    }

    /**
     * Checks whether a given string is a valid nonce in the scope of the
     * current session.
     *
     * This function should only be used for authorized
     * users.
     *
     * @param string $nonce
     */
    public static function isNonce($nonce)
    {
        return (isset($_SESSION['tiNonce']) && $_SESSION['tiNonce'] === $nonce);
    }

    /**
     * Returns the nonce associated with the current session.
     *
     * @return string
     */
    public static function getNonce()
    {
        return isset($_SESSION['tiNonce']) ? $_SESSION['tiNonce'] : null;
    }

    /**
     * Attempts to create an authorized session using given credentials.
     *
     * @param string $name
     * @param string $password
     *
     * @see Database\UsersTableAdapter::getUserByName
     * @see Cryptography::check
     */
    public static function login($name, $password)
    {
        $dbc = Application::dbConnection();
        $user = $dbc->users()->getUserByName($name);

        if(!$user) return false;

        $correctPassword = Cryptography::check($password, $user->password);
        if($correctPassword) {
            $_SESSION['tiUserId'] = $user->id;
            $_SESSION['tiNonce']  = sha1(microtime(true));
            self::$userInfo       = $user;
            return $user->id;
        } else {
            self::clearSessionData();
            self::$userInfo       = null;
            return false;
        }
    }

    /**
     * Clears session data used by the authorization module.
     */
    private static function clearSessionData()
    {
        $_SESSION['tiUserId']     = null;
        $_SESSION['tiRealUserId'] = null;
        $_SESSION['tiNonce']      = null;
    }

    /**
     * Marks the session as unauthorized.
     */
    public static function logout()
    {
        self::clearSessionData();
    }
}

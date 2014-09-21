<?php
namespace tniessen\tinyIt;

/**
 * Cryptography utility class.
 */
class Cryptography
{
    /**
     * Calculates a hash of a password.
     *
     * This function uses blowfish (eight rounds).
     *
     * @param string $password
     * @return string
     */
    public static function hash($password)
    {
        $salt = substr(str_replace('+', '.', base64_encode(sha1(microtime(true), true))), 0, 22);
        return crypt($password, "$2a$08$salt");
    }

    /**
     * Checks a password against a pre-calculated hash.
     *
     * @see Cryptography::hash
     * @return bool
     */
    public static function check($password, $hash)
    {
        return crypt($password, $hash) === $hash;
    }
}
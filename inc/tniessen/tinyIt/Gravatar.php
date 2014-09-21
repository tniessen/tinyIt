<?php
namespace tniessen\tinyIt;

/**
 * Server-side wrapper around the Gravatar API.
 */
class Gravatar
{
    /**
     * Builds a URL which can be used to download the gravatar of a user
     * identified by his/her email.
     *
     * The <a href="https://gravatar.com/site/implement/images/">Gravatar API
     * documentation</a> describes the following parameters in detail.
     *
     * @param string $email
     * @param bool $secure
     * @param int $size
     * @param string $def
     * @param string $rating
     * @return URL
     */
    public static function getURL($email, $secure = false, $size = 80, $def = 'mm', $rating = 'g')
    {
        if($secure) {
            $url = new URL('https', 'secure.gravatar.com');
        } else {
            $url = new URL('http', 'www.gravatar.com');
        }
        $url->path = '/avatar/' . md5($email);
        $url->query = URL::buildQuery(array(
            's' => $size,
            'd' => $def,
            'r' => $rating
        ));
        return $url;
    }
}

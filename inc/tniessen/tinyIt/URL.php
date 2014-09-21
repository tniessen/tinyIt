<?php
namespace tniessen\tinyIt;

class URL
{
    /**
     * The protocol (scheme) of this URL.
     *
     * @var string
     */
    public $protocol;

    /**
     * The host of this URL.
     *
     * @var string
     */
    public $host;

    /**
     * The port of this URL (optional).
     *
     * @var int
     */
    public $port;

    /**
     * The path of this URL.
     *
     * @var string
     */
    public $path;

    /**
     * The query of this URL.
     *
     * @var string
     */
    public $query;

    /**
     * The fragment of this URL.
     *
     * @var string
     */
    public $fragment;

    /**
     * Creates a new URL instance with the given protocol and host.
     *
     * Protocol (scheme) and host can optionally be followed by port, path,
     * query and fragment.
     *
     * @param string $protocol
     * @param string $host
     * @param int    $port
     * @param string $path
     * @param string $query
     * @param string $fragment
     */
    public function __construct($protocol, $host, $port = null, $path = '/', $query = '', $fragment = '')
    {
        $this->protocol = $protocol;
        $this->host = $host;
        $this->port = ($port !== null) ? intval($port) : null;
        $this->path = $path;
        $this->query = $query;
        $this->fragment = $fragment;
    }

    /**
     * Constructs a string representation of this URL.
     *
     * @param bool $skipPort whether default ports should be omitted.
     * @return string
     */
    public function build($skipPort = true)
    {
        $url = $this->protocol . '://' . $this->host;

        if($this->port !== null) {
            $skipPort &= self::isDefaultPort($this->protocol, $this->port);
            if(!$skipPort) $url .= ':' . $this->port;
        }

        $url .= $this->path;

        if($this->query) $url .= '?' . $this->query;

        if($this->fragment) $url .= '#' . $this->fragment;

        return $url;
    }

    /**
     * Parses the query string and returns an array of `key => value` mappings
     * representing the contained data.
     *
     * @return array
     */
    public function parseQuery()
    {
        $q = array();
        parse_str($this->query, $q);
        return $q;
    }

    /**
     * Requests the client to change the window location to this URL.
     *
     * As this function uses the `Location` header in order to instruct the
     * client browser to change the URL, ensure that no output is made before
     * (or after) calling this function. Do not overwrite the `Location`
     * header.
     *
     * @param int $code the HTTP status code, 301 or 302
     */
    public function redirectTo($code = 302)
    {
        header('Location: ' . $this->build(), true, $code);
    }

    /**
     * Checks whether two URLs are equal.
     *
     * Two URLs are considered equal if they share the same protocol, host,
     * port, path, query and fragment.
     */
    public function equals($url)
    {
        return ($this->protocol === $url->protocol) &&
               ($this->host     === $url->host    ) &&
               ($this->port     === $url->port    ) &&
               ($this->path     === $url->path    ) &&
               ($this->query    === $url->query   ) &&
               ($this->fragment === $url->fragment);
    }

    /**
     * Checks whether two URLs have the same base.
     *
     * "Base" refers to protocol (scheme), host and port.
     *
     * @param URL $target
     * @param bool $ignoreProtocol Whether to ignore the protocol
     * @return bool
     */
    public function hasSameBase($target, $ignoreProtocol = false)
    {
        return ($target->host === $this->host) &&
               ($target->port === $this->port) &&
               ($ignoreProtocol xor $target->protocol === $this->protocol);
    }

    /**
     * Retrieves the directory component of the path.
     *
     * The returned path will always end with a URL path separator (slash).
     *
     * @return string
     */
    public function pathDirname() {
        if(substr($this->path, -1) === '/') {
            return $this->path;
        }
        return dirname($this->path) . '/';
    }

    /**
     * Parses a URL from its string representation.
     *
     * @param string $url
     * @param string $defProtocol Default protocol
     * @return URL
     */
    public static function parse($url, $defProtocol = null)
    {
        $parsed = parse_url($url);
        $scheme = isset($parsed['scheme']) ? $parsed['scheme'] : $defProtocol;
        $port = isset($parsed['port']) && $parsed['port'] ? $parsed['port'] : null;
        return new URL(
            $scheme,
            isset($parsed['host'])     ? $parsed['host']     : null,
            $port,
            isset($parsed['path'])     ? $parsed['path']     : null,
            isset($parsed['query'])    ? $parsed['query']    : null,
            isset($parsed['fragment']) ? $parsed['fragment'] : null
        );
    }

    /**
     * Checks whether a port is the default port for a protocol.
     *
     * This function currently supports the following protocols:
     * - http (80)
     * - https (443)
     *
     * @param string $protocol
     * @param int    $port
     * @return bool
     */
    final public static function isDefaultPort($protocol, $port)
    {
        switch(strtolower($protocol)) {
            case 'http':
                return ($port == 80);
            case 'https':
                return ($port == 443);
            default:
                return false;
        }
    }

    /**
     * Constructs a string representation of the given parameter array.
     *
     * @param array $params
     * @return string
     */
    public static function buildQuery($params) {
        $query = '';
        foreach($params as $key => $value) {
            if(strlen($query) > 0) $query .= '&';
            $query .= urlencode($key) . '=' . urlencode($value);
        }
        return $query;
    }

    /**
     * Returns the requested URL as sent by the client.
     *
     * The returned URL might differ from the URL returned by URL::getReal due
     * to prior rewrites. Whilst URL::getReal will always point to the entry
     * point of the application, URL::getCurrent might point to an arbitrary
     * path.
     *
     * If rewrites are disabled (e.g. during installation), this URL should not
     * differ from the URL returned by URL::getReal.
     *
     * @return URL
     */
    public static function getCurrent()
    {
        $path = $_SERVER['REQUEST_URI'];
        $qpos = strpos($path, '?');
        if($qpos !== false) {
            $path = substr($path, 0, $qpos);
        }

        $host = $_SERVER['SERVER_NAME'];
        if(isset($_SERVER['HTTP_HOST'])) {
            $host = $_SERVER['HTTP_HOST'];
        }

        $url = new URL(
            // Protocol
            isset($_SERVER['HTTPS']) ? 'https' : 'http',
            // Host
            $host,
            // Port
            $_SERVER['SERVER_PORT'],
            // Path
            $path,
            // Query
            $_SERVER['QUERY_STRING']
        );

        return $url;
    }

    /**
     * Returns the real requested URL.
     *
     * The result might differ from the URL returned by URL::getCurrent due to
     * prior rewrites. The returned URL should always point to the application
     * entry point (a PHP file) and could therefore differ from the URL visible
     * to the client.
     *
     * @return URL
     */
    public static function getReal()
    {
        $url = self::getCurrent();
        $url->path = $_SERVER['SCRIPT_NAME'];
        return $url;
    }
}

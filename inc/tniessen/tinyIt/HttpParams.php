<?php
namespace tniessen\tinyIt;

class HttpParams
{
    private static $_get;
    private static $_post;

    private $values;

    /**
     * Creates a new instance of this class consisting of
     * the values provided as an array.
     *
     * @param array $values
     */
    public function __construct($values)
    {
        $this->values = $values;
    }

    /**
     * Checks whether this set of values is empty.
     *
     * @return bool
     */
    public function isEmpty()
    {
        return count($this->values) === 0;
    }

    /**
     * Checks whether all given keys exist in this set of values.
     *
     * @param string|array $key
     * @return bool
     */
    public function has($key)
    {
        $all = is_array($key) ? $key : array($key);
        foreach($all as $key) {
            if(!isset($this->values[$key])) {
                return false;
            }
        }
        return true;
    }

    /**
     * Checks whether this set of values contains values for all given keys.
     *
     * As this function was designed to verify the integrity of GET / POST
     * parameters, this set is considered to contain a value if its key exists
     * and it is not an empty string.
     *
     * @param string|array $key
     * @return bool
     * @see HttpParams::has
     */
    public function hasValues($key)
    {
        $all = is_array($key) ? $key : array($key);
        foreach($all as $key) {
            if(!isset($this->values[$key]) || $this->values[$key] === '') {
                return false;
            }
        }
        return true;
    }

    /**
     * Retrieves a value from this set.
     *
     * @param string $key
     * @param string $default
     * @return string
     */
    public function get($key, $default = null)
    {
        if(isset($this->values[$key])) {
            return $this->values[$key];
        }
        return $default;
    }

    /**
     * Retrieves the values associated with multiple keys.
     *
     * @param string|array $key
     * @return array
     */
    public function filter($key)
    {
        $all = is_array($key) ? $key : array($key);
        $values = array();
        foreach($all as $key) {
            $values[$key] = $this->get($key);
        }
        return $values;
    }

    /**
     * Returns the array of `key => value` mappings this instance is based on.
     *
     * @return array
     */
    public function values()
    {
        return $this->values;
    }

    /**
     * Returns an instance of HttpParams representing all values passed as
     * HTTP GET parameters.
     *
     * The created instance will be cached for subsequent calls.
     */
    public static function _GET()
    {
        if(!self::$_get) {
            $values = $_GET;
            self::$_get = new HttpParams($values);
        }
        return self::$_get;
    }

    /**
     * Returns an instance of HttpParams representing all values passed as
     * HTTP POST parameters.
     *
     * The created instance will be cached for subsequent calls.
     */
    public static function _POST()
    {
        if(!self::$_post) {
            $values = $_POST;
            self::$_post = new HttpParams($values);
        }
        return self::$_post;
    }

}

<?php
namespace tniessen\tinyIt\Database;

/**
 * This class manages MySQL database access.
 *
 * The following database schema is used:
 * - users(**id**, name, name_lc, display_name, password, email, registered, &#8593;group_id, flags)
 * - groups(**id**, name)
 * - permissions(**&#8593;group_id**, **name**)
 * - options(**name**, value)
 * - links(**id**, type, path, target, &#8593;owner_id, priority)
 *
 */
class DatabaseConnection
{
    /**
     * The default MySQL port
     */
    const DEFAULT_PORT = 3306;

    /**
     * The database version implemented by this class
     */
    const DB_VERSION   = 1;

    private $host;
    private $port;

    private $dbname;
    private $tablePrefix;

    private $dbh;
    private $users;
    private $options;
    private $links;
    private $groups;
    private $permissions;

    /**
     * Creates a new DatabaseConnection.
     */
    public function __construct($host, $port = DEFAULT_PORT)
    {
        $this->host = $host;
        $this->port = $port;
        $this->dbh = null;
    }

    /**
     * Initializes the connection.
     *
     * If a table prefix is passed, it will be prepended to all table names.
     */
    public function init($dbname, $tablePrefix = '')
    {
        $this->dbname = $dbname;
        $this->tablePrefix = $tablePrefix;
    }

    /**
     * Starts this connection by connecting to the MySQL server.
     *
     * @throws \PDOException If connecting fails
     */
    public function connect($user, $password)
    {
        $dsn = 'mysql:host=' . $this->host . ';port=' . $this->port
             . ';dbname=' . $this->dbname;
        $this->dbh = new \PDO($dsn, $user, $password);
        $this->dbh->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }

    /**
     * Disconnects from the server.
     *
     * The connection will not be terminated unless all
     * cursors have been closed and all references to
     * `PDOStatement`s have been cleared.
     */
    public function disconnect()
    {
        $this->dbh = null;
    }

    /**
     * Checks whether this instance is currently connected.
     *
     * @return bool
     */
    public function isConnected()
    {
        return ($this->dbh != null);
    }

    public function isVersionSupported()
    {
        return getInstalledVersion() <= DB_VERSION;
    }

    public function isUpgradeRequired()
    {
        return getInstalledVersion() < DB_VERSION;
    }

    /**
     * Retrieves the installed database version.
     *
     * If the value could not be retrieved, `false` will be returned.
     *
     * @return int
     */
    public function getInstalledVersion()
    {
        try {
            $val = $this->options()->getOption('db_version');
        } catch(\PDOException $e) {
            return false;
        }
        return $val ? intval($val) : false;
    }

    /**
     * Checks whether the database is installed.
     *
     * The database is considered to be installed if the `db_version` option
     * can be retrieved.
     *
     * @return bool
     * @see DatabaseConnection::getInstalledVersion
     */
    public function isInstalled()
    {
        return !!$this->getInstalledVersion();
    }

    /**
     * Installs all tables.
     *
     * Creates the following tables:
     * - `users`
     * - `groups`
     * - `permissions`
     * - `options`
     * - `links`
     *
     * The actual table names may contain a prefix as specified
     * by calling `init`.
     */
    public function installDatabase()
    {
        $sql = 'CREATE TABLE ' . $this->tbl('users') . "(
                  id           INT           AUTO_INCREMENT PRIMARY KEY,
                  name         VARCHAR(20)   NOT NULL,
                  name_lc      VARCHAR(20)   NOT NULL UNIQUE KEY,
                  display_name VARCHAR(32)   NOT NULL,
                  password     VARCHAR(60)   NOT NULL,
                  email        VARCHAR(40)   NOT NULL,
                  registered   INT           NOT NULL,
                  group_id     INT           DEFAULT NULL,
                  flags        INT           NOT NULL
                )";
        $this->dbh->exec($sql);
       $sql = 'CREATE TABLE ' . $this->tbl('groups') . "(
                  id           INT           AUTO_INCREMENT PRIMARY KEY,
                  name         VARCHAR(20)   NOT NULL
                )";
        $this->dbh->exec($sql);
        $sql = 'CREATE TABLE ' . $this->tbl('permissions') . "(
                  group_id     INT           NOT NULL,
                  name         VARCHAR(80)   NOT NULL,
                  PRIMARY KEY(group_id, name)
                )";
        $this->dbh->exec($sql);
        $sql = 'CREATE TABLE ' . $this->tbl('options') . "(
                  name         CHAR(60)      NOT NULL PRIMARY KEY,
                  value        VARCHAR(8192)
                )";
        $this->dbh->exec($sql);
        $sql = 'CREATE TABLE ' . $this->tbl('links') . "(
                  id           INT           AUTO_INCREMENT PRIMARY KEY,
                  type         CHAR(12)      NOT NULL,
                  path         VARCHAR(255)  NOT NULL,
                  target       VARCHAR(1024) NOT NULL,
                  owner_id     INT           NOT NULL,
                  priority     INT,
                  INDEX(type),
                  INDEX(path)
                )";
        $this->dbh->exec($sql);
    }

    /**
     * Sets a number of default options.
     *
     * @throws \PDOException If a fatal error occurs
     * @see DatabaseConnection::options
     */
    public function setDefaultOptions()
    {
        $this->options()->setOptions(array(
            'db_version'         => self::DB_VERSION,
            // General
            'home_action'        => 'show_admin',
            // Links
            'linkgen_chars'      => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789',
            'linkgen_length'     => 5,
            'custom_links_regex' => '^[a-zA-Z0-9]+$',
            // Users
            'allow_registration' => false,
            'allow_name_changes' => true
        ));
    }

    /**
     * Returns the actual name of the table.
     *
     * If no prefix has been set, the returned name will be equal to the passed
     * string. If a prefix has been set, it will be prepended to the passed
     * string.
     *
     * @param string $name
     * @return string
     */
    private function tbl($name)
    {
        return $this->tablePrefix . $name;
    }

    /**
     * Retrieves the `LAST_INSERT_ID()`.
     *
     * @return int
     * @throws \PDOException
     */
    public function lastInsertId() 
    {
        $stmt = $this->select(null, 'LAST_INSERT_ID()');
        $id = $stmt->fetch(\PDO::FETCH_COLUMN);
        $stmt->closeCursor();
        return $id;
    }

    /**
     * Executes a `SELECT` statement.
     *
     * @param string $tbl
     * @param string $columns
     * @param string $where
     * @param string $whereArgs
     * @param string $orderBy
     * @param string $limit
     * @throws \PDOException
     * @return \PDOStatement
     */
    public function select($tbl, $columns = '*', $where = '', $whereArgs = null, $orderBy = null, $limit = null)
    {
        $sql = 'SELECT ' . $columns;
        if($tbl) {
            $sql .= ' FROM ' . $this->tbl($tbl);
        }
        if($where) {
            $sql .= ' WHERE ' . $where;
        }
        if($orderBy) {
            $sql .= ' ORDER BY ' . $orderBy;
        }
        if($limit !== null) {
            $sql .= ' LIMIT ' . $limit;
        }
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute($whereArgs);
        return $stmt;
    }

    /**
     * Executes an `UPDATE` statement.
     *
     * @param string $tbl
     * @param string $valspec
     * @param string $valueArgs
     * @param string $where
     * @param string $whereArgs
     * @throws \PDOException
     * @return \PDOStatement
     */
    public function update($tbl, $valspec, $valueArgs = null, $where = '', $whereArgs = null)
    {
        $sql = 'UPDATE ' . $this->tbl($tbl) . ' SET ' . $valspec;
        if($where) {
            $sql .= ' WHERE ' . $where;
        }
        $stmt = $this->dbh->prepare($sql);
        $args = array();
        if($valueArgs) $args = array_merge($args, $valueArgs);
        if($whereArgs) $args = array_merge($args, $whereArgs);
        $stmt->execute($args);
        return $stmt;
    }

    /**
     * Executes an `INSERT` statement.
     *
     * @param string $tbl
     * @param string $valspec
     * @param string $valueArgs
     * @param string $onDuplicate
     * @param string $onDuplicateArgs
     * @throws \PDOException
     * @return \PDOStatement
     */
    public function insert($tbl, $valspec, $valueArgs = null, $onDuplicate = null, $onDuplicateArgs = null)
    {
        $sql = 'INSERT INTO ' . $this->tbl($tbl) . ' SET ' . $valspec;
        if($onDuplicate) {
            $sql .= 'ON DUPLICATE KEY UPDATE ' . $onDuplicate;
        }
        $args = array();
        $stmt = $this->dbh->prepare($sql);
        if($valueArgs) $args = array_merge($args, $valueArgs);
        if($onDuplicateArgs) $args = array_merge($args, $onDuplicateArgs);
        $stmt->execute($args);
        return $stmt;
    }

    /**
     * Executes a `DELETE` statement.
     *
     * @param string $tbl
     * @param string $where
     * @param string $whereArgs
     * @param string $limit
     * @throws \PDOException
     * @return \PDOStatement
     */
    public function delete($tbl, $where = '', $whereArgs = null, $limit = null)
    {
        $sql = 'DELETE FROM ' . $this->tbl($tbl);
        if($where) {
            $sql .= ' WHERE ' . $where;
        }
        if($limit !== null) {
            $sql .= ' LIMIT ' . $limit;
        }
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute($whereArgs);
        return $stmt;
    }

    /**
     * Returns a {@link UsersTableAdapter} bound to this connection.
     *
     * @return UsersTableAdapter
     */
    public function users()
    {
        if($this->users == null) {
            $this->users = new UsersTableAdapter($this, 'users');
        }
        return $this->users;
    }

    /**
     * Returns an {@link OptionsTableAdapter} bound to this connection.
     *
     * @return OptionsTableAdapter
     */
    public function options()
    {
        if($this->options == null) {
            $this->options = new OptionsTableAdapter($this, 'options');
        }
        return $this->options;
    }

    /**
     * Returns a {@link LinksTableAdapter} bound to this connection.
     *
     * @return LinksTableAdapter
     */
    public function links()
    {
        if($this->links == null) {
            $this->links = new LinksTableAdapter($this, 'links');
        }
        return $this->links;
    }

    /**
     * Returns a {@link GroupsTableAdapter} bound to this connection.
     *
     * @return GroupsTableAdapter
     */
    public function groups()
    {
        if($this->groups == null) {
            $this->groups = new GroupsTableAdapter($this, 'groups');
        }
        return $this->groups;
    }

    /**
     * Returns a {@link PermissionsTableAdapter} bound to this connection.
     *
     * @return PermissionsTableAdapter
     */
    public function permissions()
    {
        if($this->permissions == null) {
            $this->permissions = new PermissionsTableAdapter($this, 'permissions');
        }
        return $this->permissions;
    }

    /**
     * Tests whether a connection can be established.
     *
     * @param string $host
     * @param int    $port
     * @param string $dbname
     * @param string $user
     * @param string $password
     * @return mixed `true` or {@link \PDOException}
     */
    public static function test($host, $port, $dbname, $user, $password)
    {
        try {
            $conn = new DatabaseConnection($host, $port);
            $conn->init($dbname);
            $conn->connect($user, $password);
            return true;
        } catch(\PDOException $e) {
            return $e;
        }
    }
}

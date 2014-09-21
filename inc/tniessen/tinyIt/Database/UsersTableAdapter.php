<?php
namespace tniessen\tinyIt\Database;

class UsersTableAdapter extends TableAdapter
{
    const FLAG_ALMIGHTY = 1;

    /**
     * Adds a user.
     *
     * @param string $username
     * @param string $displayName
     * @param string $email
     * @param string $password
     * @return int
     * @throws \PDOException
     */
    public function addUser($username, $displayName, $email, $password)
    {
        $this->insert('name=?, name_lc=?, display_name=?, email=?, password=?, registered=' . time(), array(
            $username,
            strtolower($username),
            $displayName,
            strtolower(trim($email)),
            \tniessen\tinyIt\Cryptography::hash($password)
        ));
        return $this->dbc->lastInsertId();
    }

    /**
     * Removes a user.
     *
     * @param int $id
     * @throws \PDOException
     */
    public function removeUser($id)
    {
        $stmt = $this->delete("id=$id");
        $nRows = $stmt->rowCount();
        $stmt->closeCursor();
        return $nRows;
    }

    /**
     * Retrieves a user by his id.
     *
     * @param int $id
     * @throw \PDOException
     */
    public function getUser($id)
    {
        $stmt = $this->select('*', "id=$id");
        $user = $stmt->fetch(\PDO::FETCH_OBJ);
        $stmt->closeCursor();
        return $user;
    }

    /**
     * Retrieves a user by his name.
     *
     * @param string $name
     * @throw \PDOException
     */
    public function getUserByName($name)
    {
        $lcname = strtolower($name);
        $stmt = $this->select('*', "name_lc=?", array($lcname));
        $user = $stmt->fetch(\PDO::FETCH_OBJ);
        $stmt->closeCursor();
        return $user;
    }

    /**
     * Retrieves multiple users from the database.
     *
     * @param int    $offset
     * @param int    $count
     * @param string $orderBy
     * @return array
     * @throws \PDOException
     */
    public function getUsers($offset, $count, $orderBy = 'name ASC')
    {
        $stmt = $this->select('*', null, null, $orderBy, "$offset, $count");
        $rows = $stmt->fetchAll(\PDO::FETCH_OBJ);
        $stmt->closeCursor();
        return $rows;
    }

    /**
     * Renames a user.
     *
     * This function does not prevent name collisions. Ensure that no user with
     * a conflicting name exists before renaming a user.
     *
     * @param int    $id
     * @param string $name
     * @return bool whether the operation was successful
     * @throw \PDOException
     */
    public function renameUser($id, $name)
    {
        $lcname = strtolower($name);
        $stmt = $this->update('name=?, name_lc=?', array($name, $lcname), "id=$id");
        $success = $stmt->rowCount();
        $stmt->closeCursor();
        return $success;
    }

    /**
     * Sets the display name of a user.
     *
     * @param int    $id
     * @param string $displayName
     * @return bool whether the operation was successful
     * @throw \PDOException
     */
    public function setDisplayName($id, $displayName)
    {
        $stmt = $this->update('display_name=?', array($displayName), "id=$id");
        $success = $stmt->rowCount();
        $stmt->closeCursor();
        return $success;
    }

    /**
     * Sets the email of a user.
     *
     * @param int    $id
     * @param string $email
     * @return bool whether the operation was successful
     * @throw \PDOException
     */
    public function setEmail($id, $email)
    {
        $stmt = $this->update('email=?', array($email), "id=$id");
        $success = $stmt->rowCount();
        $stmt->closeCursor();
        return $success;
    }

    /**
     * Sets the group of a user.
     *
     * @param int   $id
     * @param int   $group
     * @return bool whether the operation was successful
     * @throw \PDOException
     */
    public function setGroup($id, $group)
    {
        $stmt = $this->update("group_id=?", array($group ? $group : null), "id=$id");
        $success = $stmt->rowCount();
        $stmt->closeCursor();
        return $success;
    }

    public function moveUsersToGroup($fromGroup, $toGroup)
    {
        $stmt = $this->update("group_id=?", array($toGroup ? $toGroup : null), "group_id=?", array($fromGroup ? $fromGroup : null));
        $nRows = $stmt->rowCount();
        $stmt->closeCursor();
        return $nRows;
    }

    /**
     * Sets the flags attribute of a user.
     *
     * @param int   $id
     * @param int   $flags
     * @return bool whether the operation was successful
     * @throw \PDOException
     */
    public function setFlags($id, $flags)
    {
        $stmt = $this->update("flags=$flags", null, "id=$id");
        $success = $stmt->rowCount();
        $stmt->closeCursor();
        return $success;
    }

    public function countGroupMembers($group_id)
    {
        $stmt = $this->select('COUNT(*)', "group_id=$group_id");
        $n = $stmt->fetch(\PDO::FETCH_COLUMN);
        $stmt->closeCursor();
        return $n;
    }
}

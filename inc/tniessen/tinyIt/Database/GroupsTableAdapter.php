<?php
namespace tniessen\tinyIt\Database;

class GroupsTableAdapter extends TableAdapter
{
    /**
     * Adds a group.
     *
     * @param string $name
     * @throws \PDOException
     */
    public function addGroup($name)
    {
        $this->insert('name=?', array(
            $name
        ));
        return $this->dbc->lastInsertId();
    }

    /**
     * Removes a group.
     *
     * @param int $id
     * @throws \PDOException
     */
    public function removeGroup($id)
    {
        $stmt = $this->delete("id=$id");
        $nRows = $stmt->rowCount();
        $stmt->closeCursor();
        return $nRows;
    }

    /**
     * Retrieves a group by its id.
     *
     * @param int $id
     * @throw \PDOException
     */
    public function getGroup($id)
    {
        $stmt = $this->select('*', "id=$id");
        $group = $stmt->fetch(\PDO::FETCH_OBJ);
        $stmt->closeCursor();
        return $group;
    }

    /**
     * Retrieves multiple groups from the database.
     *
     * @param int    $offset
     * @param int    $count
     * @param string $orderBy
     * @return array
     * @throws \PDOException
     */
    public function getGroups($offset, $count, $orderBy = 'name ASC')
    {
        $stmt = $this->select('*', null, null, $orderBy, "$offset, $count");
        $rows = $stmt->fetchAll(\PDO::FETCH_OBJ);
        $stmt->closeCursor();
        return $rows;
    }

    /**
     * Renames a group.
     *
     * This function does not prevent name collisions. Ensure that no group
     * with a conflicting name exists before renaming a group.
     *
     * @param int    $id
     * @param string $name
     * @return bool whether the operation was successful
     * @throw \PDOException
     */
    public function renameGroup($id, $name)
    {
        $stmt = $this->update('name=?', array($name), "id=$id");
        $success = $stmt->rowCount();
        $stmt->closeCursor();
        return $success;
    }
}

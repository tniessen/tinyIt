<?php
namespace tniessen\tinyIt\Database;

class PermissionsTableAdapter extends TableAdapter
{
    /**
     * Adds a permission.
     *
     * @param int    $group
     * @param string $name
     * @throws \PDOException
     */
    public function addPermission($group, $name)
    {
        $this->insert("group_id=$group, name=?", array(
            $name
        ));
    }

    /**
     * Removes a permission.
     *
     * @param int    $group
     * @param string $name
     * @return whether the operation was successful
     * @throws \PDOException
     */
    public function removePermission($group, $name)
    {
        $stmt = $this->delete("group_id=$group AND name=?", array(
            $name
        ));
        $nRows = $stmt->rowCount();
        $stmt->closeCursor();
        return $nRows;
    }

    /**
     * Retrieves permissions of a group.
     *
     * @param int $group
     * @return array
     * @throws \PDOException
     */
    public function getPermissions($group)
    {
        $stmt = $this->select("name", "group_id=$group");
        $rows = $stmt->fetchAll(\PDO::FETCH_COLUMN);
        $stmt->closeCursor();
        return $rows;
    }

    /**
     * Removes permissions of a group.
     *
     * @param int $group
     * @return int number of removed permissions
     * @throws \PDOException
     */
    public function removePermissions($group)
    {
        $stmt = $this->delete("group_id=$group");
        $nRows = $stmt->rowCount();
        $stmt->closeCursor();
        return $nRows;
    }
}

<?php
namespace tniessen\tinyIt\Database;

class LinksTableAdapter extends TableAdapter
{
    /**
     * Generates a random path which must not conflict with other links.
     *
     * This function will generate random paths until the generated path cannot
     * be resolved anymore. A path which cannot be resolved is considered free
     * and therefore eligible for use.
     *
     * Improper use of this function can lead to fatal issues. For example, the
     * wildcard `.+` will lead to an infinite loop during the execution of this
     * function.
     *
     * The probability of finding a solution within `x` attempts can be
     * described as
     *
     * `p(Success) = 1 - (s / c ^ l) ^ x`
     *
     * where `s` is the number of existing paths with the length `l`, `c` is
     * the number of available characters (`strlen($chars)`) and `l` is the
     * length of the paths (`$length`). This equation ignores the existence of
     * wildcards and resulting collisions.
     *
     * The most important performance factor is `s / c ^ l`, where `c ^ l` is
     * the total number of available paths, including `s` paths which already
     * exist. The smaller `s / c ^ l` is, the faster this function will
     * perform.
     *
     * To improve the performance, one can increase `c ^ l` by increasing
     * either `c` (the number of possible characters) or `l` (the length of
     * generated paths).
     *
     * @param int    $length
     * @param string $chars
     * @throws \PDOException
     * @return string
     * @see LinksTableAdapter::generateRandomPath
     */
    public function findAvailablePath($length, $chars)
    {
        do {
            $path = $this->generateRandomPath($length, $chars);
        } while($this->resolvePath($path));
        return $path;
    }

    /**
     * Generates a random path.
     *
     * @param int    $length
     * @param string $chars
     * @return string
     */
    public function generateRandomPath($length, $chars)
    {
        $str = '';
        $n = strlen($chars);
        for($i = 0; $i < $length; $i++) {
            $str .= $chars[rand(0, $n - 1)];
        }
        return $str;
    }

    /**
     * Checks whether a path conflicts with other entries.
     *
     * If a conflict is found, the entry is returned.
     *
     * @param string $shortpath
     * @throws \PDOException
     */
    public function checkConflictsStatic($shortpath)
    {
        return $this->resolvePath($shortpath);
    }

    /**
     * Adds a link.
     *
     * @param string $type
     * @param string $path
     * @param string $target
     * @param int    $owner
     * @throws \PDOException
     */
    public function addLink($type, $path, $target, $owner)
    {
        $this->insert("type=?, path=?, target=?, owner_id=$owner", array($type, $path, $target));
        $stmt = $this->select('*', 'id=(SELECT LAST_INSERT_ID())');
        $row = $stmt->fetch(\PDO::FETCH_OBJ);
        $stmt->closeCursor();
        return $row;
    }

    /**
     * Removes a link.
     *
     * @param int $id
     * @throws \PDOException
     */
    public function removeLink($id)
    {
        $stmt = $this->delete("id=$id");
        $nRows = $stmt->rowCount();
        $stmt->closeCursor();
        return $nRows;
    }

    /**
     * Removes all links associated with a user.
     *
     * @param int $owner
     * @throws \PDOException
     */
    public function removeLinksByUser($owner)
    {
        $stmt = $this->delete("owner_id=$owner");
        $nRows = $stmt->rowCount();
        $stmt->closeCursor();
        return $nRows;
    }

    /**
     * Retrieves a link by its id.
     *
     * @param int $id
     * @throws \PDOException
     */
    public function getLink($id)
    {
        $stmt = $this->select('*', "id=$id");
        $row = $stmt->fetch(\PDO::FETCH_OBJ);
        $stmt->closeCursor();
        return $row;
    }

    /**
     * Updates path and target of a link.
     *
     * @param int    $id
     * @param string $path
     * @param string $target
     * @throws \PDOException
     */
    public function updateLink($id, $path, $target)
    {
        $stmt = $this->update('path=?, target=?', array($path, $target), "id=$id");
        $success = $stmt->rowCount();
        $stmt->closeCursor();
        return $success;
    }

    /**
     * Sets the priority of a link.
     *
     * @param int $id
     * @param int $priority
     * @throws \PDOException
     */
    public function setPriority($id, $priority)
    {
        $stmt = $this->update('priority=?', array($priority), "id=$id AND type='regex'");
        $success = $stmt->rowCount();
        $stmt->closeCursor();
        return $success;
    }

    /**
     * Retrieves links from the database.
     *
     * @param int    $offset
     * @param int    $count
     * @param string $orderBy
     * @throws \PDOException
     */
    public function getLinks($offset, $count, $orderBy = 'type ASC, path ASC')
    {
        $stmt = $this->select('*', null, null, $orderBy, "$offset, $count");
        $rows = $stmt->fetchAll(\PDO::FETCH_OBJ);
        $stmt->closeCursor();
        return $rows;
    }

    /**
     * Counts links matching given criteria.
     *
     * @param string $where
     * @param array  $whereArgs
     * @throws \PDOException
     */
    public function countLinks($where = null, $whereArgs = null)
    {
        $stmt = $this->select('COUNT(id)', $where, $whereArgs);
        $count = $stmt->fetchColumn();
        $stmt->closeCursor();
        return $count;
    }

    /**
     * Resolves a path to a matching link.
     *
     * @param string $path
     * @throws \PDOException
     */
    public function resolvePath($path)
    {
        $result = $this->resolvePathStatic($path);
        if($result) {
            return $result;
        }
        $result = $this->resolvePathWildcard($path);
        if($result) {
            return $result;
        }
        return null;
    }

    /**
     * Resolves a path to a static link.
     *
     * @param string $path
     * @throws \PDOException
     */
    public function resolvePathStatic($path)
    {
        $stmt = $this->select('*', "type='static' AND path=?", array($path));
        $row = $stmt->fetch(\PDO::FETCH_OBJ);
        $stmt->closeCursor();

        if($row) {
            $row->resolved = $row->target;
            return $row;
        }

        return null;
    }

    /**
     * Resolves a path to a wildcard.
     *
     * This function pays attention to the priority of wildcards. Links with a
     * higher priority will be checked against the given path before others
     * with a lower priority. The order of links with the same priority is
     * undefined.
     *
     * @param string $path
     * @throws \PDOException
     */
    public function resolvePathWildcard($path)
    {
        $stmt = $this->select('*', "type='regex'", null, 'priority DESC');
        $match = null;
        $success = false;
        while($row = $stmt->fetch(\PDO::FETCH_OBJ)) {
            $pattern = '/^' . $row->path . '$/';
            $replacement = $row->target;
            $target = preg_replace($pattern, $replacement, $path, 1, $success);
            if($success) break;
        }
        $stmt->closeCursor();

        if($success) {
            $row->resolved = $target;
            return $row;
        }

        return null;
    }
}
